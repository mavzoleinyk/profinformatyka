<?php


if ( !class_exists( 'Shoestrap_Branding' ) ) {

	/**
	* The Branding module
	*/
	class Shoestrap_Branding {

		function __construct() {
			add_filter( 'redux/options/' . SHOESTRAP_OPT_NAME . '/sections', array( $this, 'options' ), 20 );
			add_action( 'wp_head',            array( $this, 'icons'            ) );
		}

		/*
		 * The branding core options for the Shoestrap theme
		 */
		function options( $sections ) {
			$fields = array();
			// Branding Options
			$section = array(
				'title' => __( 'Kolorystyka', 'shoestrap' ),
				'icon' => 'el-icon-certificate'
			);

			$fields[] = array(
				'title'       => 'Kolory główne',
				'desc'        => '',
				'id'          => 'help6',
				'default'     => __( 'Przykład zastosowania - http://getbootstrap.com/css/#helper-classes-backgrounds', 'shoestrap' ),
				'type'        => 'info'
			);

			// $fields[] = array(
			// 	'title'       => __( 'Enable Gradients', 'shoestrap' ),
			// 	'desc'        => __( 'Enable gradients for buttons and the navbar. Default: Off.', 'shoestrap' ),
			// 	'id'          => 'gradients_toggle',
			// 	'default'     => 0,
			// 	'compiler'    => true,
			// 	'type'        => 'switch',
			// );

			$fields[] = array(
				'title'       => __( 'Primary', 'shoestrap' ),
				'desc'        => __( 'Ustawienie tego koloru wpłynie na kolor linków, przycisków z klasą .primary, tło niektórych elementów.', 'shoestrap' ),
				'id'          => 'color_brand_primary',
				'default'     => '#428bca',
				'compiler'    => true,
				'transparent' => false,
				'type'        => 'color'
			);

			$fields[] = array(
				'title'       => __( 'Success', 'shoestrap' ),
				'desc'        => __( 'Kolor dla success messages etc. Domyślnie: #5cb85c.', 'shoestrap' ),
				'id'          => 'color_brand_success',
				'default'     => '#5cb85c',
				'compiler'    => true,
				'transparent' => false,
				'type'        => 'color',
			);

			$fields[] = array(
				'title'       => __( 'Brand Colors: Warning', 'shoestrap' ),
				'desc'        => __( 'Kolor dla warning messages etc. Domyślnie: #f0ad4e.', 'shoestrap' ),
				'id'          => 'color_brand_warning',
				'default'     => '#f0ad4e',
				'compiler'    => true,
				'type'        => 'color',
				'transparent' => false,
			);

			$fields[] = array(
				'title'       => __( 'Brand Colors: Danger', 'shoestrap' ),
				'desc'        => __( 'Kolor dla success messages etc. Domyślnie: #d9534f.', 'shoestrap' ),
				'id'          => 'color_brand_danger',
				'default'     => '#d9534f',
				'compiler'    => true,
				'type'        => 'color',
				'transparent' => false,
			);

			$fields[] = array(
				'title'       => __( 'Brand Colors: Info', 'shoestrap' ),
				'desc'        => __( 'Kolor dla info messages etc. Domyślnie: #5bc0de.', 'shoestrap' ),
				'id'          => 'color_brand_info',
				'default'     => '#5bc0de',
				'compiler'    => true,
				'type'        => 'color',
				'transparent' => false,
			);

			$section['fields'] = $fields;

			$section = apply_filters( 'shoestrap_module_branding_options_modifier', $section );

			$sections[] = $section;
			return $sections;
		}

		function icons() {
			global $ss_settings;

			$favicon_item    = $ss_settings['favicon'];
			$apple_icon_item = $ss_settings['apple_icon'];

			// Add the favicon
			if ( ! empty( $favicon_item['url'] ) && $favicon_item['url'] != '' ) {
				$favicon = Shoestrap_Image::_resize( $favicon_item['url'], 32, 32, true, false );

				echo '<link rel="shortcut icon" href="'.$favicon['url'].'" type="image/x-icon" />';
			}

			// Add the apple icons
			if ( ! empty( $apple_icon_item['url'] ) ) {
				$iphone_icon        = Shoestrap_Image::_resize( $apple_icon_item['url'], 57, 57, true, false );
				$iphone_icon_retina = Shoestrap_Image::_resize( $apple_icon_item['url'], 57, 57, true, true );
				$ipad_icon          = Shoestrap_Image::_resize( $apple_icon_item['url'], 72, 72, true, false );
				$ipad_icon_retina   = Shoestrap_Image::_resize( $apple_icon_item['url'], 72, 72, true, true );
				?>

				<!-- For iPhone --><link rel="apple-touch-icon-precomposed" href="<?php echo $iphone_icon['url'] ?>">
				<!-- For iPhone 4 Retina display --><link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $iphone_icon_retina['url'] ?>">
				<!-- For iPad --><link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $ipad_icon['url'] ?>">
				<!-- For iPad Retina display --><link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $ipad_icon_retina['url'] ?>">
				<?php
			}
		}

		/*
		 * The site logo.
		 * If no custom logo is uploaded, use the sitename
		 */
		public static function logo() {
			global $ss_settings;
			$logo  = $ss_settings['logo'];

			if ( ! empty( $logo['url'] ) ) {
				$branding = '<img id="site-logo" src="' . $logo['url'] . '" alt="' . get_bloginfo( 'name' ) . '">';
			} else {
				$branding = '<span class="sitename">' . get_bloginfo( 'name' ) . '</span>';
			}

			return $branding;
		}
	}
}
