<?php


if ( !class_exists( 'Shoestrap_Skrypty' ) ) {

	/**
	* The "Advanced" module
	*/
	class Shoestrap_Skrypty {

		function __construct() {
			global $ss_settings;

			add_filter( 'redux/options/' . SHOESTRAP_OPT_NAME . '/sections', array( $this, 'options' ), 140 );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts'            ), 100 );

		}

		/**
		* Utility function
		*/
		public static function add_filters( $tags, $function ) {
			foreach( $tags as $tag ) {
				add_filter( $tag, $function );
			}
		}

		/**
		 * The advanced core options for the Shoestrap theme
		 */
		function options( $sections ) {

			// Dodatkowe skrypty Settings
			$section = array(
				'title'   => __( 'Dodatkowe skrypty', 'shoestrap' ),
				'icon'    => 'el-icon-puzzle'
			);

			$fields[] = array(
				'title'     => __( 'Wyrównaj wysokość elementów', 'shoestrap' ),
				'desc'      => __( '<p>Zastosowanie:</p><p> &lt;div data-mh="nazwa-grupy">My text&lt;/div></p><p> &lt;div data-mh="nazwa-grupy">Some other text&lt;/div>.</p> <p>Dokumentacja: https://github.com/liabru/jquery-match-height</p>', 'shoestrap' ),
				'id'        => 'equal_heihgt',
				'default'   => 0,
				'type'      => 'switch',
			);
			$fields[] = array(
				'title'     => __( 'WOW.js', 'shoestrap' ),
				'desc'      => __( '<p>Dokumentacja: http://mynameismatthieu.com/WOW/docs.html</p> <p>Efekty: http://daneden.github.io/animate.css/<p> <p>oraz http://www.justinaguilar.com/animations/index.html</p>', 'shoestrap' ),
				'id'        => 'wow_js',
				'default'   => 0,
				'type'      => 'switch',
			);
			$fields[] = array(
				'title'     => __( 'Parallax', 'shoestrap' ),
				'desc'      => __( 'Prosty efekt parallax. Do elementu w którym ma wystąpić efekt parallax należy dodać klasę .parallaxuj, a w cssie zdefiniować tło oraz background-atachment:fixed', 'shoestrap' ),
				'id'        => 'parallax_js',
				'default'   => 0,
				'type'      => 'switch',
			);
			$fields[] = array(
				'title'     => __( 'Font Awesome', 'shoestrap' ),
				'desc'      => __( 'Zbiór Fonto-ikonek :) - lista ikonek - http://fortawesome.github.io/Font-Awesome/icons/ ', 'shoestrap' ),
				'id'        => 'fontawesome_css',
				'default'   => 0,
				'type'      => 'switch',
			);
			$fields[] = array(
				'title'     => __( 'OnePage menu', 'shoestrap' ),
				'desc'      => __( 'Po kliknięciu w element menu, zostaniemy przeniesieni w dół strony, do odpowiedniej sekcji. ', 'shoestrap' ),
				'id'        => 'scroll_to',
				'default'   => 0,
				'type'      => 'switch',
			);		
			$fields[] = array(
				'title'     => __( 'Counter Up', 'shoestrap' ),
				'desc'      => __( 'Animacja zliczająca w górę do określonej liczny. <p>Zastosowanie: &lt;div class="counter-up"&gt;1234&lt;/div&gt;</p> ', 'shoestrap' ),
				'id'        => 'counter_up',
				'default'   => 0,
				'type'      => 'switch',
			);
			$fields[] = array(
				'title'     => __( 'Full Height', 'shoestrap' ),
				'desc'      => __( 'Div z klasą .fullHeight zostanie rozciągnięty do wysokości okna.', 'shoestrap' ),
				'id'        => 'full_height',
				'default'   => 0,
				'type'      => 'switch',
			);	
			$section['fields'] = $fields;

			$section = apply_filters( 'shoestrap_module_advanced_options_modifier', $section );

			$sections[] = $section;

			return $sections;

		}

		/**
		 * Enqueue some extra scripts
		 */
		function scripts() {
			$settings = get_option( SHOESTRAP_OPT_NAME );
			if ( $settings['equal_heihgt'] == 1 ) {
				wp_register_script('matchHeight', get_bloginfo('template_directory') .'/dodatki/equal-height/jquery.matchHeight-min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('matchHeight');
			}
			if ( $settings['wow_js'] == 1 ) {
				wp_register_script('wow', get_bloginfo('template_directory') .'/dodatki/wow/wow.min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('wow');
        		wp_register_script('wowInit', get_bloginfo('template_directory') .'/dodatki/wow/wow-init.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('wowInit');
        		wp_register_style( 'wow', get_bloginfo('template_directory') .'/dodatki/wow/animate.css' );
				wp_enqueue_style( 'wow' );
			}
			if ( $settings['parallax_js'] == 1 ) {
				wp_register_script('parallax', get_bloginfo('template_directory') .'/dodatki/parallax/jquery.parallax.min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('parallax');
        		wp_register_script('parallaxInit', get_bloginfo('template_directory') .'/dodatki/parallax/parallax-init.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('parallaxInit');
			}	
			if ( $settings['fontawesome_css'] == 1 ) {
        		wp_register_style( 'fontAwesome', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css' );
				wp_enqueue_style( 'fontAwesome' );
			}	
			if ( $settings['scroll_to'] == 1 ) {
				wp_register_script('scrollTo', get_bloginfo('template_directory') .'/dodatki/scrollto/jquery.singlePageNav.min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('scrollTo');
				wp_register_script('scrollToInit', get_bloginfo('template_directory') .'/dodatki/scrollto/jquery.singlePageNavInit.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('scrollToInit');   
        	} 
        	if ( $settings['counter_up'] == 1 ) {
				wp_register_script('counterUp', get_bloginfo('template_directory') .'/dodatki/counterUp/jquery.counterup.min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('counterUp');
				wp_register_script('counterUpInit', get_bloginfo('template_directory') .'/dodatki/counterUp/jquery.counterupInit.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('counterUpInit');        		
			}
			if ( $settings['full_height'] == 1 ) {
				wp_register_script('fullHeight', get_bloginfo('template_directory') .'/dodatki/fullHeight.min.js', array('jquery'), '1.0',true);
        		wp_enqueue_script('fullHeight');
			}
			}
		}
	}
