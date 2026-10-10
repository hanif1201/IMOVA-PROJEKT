#!/usr/bin/env bash
# Richtet die lokale WordPress-Instanz für IMOVA ein.
set -euo pipefail
cd "$(dirname "$0")/.."

docker compose up -d db wordpress

wp() { docker compose run --rm -T cli wp "$@"; }

echo "Warte auf WordPress …"
for i in $(seq 1 60); do
  if docker compose run --rm -T cli wp core version >/dev/null 2>&1; then break; fi
  sleep 2
done

if ! wp core is-installed >/dev/null 2>&1; then
  wp core install \
    --url="http://localhost:8080" \
    --title="IMOVA PROJEKT" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="sebastian@imova-projekt.de" \
    --skip-email
fi

wp language core install de_DE --activate || true
wp option update timezone_string "Europe/Berlin"
wp option update date_format "d.m.Y"
wp option update blogdescription "Immobilien & Projektentwicklung in München und Augsburg"
wp rewrite structure "/%postname%/" --hard

# Standardinhalte entfernen
wp post delete 1 2 3 --force >/dev/null 2>&1 || true

# Theme aktivieren (legt Seiten, Menüs und das erste Objekt an)
wp theme activate imova
wp imova setup
wp rewrite flush --hard

echo
echo "Fertig: http://localhost:8080  (Admin: http://localhost:8080/wp-admin  –  admin / admin)"
