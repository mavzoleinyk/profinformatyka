<?php

if ( ! class_exists( 'Shoestrap_Background' ) ) {

	/**
	* The "Background" module
	*/
	class Shoestrap_Background {

		function __construct() {
			add_filter( 'redux/options/' . SHOESTRAP_OPT_NAME . '/sections', array( $this, 'options' ), 30 );
			add_action( 'wp_enqueue_scripts', array( $this, 'css' ), 101 );
			add_action( 'plugins_loaded',     array( $this, 'upgrade_options' ) );
		}

		/*
		 * The background core options for the Shoestrap theme
		 */
		function options( $sections ) {
			global $redux;
			$settings = get_option( SHOESTRAP_OPT_NAME );

			// Blog Options
			$section = array(
				'title' => __( 'Tła', 'shoestrap' ),
				'icon'  => 'el-icon-photo',
			);

			$fields[] = array(
				'title'       => __( ' Główne tło ', 'shoestrap' ),
				'desc'        => __( 'Tło strony przypisane do tagu body. Domyślnie: #ffffff.', 'shoestrap' ),
				'id'          => 'html_bg',
				'default'     => array(
					'background-color' => isset( $settings['html_color_bg'] ) ? $settings['html_color_bg'] : '#ffffff',
				),
				'transparent' => false,
				'type'        => 'background',
				'output'      => 'body'
			);

			$fields[] = array(
				'title'       => __( 'Content Background', 'shoestrap' ),
				'desc'        => __( 'Tło bloku z zawartością strony', 'shoestrap' ),
				'id'          => 'body_bg',
				'default'     => array(
					'background-color'    => isset( $settings['color_body_bg'] ) ? $settings['color_body_bg'] : '#ffffff',
					'background-repeat'   => isset( $settings['background_repeat'] ) ? $settings['background_repeat'] : NULL,
					'background-position' => isset( $settings['background_position_x'] ) ? $settings['background_position_x'] . ' center' : NULL,
					'background-image'    => isset( $settings['background_image']['url'] ) ? $settings['background_image']['url'] : NULL,
				),
				'compiler'    => true,
				'transparent' => false,
				'type'        => 'background',
				'output'      => '.wrap.main-section .content .bg'
			);

			$fields[] = array(
				'title'   => __( 'Content Background Opacity', 'shoestrap' ),
				'desc'    => __( 'Nie działa w przypadku, gdy jako tło ustawimy grafikę. Należy również pamiętać, że w przypadku graficznego tła dla zawartości strony, wartość opacity musi być ustawiona na 100, w przeciwnym wypadku tło w ogóle się nie pojawi. ', 'shoestrap' ),
				'id'      => 'body_bg_opacity',
				'default' => 100,
				'min'     => 0,
				'step'    => 1,
				'max'     => 100,
				'type'    => 'slider',
			);

			$section['fields'] = $fields;

			$section = apply_filters( 'shoestrap_module_background_options_modifier', $section );

			$sections[] = $section;
			return $sections;

		}

		function css() {
			global $ss_settings;

			$content_opacity = $ss_settings['body_bg_opacity'];
			$bg_color        = $ss_settings['body_bg'];

			if ( isset( $bg_color['background-color'] ) ) {
				$bg_color = $bg_color['background-color'];
			} else {
				$bg_color = '#ffffff';
			}

			// Style defaults to null.
			$style = null;

			// The Content background color
			if ( $content_opacity < 100 ) {

				$content_bg = 'background:' . Shoestrap_Color::get_rgba( $bg_color, $content_opacity ) . ';';
				$style = '.wrap.main-section div.content .bg {' . $content_bg . '}';

			}

			wp_add_inline_style( 'shoestrap_css', $style );
		}
	}
}
