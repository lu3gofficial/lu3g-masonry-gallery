#!/bin/sh
# Crea dist/lu3g-masonry-gallery.zip (cartella lu3g-masonry-gallery/ all'interno), il pacchetto che WordPress installa e aggiorna.
# Esclude i file che servono solo allo sviluppo (git, script, automazioni).
set -e
cd "$(dirname "$0")/.."
rm -rf dist && mkdir -p dist/lu3g-masonry-gallery
cp -R lu3g-masonry-gallery.php includes assets dist/lu3g-masonry-gallery/
(cd dist && zip -rq lu3g-masonry-gallery.zip lu3g-masonry-gallery -x '*.DS_Store')
rm -rf dist/lu3g-masonry-gallery
echo "dist/lu3g-masonry-gallery.zip"
