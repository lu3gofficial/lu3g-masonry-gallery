<?php
/**
 * Widget Elementor: LU3G Masonry Gallery.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class LU3G_Masonry_Gallery_Widget extends Widget_Base {

	public function get_name() {
		return 'lu3g_masonry_gallery';
	}

	public function get_title() {
		return 'LU3G Masonry Gallery';
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	public function get_categories() {
		return array( 'general' );
	}

	public function get_keywords() {
		return array( 'masonry', 'gallery', 'repeater', 'jetengine', 'lu3g', 'ambientazioni' );
	}

	public function get_style_depends() {
		return array( 'lu3g-masonry-gallery' );
	}

	public function get_script_depends() {
		return array( 'lu3g-masonry-gallery' );
	}

	/* ------------------------------------------------------------------
	 * CONTROLLI
	 * ------------------------------------------------------------------ */

	protected function register_controls() {

		/* --- Sorgente dati --- */
		$this->start_controls_section( 'section_source', array(
			'label' => 'Sorgente dati',
		) );

		$this->add_control( 'repeater_field', array(
			'label'       => 'Campo repeater (meta key)',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'gallery_ambientazione',
			'label_block' => true,
		) );

		$this->add_control( 'structure', array(
			'label'       => 'Struttura del repeater',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'product',
			'options'     => array(
				'product' => 'Una riga per prodotto (campo Gallery o Media)',
				'photo'   => 'Una riga per foto (campo Media)',
			),
			'description' => 'Una riga per prodotto: nome, link e una o più foto. Funziona sia con il campo Gallery sia con Media.',
		) );

		$this->add_control( 'product_photos', array(
			'label'     => 'Foto per prodotto nel masonry',
			'type'      => Controls_Manager::SELECT,
			'default'   => 'all',
			'options'   => array(
				'all'   => 'Tutte',
				'first' => 'Solo la prima',
			),
			'condition' => array(
				'structure' => 'product',
				'layout'    => 'masonry',
			),
		) );

		$this->add_control( 'image_key', array(
			'label'   => 'Sottocampo foto / gallery',
			'type'    => Controls_Manager::TEXT,
			'default' => 'foto',
		) );

		$this->add_control( 'link_key', array(
			'label'   => 'Sottocampo link',
			'type'    => Controls_Manager::TEXT,
			'default' => 'link',
		) );

		$this->add_control( 'group_key', array(
			'label'       => 'Sottocampo gruppo (solo mosaico)',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => 'Solo per il layout Mosaico: le foto consecutive con lo stesso valore formano un blocco. Vuoto = raggruppamento automatico.',
			'condition'   => array(
				'structure' => 'photo',
				'layout'    => 'mosaic',
			),
		) );

		$this->add_control( 'title_key', array(
			'label'       => 'Sottocampo nome prodotto',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'prodotto',
			'separator'   => 'before',
			'description' => 'Mostrato in hover. Lascia vuoto per non mostrare testi.',
		) );

		$this->add_control( 'subtitle_key', array(
			'label'       => 'Sottocampo sottotitolo',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'collezione',
			'description' => 'Riga piccola sotto al nome (es. collezione o marchio). Facoltativo.',
		) );

		$this->add_control( 'preview_post_id', array(
			'label'       => 'ID post per anteprima editor',
			'type'        => Controls_Manager::NUMBER,
			'description' => 'Usato solo quando il post corrente non ha foto (es. modificando il template). Lascia vuoto se non serve.',
		) );

		$this->add_control( 'link_target', array(
			'label'   => 'Apertura link',
			'type'    => Controls_Manager::SELECT,
			'default' => '_self',
			'options' => array(
				'_self'  => 'Stessa scheda',
				'_blank' => 'Nuova scheda',
			),
		) );

		$this->add_control( 'image_size', array(
			'label'   => 'Dimensione immagine',
			'type'    => Controls_Manager::SELECT,
			'default' => LU3G_MG_IMAGE_SIZE,
			'options' => $this->get_image_size_options(),
		) );

		$this->end_controls_section();

		/* --- Layout --- */
		$this->start_controls_section( 'section_layout', array(
			'label' => 'Layout',
		) );

		$this->add_control( 'layout', array(
			'label'       => 'Tipo di layout',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'masonry',
			'options'     => array(
				'mosaic'  => 'Mosaico',
				'masonry' => 'Masonry a colonne',
			),
			'description' => 'Mosaico: blocchi che si alternano (grande + piccole, poi tre affiancate). Le foto vengono ritagliate per riempire le celle, quindi funziona con qualsiasi formato.',
		) );

		$this->add_control( 'start_side', array(
			'label'     => 'Primo blocco: foto grande a',
			'type'      => Controls_Manager::SELECT,
			'default'   => 'left',
			'options'   => array(
				'left'  => 'Sinistra',
				'right' => 'Destra',
			),
			'condition' => array( 'layout' => 'mosaic' ),
		) );

		$this->add_control( 'trio_ratio', array(
			'label'       => 'Forma delle foto nel blocco da tre',
			'type'        => Controls_Manager::SELECT,
			'default'     => '0.75',
			'options'     => array(
				'0.75' => 'Verticali (3:4)',
				'1'    => 'Quadrate',
				'1.33' => 'Orizzontali (4:3)',
			),
			'condition'   => array( 'layout' => 'mosaic' ),
		) );

		$this->add_responsive_control( 'columns', array(
			'label'          => 'Colonne',
			'type'           => Controls_Manager::NUMBER,
			'min'            => 1,
			'max'            => 6,
			'default'        => 3,
			'tablet_default' => 2,
			'mobile_default' => 1,
			'condition'      => array( 'layout' => 'masonry' ),
			'selectors'      => array(
				'{{WRAPPER}} .lmg' => '--lmg-cols: {{VALUE}};',
			),
		) );

		$this->add_responsive_control( 'gap', array(
			'label'          => 'Spaziatura',
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => array( 'px' ),
			'range'          => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
			'default'        => array( 'size' => 16, 'unit' => 'px' ),
			'tablet_default' => array( 'size' => 12, 'unit' => 'px' ),
			'mobile_default' => array( 'size' => 10, 'unit' => 'px' ),
			'selectors'      => array(
				'{{WRAPPER}} .lmg' => '--lmg-gap: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->add_control( 'ratio_mode', array(
			'label'       => 'Altezze delle foto',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'rhythm',
			'options'     => array(
				'rhythm'  => 'Ritmo alternato (consigliato)',
				'natural' => 'Proporzioni reali delle foto',
			),
			'description' => 'Ritmo alternato: le foto ricevono altezze diverse a rotazione e vengono ritagliate al centro, così l\'effetto masonry si vede anche se le foto hanno tutte lo stesso formato.',
			'condition'   => array( 'layout' => 'masonry' ),
		) );

		$this->add_control( 'ratio_heading', array(
			'label'     => 'Limiti proporzioni',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => array( 'layout' => 'masonry', 'ratio_mode' => 'natural' ),
		) );

		$this->add_control( 'ratio_note', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => 'Rapporto larghezza/altezza. Le foto fuori da questi limiti vengono ritagliate al centro, così panoramiche e verticali estreme non rovinano il masonry. Es: 0.75 = 3:4 verticale, 1.5 = 3:2 orizzontale.',
			'content_classes' => 'elementor-descriptor',
			'condition' => array( 'layout' => 'masonry', 'ratio_mode' => 'natural' ),
		) );

		$this->add_control( 'min_ratio', array(
			'label'   => 'Più verticale ammessa',
			'type'    => Controls_Manager::NUMBER,
			'min'     => 0.3,
			'max'     => 1,
			'step'    => 0.05,
			'default' => 0.7,
			'condition' => array( 'layout' => 'masonry', 'ratio_mode' => 'natural' ),
		) );

		$this->add_control( 'max_ratio', array(
			'label'   => 'Più orizzontale ammessa',
			'type'    => Controls_Manager::NUMBER,
			'min'     => 1,
			'max'     => 3,
			'step'    => 0.05,
			'default' => 1.6,
			'condition' => array( 'layout' => 'masonry', 'ratio_mode' => 'natural' ),
		) );

		$this->end_controls_section();

		/* --- Animazioni --- */
		$this->start_controls_section( 'section_animation', array(
			'label' => 'Animazioni',
		) );

		$this->add_control( 'entrance', array(
			'label'   => 'Animazione di ingresso',
			'type'    => Controls_Manager::SELECT,
			'default' => 'fade-up',
			'options' => array(
				'none'    => 'Nessuna',
				'fade'    => 'Dissolvenza',
				'fade-up' => 'Dissolvenza dal basso',
				'zoom'    => 'Zoom leggero',
			),
		) );

		$this->add_control( 'duration', array(
			'label'     => 'Durata (ms)',
			'type'      => Controls_Manager::NUMBER,
			'min'       => 100,
			'max'       => 2000,
			'step'      => 50,
			'default'   => 700,
			'condition' => array( 'entrance!' => 'none' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-duration: {{VALUE}}ms;',
			),
		) );

		$this->add_control( 'stagger', array(
			'label'     => 'Ritardo tra le foto (ms)',
			'type'      => Controls_Manager::NUMBER,
			'min'       => 0,
			'max'       => 400,
			'step'      => 10,
			'default'   => 90,
			'condition' => array( 'entrance!' => 'none' ),
		) );

		$this->add_control( 'hover_zoom', array(
			'label'        => 'Zoom in hover',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'separator'    => 'before',
			'return_value' => 'yes',
		) );

		$this->add_control( 'hover_scale', array(
			'label'     => 'Intensità zoom',
			'type'      => Controls_Manager::SLIDER,
			'range'     => array( 'px' => array( 'min' => 1, 'max' => 1.2, 'step' => 0.01 ) ),
			'default'   => array( 'size' => 1.06 ),
			'condition' => array( 'hover_zoom' => 'yes' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-scale: {{SIZE}};',
			),
		) );

		$this->add_control( 'hover_style', array(
			'label'     => 'Stile hover',
			'type'      => Controls_Manager::SELECT,
			'default'   => 'frame',
			'separator' => 'before',
			'options'   => array(
				'frame'   => 'Cornice',
				'minimal' => 'Minimal',
				'label'   => 'Etichetta',
				'veil'    => 'Velo pieno',
				'corners' => 'Angoli',
				'bw'      => 'Bianco e nero → colore',
			),
		) );

		$this->add_control( 'show_frame', array(
			'label'        => 'Cornice in hover',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'condition'    => array( 'hover_style' => 'frame' ),
		) );

		$this->add_control( 'show_arrow', array(
			'label'        => 'Freccia in hover',
			'type'         => Controls_Manager::SWITCHER,
			'default'      => 'yes',
			'return_value' => 'yes',
			'description'  => 'Mostrata solo sulle foto con link.',
		) );

		$this->end_controls_section();

		/* --- Stile --- */
		$this->start_controls_section( 'section_style', array(
			'label' => 'Foto',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'radius', array(
			'label'      => 'Arrotondamento',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 0, 'unit' => 'px' ),
			'selectors'  => array(
				'{{WRAPPER}} .lmg' => '--lmg-radius: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->add_control( 'placeholder_color', array(
			'label'     => 'Sfondo durante il caricamento',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#efebe6',
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-placeholder: {{VALUE}};',
			),
		) );

		$this->add_control( 'overlay_color', array(
			'label'     => 'Overlay in hover',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.08)',
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-overlay: {{VALUE}};',
			),
		) );

		$this->add_control( 'arrow_color', array(
			'label'     => 'Colore freccia',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'condition' => array( 'show_arrow' => 'yes' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-arrow: {{VALUE}};',
			),
		) );

		$this->add_control( 'frame_color', array(
			'label'     => 'Colore cornice / angoli',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.85)',
			'separator' => 'before',
			'condition' => array( 'hover_style' => array( 'frame', 'corners' ) ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-frame: {{VALUE}};',
			),
		) );

		$this->add_control( 'frame_inset', array(
			'label'      => 'Distanza dal bordo',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 14, 'unit' => 'px' ),
			'description' => 'Distanza di cornice, angoli, etichetta e testi dal bordo della foto.',
			'selectors'  => array(
				'{{WRAPPER}} .lmg' => '--lmg-frame-inset: {{SIZE}}{{UNIT}};',
			),
		) );

		$this->add_control( 'veil_color', array(
			'label'     => 'Colore velo',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(35, 32, 28, 0.62)',
			'condition' => array( 'hover_style' => 'veil' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-veil: {{VALUE}};',
			),
		) );

		$this->add_control( 'label_bg', array(
			'label'     => 'Sfondo etichetta',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'condition' => array( 'hover_style' => 'label' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-label-bg: {{VALUE}};',
			),
		) );

		$this->add_control( 'label_text', array(
			'label'     => 'Testo etichetta',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1d1d1b',
			'condition' => array( 'hover_style' => 'label' ),
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-label-text: {{VALUE}};',
			),
		) );

		$this->end_controls_section();

		/* --- Testi in hover --- */
		$this->start_controls_section( 'section_style_text', array(
			'label' => 'Testi in hover',
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'text_color', array(
			'label'     => 'Colore testi',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => array(
				'{{WRAPPER}} .lmg' => '--lmg-text: {{VALUE}};',
			),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'title_typography',
			'label'    => 'Nome prodotto',
			'selector' => '{{WRAPPER}} .lmg__title',
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'subtitle_typography',
			'label'    => 'Sottotitolo',
			'selector' => '{{WRAPPER}} .lmg__subtitle',
		) );

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------------
	 * DATI
	 * ------------------------------------------------------------------ */

	private function get_image_size_options() {
		$options = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = $size;
		}
		$options[ LU3G_MG_IMAGE_SIZE ] = 'LU3G Masonry (1000px)';
		$options['full']               = 'Originale (sconsigliato)';
		return $options;
	}

	private function is_editor() {
		return class_exists( '\Elementor\Plugin' )
			&& ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() );
	}

	/**
	 * Converte il valore di un campo Media o Gallery JetEngine in una lista di ID allegato.
	 * Gestisce: ID, "12,34,56", URL, array di ID/URL, array di { id, url }.
	 */
	private function resolve_attachment_ids( $value ) {
		$ids = array();

		if ( is_array( $value ) ) {
			if ( isset( $value['id'] ) || isset( $value['url'] ) ) {
				$ids[] = ! empty( $value['id'] ) ? (int) $value['id'] : (int) attachment_url_to_postid( (string) $value['url'] );
			} else {
				foreach ( $value as $item ) {
					$ids = array_merge( $ids, $this->resolve_attachment_ids( $item ) );
				}
			}
		} elseif ( is_numeric( $value ) ) {
			$ids[] = (int) $value;
		} elseif ( is_string( $value ) && '' !== trim( $value ) ) {
			if ( false !== strpos( $value, ',' ) ) {
				foreach ( explode( ',', $value ) as $part ) {
					$ids = array_merge( $ids, $this->resolve_attachment_ids( trim( $part ) ) );
				}
			} else {
				$ids[] = (int) attachment_url_to_postid( $value );
			}
		}

		return array_values( array_filter( $ids ) );
	}

	/**
	 * Legge il repeater dal post e restituisce [ [id, link], ... ] solo con immagini valide.
	 */
	private function get_rows( $post_id, $settings ) {
		$raw = get_post_meta( $post_id, $settings['repeater_field'], true );

		if ( is_string( $raw ) && '' !== $raw ) {
			$decoded = json_decode( $raw, true );
			$raw     = is_array( $decoded ) ? $decoded : maybe_unserialize( $raw );
		}

		if ( ! is_array( $raw ) ) {
			return array();
		}

		$rows       = array();
		$by_product = 'photo' !== $settings['structure'];
		$n          = 0;

		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$n++;

			$ids = $this->resolve_attachment_ids( isset( $row[ $settings['image_key'] ] ) ? $row[ $settings['image_key'] ] : '' );

			if ( $by_product && 'masonry' === $settings['layout'] && 'first' === $settings['product_photos'] ) {
				$ids = array_slice( $ids, 0, 1 );
			}
			$link     = isset( $row[ $settings['link_key'] ] ) ? trim( (string) $row[ $settings['link_key'] ] ) : '';
			$title    = ! empty( $settings['title_key'] ) && isset( $row[ $settings['title_key'] ] ) ? trim( wp_strip_all_tags( (string) $row[ $settings['title_key'] ] ) ) : '';
			$subtitle = ! empty( $settings['subtitle_key'] ) && isset( $row[ $settings['subtitle_key'] ] ) ? trim( wp_strip_all_tags( (string) $row[ $settings['subtitle_key'] ] ) ) : '';

			if ( $by_product ) {
				// Ogni riga del repeater è un prodotto: tutte le sue foto nello stesso blocco.
				$group = 'riga-' . $n;
			} else {
				$group = ! empty( $settings['group_key'] ) && isset( $row[ $settings['group_key'] ] ) ? trim( (string) $row[ $settings['group_key'] ] ) : '';
			}

			foreach ( $ids as $id ) {
				if ( ! wp_attachment_is_image( $id ) ) {
					continue;
				}
				$rows[] = array(
					'id'       => $id,
					'link'     => $link,
					'group'    => $group,
					'title'    => $title,
					'subtitle' => $subtitle,
				);
			}
		}

		return $rows;
	}

	/* ------------------------------------------------------------------
	 * RENDER
	 * ------------------------------------------------------------------ */

	protected function render() {
		$settings = $this->get_settings_for_display();
		$post_id  = get_the_ID();
		$rows     = $post_id ? $this->get_rows( $post_id, $settings ) : array();

		// Anteprima nell'editor del template.
		if ( empty( $rows ) && $this->is_editor() ) {
			if ( ! empty( $settings['preview_post_id'] ) ) {
				$post_id = (int) $settings['preview_post_id'];
			} else {
				// Nessun ID impostato: prendi l'ultimo post che ha il repeater compilato.
				$found = get_posts( array(
					'post_type'      => 'any',
					'post_status'    => array( 'publish', 'draft', 'private' ),
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => $settings['repeater_field'], // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_compare'   => 'EXISTS',
				) );
				$post_id = $found ? (int) $found[0] : $post_id;
			}
			$rows = $this->get_rows( $post_id, $settings );
		}

		if ( empty( $rows ) ) {
			if ( $this->is_editor() ) {
				printf(
					'<div class="lmg-empty">LU3G Masonry Gallery: nessuna foto nel campo <code>%s</code> del post #%d. Controlla i nomi dei campi o imposta un "ID post per anteprima".</div>',
					esc_html( $settings['repeater_field'] ),
					(int) $post_id
				);
			}
			return;
		}

		$mosaic = 'masonry' !== $settings['layout'];
		$ctx    = array(
			'post_id'   => $post_id,
			'size'      => $settings['image_size'] ? $settings['image_size'] : LU3G_MG_IMAGE_SIZE,
			'target'    => '_blank' === $settings['link_target'] ? '_blank' : '_self',
			'arrow'     => 'yes' === $settings['show_arrow'],
			'captions'  => ! empty( $settings['title_key'] ),
			// Sequenza di proporzioni (larghezza/altezza): pensata per 2-4 colonne, non si ripete in modo evidente.
			'rhythm'    => 'natural' === $settings['ratio_mode'] ? array() : array( 0.75, 1.1, 0.68, 0.95, 1.3, 0.8, 1.05, 0.7 ),
			'min_ratio' => max( 0.2, (float) $settings['min_ratio'] ),
			'max_ratio' => max( (float) $settings['min_ratio'], (float) $settings['max_ratio'] ),
		);

		if ( $mosaic ) {
			// Il blocco più largo è la foto grande (metà larghezza): basta mezza pagina su desktop.
			$ctx['sizes'] = '(max-width: 767px) 100vw, 50vw';
		} else {
			$cols_d       = max( 1, (int) ( $settings['columns'] ?: 3 ) );
			$cols_t       = max( 1, (int) ( ! empty( $settings['columns_tablet'] ) ? $settings['columns_tablet'] : 2 ) );
			$cols_m       = max( 1, (int) ( ! empty( $settings['columns_mobile'] ) ? $settings['columns_mobile'] : 1 ) );
			$ctx['sizes'] = sprintf(
				'(max-width: 767px) %dvw, (max-width: 1024px) %dvw, %dvw',
				(int) ceil( 100 / $cols_m ),
				(int) ceil( 100 / $cols_t ),
				(int) ceil( 100 / $cols_d )
			);
		}

		$classes = array(
			'lmg',
			$mosaic ? 'lmg--mosaic' : 'lmg--masonry',
			'lmg--anim-' . sanitize_html_class( $settings['entrance'] ),
		);
		if ( 'yes' === $settings['hover_zoom'] ) {
			$classes[] = 'lmg--hover-zoom';
		}
		$hover_style = ! empty( $settings['hover_style'] ) ? $settings['hover_style'] : 'frame';
		$classes[]   = 'lmg--hs-' . sanitize_html_class( $hover_style );
		if ( 'frame' === $hover_style && 'yes' !== $settings['show_frame'] ) {
			$classes[] = 'lmg--no-frame';
		}

		$style = $mosaic ? sprintf( ' style="--lmg-trio-ratio:%s"', esc_attr( (float) $settings['trio_ratio'] ?: 0.75 ) ) : '';

		printf(
			'<div class="%s" data-stagger="%d"%s>',
			esc_attr( implode( ' ', $classes ) ),
			(int) $settings['stagger'],
			$style // phpcs:ignore WordPress.Security.EscapeOutput
		);

		if ( $mosaic ) {
			foreach ( $this->build_blocks( $rows, 'right' === $settings['start_side'] ) as $block ) {
				printf( '<div class="lmg-block lmg-block--%s">', esc_attr( $block['type'] ) );
				foreach ( array_slice( $rows, $block['start'], $block['count'] ) as $row ) {
					$this->render_item( $row, $ctx, false );
				}
				echo '</div>';
			}
		} else {
			foreach ( $rows as $i => $row ) {
				$this->render_item( $row, $ctx, true, $i );
			}
		}

		echo '</div>';
	}

	/**
	 * Divide le foto in blocchi del mosaico.
	 *
	 * Con il campo "look" compilato: ogni gruppo di foto consecutive con lo stesso valore è un look,
	 * e la forma del blocco dipende da quante foto ha (4 = grande + 3, 3 = affiancate, 2, 1).
	 * Senza campo look: sequenza automatica grande + 3 → tre affiancate → grande + 3 specchiato → …
	 * I blocchi con la foto grande alternano sempre sinistra e destra.
	 */
	private function build_blocks( $rows, $start_right ) {
		$total     = count( $rows );
		$has_group = false;
		foreach ( $rows as $row ) {
			if ( '' !== $row['group'] ) {
				$has_group = true;
				break;
			}
		}

		// Calcola le dimensioni dei gruppi.
		$groups = array();
		if ( $has_group ) {
			$current = null;
			foreach ( $rows as $i => $row ) {
				// Una foto con il campo vuoto resta nel look precedente.
				$key = '' !== $row['group'] ? $row['group'] : $current;
				if ( 0 === $i || $key !== $current ) {
					$groups[] = 0;
				}
				$groups[ count( $groups ) - 1 ]++;
				$current = $key;
			}
		} else {
			$index = 0;
			$step  = 0;
			while ( $index < $total ) {
				$size     = min( 0 === $step % 2 ? 4 : 3, $total - $index );
				$groups[] = $size;
				$index   += $size;
				$step++;
			}
		}

		// Trasforma ogni gruppo in uno o più blocchi.
		$blocks = array();
		$index  = 0;
		$right  = $start_right;

		foreach ( $groups as $count ) {
			while ( $count > 0 ) {
				$size = $count >= 4 ? 4 : $count;
				if ( 4 === $size ) {
					$type  = $right ? 'a-r' : 'a';
					$right = ! $right;
				} elseif ( 3 === $size ) {
					$type = 'b';
				} elseif ( 2 === $size ) {
					$type = 'duo';
				} else {
					$type = 'solo';
				}

				$blocks[] = array(
					'type'  => $type,
					'start' => $index,
					'count' => $size,
				);

				$index += $size;
				$count -= $size;
			}
		}

		return $blocks;
	}

	/**
	 * Stampa una singola foto (link o div).
	 */
	private function render_item( $row, $ctx, $use_ratio, $index = 0 ) {
		$meta = wp_get_attachment_metadata( $row['id'] );
		$w    = ! empty( $meta['width'] ) ? (int) $meta['width'] : 1;
		$h    = ! empty( $meta['height'] ) ? (int) $meta['height'] : 1;

		if ( ! empty( $ctx['rhythm'] ) ) {
			$ratio = $ctx['rhythm'][ $index % count( $ctx['rhythm'] ) ];
		} else {
			$ratio = min( $ctx['max_ratio'], max( $ctx['min_ratio'], $w / max( 1, $h ) ) );
		}

		$alt = get_post_meta( $row['id'], '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = '' !== $row['title'] ? $row['title'] : get_the_title( $ctx['post_id'] );
		}

		$img = wp_get_attachment_image( $row['id'], $ctx['size'], false, array(
			'class'    => 'lmg__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'sizes'    => $ctx['sizes'],
			'alt'      => $alt,
		) );

		$has_hover = $row['link'] || ( $ctx['captions'] && '' !== $row['title'] );

		$attrs = sprintf(
			'class="lmg__item%s%s"%s data-orig-w="%d"',
			$row['link'] ? ' lmg__item--link' : '',
			$has_hover ? ' lmg__item--hover' : '',
			$use_ratio ? sprintf( ' style="--lmg-ratio:%1$s" data-ratio="%1$s"', esc_attr( round( $ratio, 4 ) ) ) : '',
			$w
		);

		if ( $row['link'] ) {
			printf(
				'<a %s href="%s" target="%s"%s data-elementor-open-lightbox="no">',
				$attrs, // phpcs:ignore WordPress.Security.EscapeOutput -- valori già escapati sopra.
				esc_url( $row['link'] ),
				esc_attr( $ctx['target'] ),
				'_blank' === $ctx['target'] ? ' rel="noopener"' : ''
			);
		} else {
			printf( '<div %s>', $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput
		}

		echo '<span class="lmg__media">' . $img . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( $has_hover ) {
			echo '<span class="lmg__frame" aria-hidden="true"><i></i><i></i><i></i><i></i></span>';
		}

		$show_title = $ctx['captions'] && '' !== $row['title'];
		$show_arrow = $row['link'] && $ctx['arrow'];

		if ( $show_title || $show_arrow ) {
			echo '<span class="lmg__caption">';
			if ( $show_title ) {
				echo '<span class="lmg__text">';
				echo '<span class="lmg__title">' . esc_html( $row['title'] ) . '</span>';
				if ( '' !== $row['subtitle'] ) {
					echo '<span class="lmg__subtitle">' . esc_html( $row['subtitle'] ) . '</span>';
				}
				echo '</span>';
			}
			if ( $show_arrow ) {
				echo '<span class="lmg__arrow" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
			}
			echo '</span>';
		}

		echo $row['link'] ? '</a>' : '</div>';
	}
}
