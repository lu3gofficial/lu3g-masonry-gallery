# LU3G Masonry Gallery

Widget Elementor che trasforma un campo repeater JetEngine (foto + link) in una gallery masonry animata. Al clic ogni foto porta al suo link, senza lightbox.

## Requisiti
- WordPress 6.0+, PHP 7.4+
- Elementor
- JetEngine (o qualsiasi repeater salvato come post meta con lo stesso formato)

## Setup del CPT (JetEngine)
- Post type **Ambientazioni**: un post per ambiente (Cucine, Bagno, Camera…)
- Repeater `gallery_ambientazione`, **una riga per prodotto**:
  - `foto` → **Gallery** (Value format: Media ID). Oggi una foto, domani anche più di una senza cambiare nulla. Funziona anche con un campo Media.
  - `link` → Text: link del prodotto, usato da tutte le sue foto
  - `prodotto` → Text: nome mostrato in hover (es. "May Tree")
  - `collezione` → Text (facoltativo): riga piccola sotto al nome (es. "Fairhaven WP")
- Nel template Single del CPT, aggiungi il widget **LU3G Masonry Gallery**

I nomi dei campi si possono cambiare dai controlli del widget.

## Layout
- **Masonry (default)**: ogni foto va sotto la colonna più corta. Con "Altezze delle foto" su **Ritmo alternato** (default) le foto ricevono altezze diverse a rotazione e vengono ritagliate al centro, così l'effetto masonry si vede anche se le foto hanno tutte lo stesso formato. Con **Proporzioni reali** ogni foto mantiene le sue proporzioni (entro i limiti impostati). Con più foto per prodotto puoi mostrarle tutte o solo la prima ("Foto per prodotto nel masonry").
- **Mosaico**: blocchi grande + piccole / tre affiancate. Utile quando ci sono più foto per prodotto (campo Gallery, "Una riga per prodotto").

## Hover
Sei stili, scelti da "Stile hover" nella sezione Animazioni:
1. **Cornice**: velatura dal basso, cornice interna, nome e freccia.
2. **Minimal**: velatura dal basso, nome e freccia, senza cornice.
3. **Etichetta**: riquadro (colore regolabile) che sale dal basso con nome e freccia.
4. **Velo pieno**: colore su tutta la foto, nome e freccia al centro.
5. **Angoli**: quattro angoli sottili che si chiudono verso la foto.
6. **Bianco e nero → colore**: foto in bianco e nero che tornano a colori.

Su dispositivi touch tutti gli stili mostrano nome e freccia in basso, sempre visibili. Colori, distanza dal bordo e tipografia si regolano dal tab Stile.

## Come gestisce le foto
- Le proporzioni arrivano dai metadati dell'immagine, quindi il layout è pronto subito, senza salti.
- Le foto oltre i limiti di proporzione (default 0.7 – 1.6) vengono ritagliate al centro.
- Usa la dimensione `lu3g_masonry` (1000px, senza ritaglio) con `srcset` e lazy load. Per le foto caricate prima dell'attivazione del plugin, rigenera le miniature (es. plugin "Regenerate Thumbnails").
- Nell'editor Elementor le foto con risoluzione troppo bassa mostrano un badge rosso.
- Le foto senza link non sono cliccabili.

## Aggiornamenti
Aggiornamento automatico da GitHub (`violix96/lu3g-masonry-gallery`) tramite le Release.
