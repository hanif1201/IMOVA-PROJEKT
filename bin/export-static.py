#!/usr/bin/env python3
"""Exportiert die lokale WordPress-Seite als statische Vorschau (z. B. für Vercel).

Aufruf: python3 bin/export-static.py [Zielordner]   (Standard: preview/)
Formulare werden deaktiviert und ein Vorschau-Hinweis eingeblendet.
"""
import os, re, sys, shutil, urllib.request, urllib.parse
from collections import deque

BASE = 'http://localhost:8080'
OUT = sys.argv[1] if len(sys.argv) > 1 else 'preview'
SKIP = ('/wp-admin', '/wp-json', '/wp-login', '/xmlrpc', '/feed', '/comments/feed', '/wp-cron', '/?')

BANNER = (
    '<div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:#a8834f;color:#fff;'
    'font:500 13px/1.4 Inter,system-ui,sans-serif;text-align:center;padding:7px 12px">'
    'Vorschau zur Abstimmung – Formulare und Filter sind in dieser Version noch nicht aktiv.</div>'
    '<style>body{padding-top:33px}.site-header{top:33px!important}</style>'
)
FORM_JS = (
    "<script>document.addEventListener('submit',function(e){var f=e.target;"
    "if(f.matches('[data-imova-form]')||f.closest('.filter-bar')){e.preventDefault();e.stopImmediatePropagation();"
    "alert('Vorschau: Das Absenden ist in dieser Version deaktiviert.');}},true);"
    "document.querySelectorAll('[data-autosubmit]').forEach(function(s){s.removeAttribute('data-autosubmit');"
    "s.addEventListener('change',function(){alert('Vorschau: Filter sind in dieser Version noch nicht aktiv.');});});</script>"
)

def local_path(path):
    path = urllib.parse.unquote(path.split('#')[0].split('?')[0])
    if path.endswith('/') or '.' not in os.path.basename(path):
        path = path.rstrip('/') + '/index.html'
    return os.path.join(OUT, path.lstrip('/'))

def fetch(url):
    try:
        with urllib.request.urlopen(url) as r:
            return r.status, r.headers.get('Content-Type', ''), r.read()
    except urllib.error.HTTPError as e:
        return e.code, e.headers.get('Content-Type', ''), e.read()

def links(text):
    found = set()
    for m in re.finditer(r'''(?:href|src|data-src)=["']([^"']+)["']''', text):
        found.add(m.group(1))
    for m in re.finditer(r'srcset=["\']([^"\']+)["\']', text):
        found.update(p.strip().split(' ')[0] for p in m.group(1).split(','))
    for m in re.finditer(r'url\(["\']?([^)"\']+)["\']?\)', text):
        found.add(m.group(1))
    return found

if os.path.isdir(OUT):
    shutil.rmtree(OUT)
seen, queue = set(), deque(['/', '/404-vorschau/'])
while queue:
    path = queue.popleft()
    if path in seen:
        continue
    seen.add(path)
    status, ctype, body = fetch(BASE + path)
    if status >= 400 and path != '/404-vorschau/':
        print('skip', status, path)
        continue
    target = os.path.join(OUT, '404.html') if path == '/404-vorschau/' else local_path(path)
    if 'text/html' in ctype or 'text/css' in ctype:
        text = body.decode('utf-8')
        for link in links(text):
            if re.search(r'\s', link) or link.startswith(('#', 'mailto:', 'tel:', 'data:', 'javascript:')):
                continue
            absolute = urllib.parse.urljoin(BASE + path, link.replace('&#038;', '&'))
            if not absolute.startswith(BASE):
                continue
            p = absolute[len(BASE):].split('#')[0]
            if not p or '/feed' in p or any(p.startswith(s) for s in SKIP) or '?' in p.split('/')[-1] and not re.search(r'\.\w+\?ver=', p):
                continue
            queue.append(p.split('?')[0])
        text = text.replace(BASE, '').replace(BASE.replace('/', '\\/'), '')
        if 'text/html' in ctype:
            text = re.sub(r'<link[^>]+(wp-json|xmlrpc|EditURI|wlwmanifest|feed)[^>]*>\s*', '', text)
            text = text.replace('</body>', BANNER + FORM_JS + '</body>', 1)
            text = text.replace('<head>', '<head><meta name="robots" content="noindex,nofollow">', 1)
        body = text.encode('utf-8')
    os.makedirs(os.path.dirname(target), exist_ok=True)
    with open(target, 'wb') as f:
        f.write(body)

with open(os.path.join(OUT, 'vercel.json'), 'w') as f:
    f.write('{\n  "cleanUrls": false,\n  "trailingSlash": true,\n  "headers": [{ "source": "/(.*)", "headers": [{ "key": "X-Robots-Tag", "value": "noindex, nofollow" }] }]\n}\n')
with open(os.path.join(OUT, 'robots.txt'), 'w') as f:
    f.write('User-agent: *\nDisallow: /\n')
pages = sum(1 for p in seen if not os.path.splitext(p.rstrip('/'))[1])
print(f'Fertig: {len(seen)} Dateien ({pages} Seiten) in {OUT}/')
