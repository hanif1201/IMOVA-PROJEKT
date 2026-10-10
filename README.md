# IMOVA PROJEKT – Website

Individuelles WordPress-Theme für die IMOVA PROJEKT GmbH (Immobilien & Projektentwicklung, München).

## Lokal starten

Voraussetzung: Docker Desktop.

```bash
./bin/setup.sh
```

- Website: http://localhost:8080
- Backend: http://localhost:8080/wp-admin – Benutzer `admin` / Passwort `admin` (nur lokal!)

Das Theme liegt in `wp-content/themes/imova` und ist in den Container eingebunden – Änderungen sind sofort sichtbar.

## Was Sebastian im Backend pflegen kann

| Bereich | Wo |
|---|---|
| Immobilien anlegen/bearbeiten, Status (Verfügbar / Demnächst / Reserviert / Verkauft), Galerie, Exposé-PDF, Energieausweis | **Immobilien** |
| Standorte für den Filter | **Immobilien → Standorte** |
| Anfragen (Kontakt, Interesse, Tippgeber, Verkauf) inkl. hochgeladener Unterlagen, Bearbeitungsstand, interne Notiz | **Anfragen** (Zähler für neue Anfragen am Menüpunkt, Widget im Dashboard) |
| Texte & Bilder jeder Seite (Startseite, Über uns inkl. Hunde, Tippgeber, Verkaufen, Kontakt) | **Seiten → Seite bearbeiten → Box „Seiteninhalte“** |
| Gründer-Geschichte, Impressum, Datenschutz | normaler Seiteninhalt (Editor) |
| **Logo** (dunkle Version für den Header, helle Version für Footer & Platzhalterbilder), Firmendaten, Telefon, E-Mail, Benachrichtigungs-E-Mail, Provisionssatz (3,57 %) | **Einstellungen → IMOVA** |
| Vorteile-Leiste unter dem Hero, Finanzierungspunkte | **Seiten → Startseite → Seiteninhalte** |
| Fotos von Sebastian & den Hunden austauschen | **Seiten → Über uns → Seiteninhalte → Bild wählen** |
| Menüs | **Design → Menüs** |

## Technische Hinweise

- **Datenschutz:** Schriften lokal (kein Google Fonts), keine Tracking-Tools. Google Maps lädt erst nach Einwilligung (Banner oder Klick auf der Karte).
- **Formulare:** ohne Plugin. Spam-Schutz über Honeypot, signierten Zeitstempel und Rate-Limit (funktioniert auch mit Seiten-Cache). Uploads liegen in `wp-content/uploads/imova-privat/` (per `.htaccess` gesperrt, Download nur im Backend). Auf Nginx-Hosting muss dieser Ordner zusätzlich in der Server-Konfiguration gesperrt werden.
- **E-Mail-Versand:** Auf dem Live-Server ein SMTP-Plugin einrichten (z. B. WP Mail SMTP), damit Benachrichtigungen zuverlässig ankommen. Anfragen werden unabhängig davon immer im Backend gespeichert.
- **Beispielobjekte:** Drei mit „(Beispiel)“ markierte Objekte und Platzhalterbilder (Unsplash) zeigen das Design. Vor dem Livegang über den Hinweis im Backend mit einem Klick löschen.

## Vor dem Livegang offen

- [ ] Logo-Dateien (PNG, transparent, eng beschnitten; dunkle + helle Version) → Einstellungen → IMOVA. SVG wird von WordPress standardmäßig nicht akzeptiert.
- [ ] Favicon → Design → Customizer → Website-Informationen → Website-Icon
- [ ] Foto Sebastian + Geschichte („Über uns“, Platzhalter markiert mit [Platzhalter])
- [ ] Fotos & Namen der beiden Hunde
- [ ] Tippgeber-Konditionen (Seite „Tippgeber“ → Seiteninhalte → Konditionen)
- [ ] Impressum: USt-IdNr., ggf. § 34c GewO / Aufsichtsbehörde, Bildnachweise
- [ ] Datenschutz: Hosting-Anbieter eintragen, rechtlich prüfen lassen
- [ ] Augsburg-Objekt: Fotos, Fläche, Preis, Energieausweis, sobald verfügbar
- [ ] Eigene Bilder statt Platzhalter (Hero, Philosophie, Tippgeber, Verkaufen)
- [ ] Domain/Hosting, SMTP, Admin-Passwort, `WP_DEBUG` aus
