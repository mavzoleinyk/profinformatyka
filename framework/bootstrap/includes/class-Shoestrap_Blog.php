<?php


if ( ! class_exists( 'Shoestrap_Blog' ) ) {

	/**
	* The "Blog" module
	*/
	class Shoestrap_Blog {

		function __construct() {

			global $ss_settings;

			add_filter( 'redux/options/' . SHOESTRAP_OPT_NAME . '/sections', array( $this, 'options' ), 70 );

			if ( ! class_exists( 'BuddyPress' ) || ( class_exists( 'BuddyPress' ) && ! shoestrap_is_bp() ) ) {
				add_action( 'shoestrap_entry_meta', array( $this, 'meta_custom_render' ) );
			}
			add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );
			add_action( 'wp', array( $this, 'remove_featured_image_per_post_type' ) );

			// Add featured images
			if ( ! is_singular() ) {
				add_action( 'shoestrap_entry_meta', array( $this, 'featured_image' ) );
			}

			// Chamnge the excerpt length
			if ( isset( $ss_settings['post_excerpt_length'] ) ) {
				add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
			}

			// Show full content instead of excerpt
			if ( isset( $ss_settings['blog_post_mode'] ) && 'full' == $ss_settings['blog_post_mode'] ) {
				add_filter( 'shoestrap_do_the_excerpt', 'get_the_content' );
				add_filter( 'shoestrap_do_the_excerpt', 'do_shortcode', 99 );
				add_action( 'shoestrap_entry_footer', array( $this, 'archives_full_footer' ) );
			}

			// Hide post meta data in footer of single posts
			if ( $ss_settings['single_meta'] == 0 ) {
				add_filter( 'shoestrap_the_tags', '__return_null' );
				add_filter( 'shoestrap_the_cats', '__return_null' );
			}
		}

		function options( $sections ) {
			global $ss_settings;

			// Post Meta Options
			$section = array(
				'title' => __( 'Blog', 'shoestrap' ),
				'icon'  => 'el-icon-wordpress'
			);

			$fields[] = array(
				'title'     => __( 'Archives Display Mode', 'shoestrap' ),
				'desc'      => __( 'Display the excerpt or the full post on post archives.', 'shoestrap' ),
				'id'        => 'blog_post_mode',
				'default'   => 'excerpt',
				'type'      => 'button_set',
				'options'   => array(
					'excerpt' => __( 'Excerpt', 'shoestrap' ),
					'full'    => __( 'Full Post', 'shoestrap' ),
				),
			);

			$fields[] = array(
				'id'          => 'shoestrap_entry_meta_config',
				'title'       => __( 'Activate and order Post Meta elements', 'shoestrap' ),
				'options'     => array(
					'tags'    			=> 'Tags',
					'date'    			=> 'Date',
					'category'			=> 'Category',
					'author'  			=> 'Author',
					'comment-count'	=> 'Comments',
					'sticky'  			=> 'Sticky'
				),
				'type'        => 'sortable',
				'mode'        => 'checkbox'
			);

			$fields[] = array(
				'title'     => __( 'Switch Date Meta in time_diff mode', 'shoestrap' ),
				'desc'      => __( 'Replace Date Meta element by displaying the difference between post creation timestamp and current timestamp. Default: OFF.', 'shoestrap' ),
				'id'        => 'date_meta_format',
				'default'   => 0,
				'type'      => 'switch',
			);

			$screen_large_desktop = filter_var( $ss_settings[ 'screen_large_desktop' ], FILTER_SANITIZE_NUMBER_INT );

			$fields[] = array(
				'id'        => 'help3',
				'title'     => __( 'Featured Images', 'shoestrap' ),
				'desc'      => __( 'Here you can select if you want to display the featured images in post archives and individual posts.
												Please note that these apply to posts, pages, as well as custom post types.
												You can select image sizes independently for archives and individual posts view.', 'shoestrap' ),
				'type'      => 'info',
			);

			$fields[] = array(
				'title'     => __( 'Featured Images on Archives', 'shoestrap' ),
				'desc'      => __( 'Display featured Images on post archives ( such as categories, tags, month view etc ). Default: OFF.', 'shoestrap' ),
				'id'        => 'feat_img_archive',
				'default'   => 0,
				'type'      => 'switch',
			);

			$fields[] = array(
				'title'     => __( 'Width of Featured Images on Archives', 'shoestrap' ),
				'desc'      => __( 'Set dimensions of featured Images on Archives. Default: Full Width', 'shoestrap' ),
				'id'        => 'feat_img_archive_custom_toggle',
				'default'   => 0,
				'required'  => array('feat_img_archive','=',array('1')),
				'off'       => __( 'Full Width', 'shoestrap' ),
				'on'        => __( 'Custom Dimensions', 'shoestrap' ),
				'type'      => 'switch',
			);

			$fields[] = array(
				'title'     => __( 'Archives Featured Image Custom Width', 'shoestrap' ),
				'desc'      => __( 'Select the width of your featured images on single posts. Default: 550px', 'shoestrap' ),
				'id'        => 'feat_img_archive_width',
				'default'   => 550,
				'min'       => 100,
				'step'      => 1,
				'max'       => $screen_large_desktop,
				'required'  => array('feat_img_archive','=',array('1')),
				'edit'      => 1,
				'type'      => 'slider'
			);

			$fields[] = array(
				'title'     => __( 'Archives Featured Image Custom Height', 'shoestrap' ),
				'desc'      => __( 'Select the height of your featured images on post archives. Default: 300px', 'shoestrap' ),
				'id'        => 'feat_img_archive_height',
				'default'   => 300,
				'min'       => 50,
				'step'      => 1,
				'edit'      => 1,
				'max'       => $screen_large_desktop,
				'required'  => array('feat_img_archive','=',array('1')),
				'type'      => 'slider'
			);

			$fields[] = array(
				'title'     => __( 'Featured Images on Posts', 'shoestrap' ),
				'desc'      => __( 'Display featured Images on posts. Default: OFF.', 'shoestrap' ),
				'id'        => 'feat_img_post',
				'default'   => 0,
				'type'      => 'switch',
			);

			$fields[] = array(
				'title'     => __( 'Width of Featured Images on Posts', 'shoestrap' ),
				'desc'      => __( 'Set dimensions of featured Images on Posts. Default: Full Width', 'shoestrap' ),
				'id'        => 'feat_img_post_custom_toggle',
				'default'   => 0,
				'off'       => __( 'Full Width', 'shoestrap' ),
				'on'        => __( 'Custom Dimensions', 'shoestrap' ),
				'type'      => 'switch',
				'required'  => array('feat_img_post','=',array('1')),
			);

			$fields[] = array(
				'title'     => __( 'Posts Featured Image Custom Width', 'shoestrap' ),
				'desc'      => __( 'Select the width of your featured images on single posts. Default: 550px', 'shoestrap' ),
				'id'        => 'feat_img_post_width',
				'default'   => 550,
				'min'       => 100,
				'step'      => 1,
				'max'       => $screen_large_desktop,
				'edit'      => 1,
				'required'  => array('feat_img_post','=',array('1')),
				'type'      => 'slider'
			);

			$fields[] = array(
				'title'     => __( 'Posts Featured Image Custom Height', 'shoestrap' ),
				'desc'      => __( 'Select the height of your featured images on single posts. Default: 330px', 'shoestrap' ),
				'id'        => 'feat_img_post_height',
				'default'   => 330,
				'min'       => 50,
				'step'      => 1,
				'max'       => $screen_large_desktop,
				'edit'      => 1,
				'required'  => array('feat_img_post','=',array('1')),
				'type'      => 'slider'
			);

			$post_types = get_post_types( array( 'public' => true ), 'names' );
			$post_type_options  = array();
			$post_type_defaults = array();

			foreach ( $post_types as $post_type ) {
				$post_type_options[$post_type]  = $post_type;
				$post_type_defaults[$post_type] = 0;
			}

			$fields[] = array(
				'title'     => __( 'Disable featured images on single post types', 'shoestrap' ),
				'id'        => 'feat_img_per_post_type',
				'type'      => 'checkbox',
				'options'   => $post_type_options,
				'default'   => $post_type_defaults,
			);

			$fields[] = array(
				'title'     => __( 'Post excerpt length', 'shoestrap' ),
				'desc'      => __( 'Choose how many words should be used for post excerpt. Default: 40', 'shoestrap' ),
				'id'        => 'post_excerpt_length',
				'default'   => 40,
				'min'       => 10,
				'step'      => 1,
				'max'       => 1000,
				'edit'      => 1,
				'type'      => 'slider'
			);

			$fields[] = array(
				'title'     => __( '"more" text', 'shoestrap' ),
				'desc'      => __( 'Text to display in case of excerpt too long. Default: Continued', 'shoestrap' ),
				'id'        => 'post_excerpt_link_text',
				'default'   => __( 'Continued', 'shoestrap' ),
				'type'      => 'text'
			);

			$fields[] = array(
				'title'     => __( 'Show Breadcrumbs', 'shoestrap' ),
				'desc'      => __( 'Display Breadcrumbs. Default: OFF.', 'shoestrap' ),
				'id'        => 'breadcrumbs',
				'default'   => 0,
				'type'      => 'switch',
			);

			$fields[] = array(
				'title'     => __( 'Show Post Meta in single posts', 'shoestrap' ),
				'desc'      => __( 'Toggle Post Meta showing in the footer of single posts. Default: ON.', 'shoestrap' ),
				'id'        => 'single_meta',
				'default'   => 1,
				'type'      => 'switch',
			);

			$section['fields'] = $fields;
			$section    = apply_filters( 'shoestrap_module_blog_modifier', $section );
			$sections[] = $section;

			return $sections;
		}

		/**
		 * Footer for full-content posts.
		 * Used on archives when 'blog_post_mode' == full
		 */
		function archives_full_footer() { ?>
			<footer style="margin-top: 2em;">
				<i class="el-icon-tag"></i> <?php _e( 'Categories: ', 'shoestrap' ); ?>
				<span class="label label-tag">
					<?php echo get_the_category_list( '</span> ' . '<span class="label label-tag">' ); ?>
				</span>

				<?php echo get_the_tag_list( '<i class="el-icon-tags"></i> ' . __( 'Tags: ', 'shoestrap' ) . '<span class="label label-tag">', '</span> ' . '<span class="label label-tag">', '</span>' ); ?>

				<?php wp_link_pages( array(
					'before' => '<nav class="page-nav"><p>' . __( 'Pages:', 'shoestrap' ),
					'after'  => '</p></nav>'
				) ); ?>
			</footer>
			<?php
		}

		/**
		 * Output of meta information for current post: categories, tags, permalink, author, and date.
		 */
		function meta_custom_render() {
			global $ss_framework, $ss_settings, $post;

			// get config and data
			$metas = $ss_settings['shoestrap_entry_meta_config'];
			$date_format = $ss_settings['date_meta_format'];

			$categories_list = get_the_category_list( __( ', ', 'shoestrap' ) );
			$tag_list        = get_the_tag_list( '', __( ', ', 'shoestrap' ) );

			$i = 0;
			if ( is_array( $metas ) ) {
				foreach ( $metas as $meta => $value ) {
					if ( $meta == 'sticky' ) {
						if ( ! empty( $value ) && is_sticky() ) {
							$i++;
						}
					} elseif ( $meta == 'date' ) {
						if ( ! empty( $value ) ) {
							$i++;
						}
					} elseif ( $meta == 'category' ) {
						if ( ! empty( $value ) && has_category() ) {
							$i++;
						}
					} elseif ( $meta == 'tags' ) {
						if ( ! empty( $value ) && has_tag() ) {
							$i++;
						}
					} elseif ( $meta == 'author' ) {
						if ( ! empty( $value ) ) {
							$i++;
						}
					} elseif ( $meta == 'comment-count' && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
						if ( ! empty( $value ) ) {
							$i++;
						}
					}
				}
			}

			$col = ( $i >= 2 ) ? round( ( 12 / ( $i ) ), 0) : 12;

			$content = '';
			if ( is_array( $metas ) ) {
				foreach ( $metas as $meta => $value ) {
					// output sticky element
					if ( $meta == 'sticky' && ! empty( $value ) && is_sticky() ) {
						$content .= $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'featured-post' ) . '<i class="el-icon-flag icon"></i> ' . __( 'Sticky', 'shoestrap' ) . $ss_framework->close_col( 'span' );
					}

					// output date element
					if ( $meta == 'date' && ! empty( $value ) ) {
						if ( ! has_post_format( 'link' ) ) {
							$format_prefix = ( has_post_format( 'chat' ) || has_post_format( 'status' ) ) ? _x( '%1$s on %2$s', '1: post format name. 2: date', 'shoestrap' ): '%2$s';

							if ( $date_format == 0 ) {
								$text = esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) );
								$icon = "el-icon-calendar icon";
							}
							elseif ( $date_format == 1 ) {
								$text = sprintf( human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago');
								$icon = "el-icon-time icon";
							}

							$content .= sprintf( $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'date' ) . '<i class="' . $icon . '"></i> <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>' . $ss_framework->close_col( 'span' ),
								esc_url( get_permalink() ),
								esc_attr( sprintf( __( 'Permalink to %s', 'shoestrap' ), the_title_attribute( 'echo=0' ) ) ),
								esc_attr( get_the_date( 'c' ) ),
								$text
							);
						}
					}

					// output category element
					if ( $meta == 'category' && ! empty( $value ) ) {
						if ( $categories_list ) {
							$content .= $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'categories-links' ) . '<i class="el-icon-folder-open icon"></i> ' . $categories_list . $ss_framework->close_col( 'span' );
						}
					}

					// output tag element
					if ( $meta == 'tags' && ! empty( $value ) ) {
						if ( $tag_list ) {
							$content .= $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'tags-links' ) . '<i class="el-icon-tags icon"></i> ' . $tag_list . $ss_framework->close_col( 'span' );
						}
					}

					// output author element
					if ( $meta == 'author' && ! empty( $value ) ) {
						$content .= sprintf( $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'author vcard' ) . '<i class="el-icon-user icon"></i> <a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a>' . $ss_framework->close_col( 'span' ),
							esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
							esc_attr( sprintf( __( 'View all posts by %s', 'shoestrap' ), get_the_author() ) ),
							get_the_author()
						);
					}

					// output comment count element
					if ( $meta == 'comment-count' && ! empty( $value ) ) {
						if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
							$content .= $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'comments-link' ) . '<i class="el-icon-comment icon"></i> <a href="' . get_comments_link( $post->ID ) . '">' . get_comments_number( $post->ID ) . ' ' . __( 'Comments', 'shoestrap' ) . '</a>' . $ss_framework->close_col( 'span' );
						}
					}

					// Output author meta but do not display it if user has selected not to show it.
					if ( $meta == 'author' && empty( $value ) ) {
						$content .= sprintf( $ss_framework->open_col( 'span', array( 'medium' => $col ), null, 'author vcard' ) . '<a class="url fn n" href="%1$s" title="%2$s" rel="author" style="display:none;">%3$s</a>' . $ss_framework->close_col( 'span' ),
							esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
							esc_attr( sprintf( __( 'View all posts by %s', 'shoestrap' ), get_the_author() ) ),
							get_the_author()
						);
					}
				}
			}

			if ( ! empty( $content ) ) {
				echo $ss_framework->open_row( 'div', null, 'row-meta' ) . $content . $ss_framework->close_row( 'div' );
			}
		}

		/**
		 * The "more" text
		 */
		function excerpt_more( $more ) {
			global $ss_settings;

			$continue_text = $ss_settings['post_excerpt_link_text'];
			return ' &hellip; <a href="' . get_permalink() . '">' . $continue_text . '</a>';
		}

		/**
		 * Excerpt length
		 */
		function excerpt_length($length) {
			global $ss_settings;

			$excerpt_length = $ss_settings['post_excerpt_length'];
			return $excerpt_length;
		}

		/*
		 * Display featured images on individual posts
		 */
		function featured_image() {
			global $ss_framework, $ss_settings;

			$data = array();

			if ( ! has_post_thumbnail() || '' == get_the_post_thumbnail() ) {
				return;
			}

			$data['width']  = Shoestrap_Layout::content_width_px();

			if ( is_singular() ) {
				// Do not process if we don't want images on single posts
				if ( $ss_settings['feat_img_post'] != 1 ) {
					return;
				}

				$data['url'] = wp_get_attachment_url( get_post_thumbnail_id() );

				if ( $ss_settings['feat_img_post_custom_toggle'] == 1 ) {
					$data['width']  = $ss_settings['feat_img_post_width'];
					$data['height'] = $ss_settings['feat_img_post_height'];
				}
			} else {
				// Do not process if we don't want images on post archives
				if ( $ss_settings['feat_img_archive'] == 0 ) {
					return;
				}

				$data['url'] = wp_get_attachment_url( get_post_thumbnail_id() );

				if ( $ss_settings['feat_img_archive_custom_toggle'] == 1 ) {
					$data['width']  = $ss_settings['feat_img_archive_width'];
					$data['height'] = $ss_settings['feat_img_archive_height'];
				}
			}

			$image = Shoestrap_Image::image_resize( $data );

			echo $ss_framework->clearfix() . '<a href="' . get_permalink() . '"><img class="featured-image ' . $ss_framework->float_class('left') . '" src="' . $image['url'] . '" /></a>';
		}

		/**
		 * Users can remove featured images per-post-type using the 'feat_img_per_post_type' control.
		 * This function makes sure that images are not added based on the user's selections.
		 */
		function remove_featured_image_per_post_type() {
			global $ss_settings;

			$post_types = get_post_types( array( 'public' => true ), 'names' );
			$post_type_options = (array) $ss_settings['feat_img_per_post_type'];

			foreach ( $post_types as $post_type ) {
				// Simply prevents "illegal string offset" messages
				if ( ! isset( $post_type_options[$post_type] ) ) {
					$post_type_options[$post_type] = 0;
				}

				if ( isset( $post_type ) && is_singular( $post_type ) ) {
					add_action( 'shoestrap_entry_meta', array( $this, 'featured_image' ) );
				}
			}
		}
	}
}
