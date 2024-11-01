<?php
/*
Plugin Name: Simple Meta Tag for Pages and Post
Plugin URI: https://www.lonare.com/simple-meta-tag-for-pages-and-post/
Description: A super simple plugin to edit meta tags on all your posts and pages for SEO. Facebook's OpenGraph and Twitter Cards are included.
Version: 1.0
Tags: meta tags, seo, edit meta tags, search engine optimization, facebook open graph, twitter cards, schema.org
Text Domain: simple-meta-tags-for-pages-and-post
Author: Lonare
Author URI: https://www.lonare.com
*/



	//better safe than sorry
		if (!function_exists('add_action')){
			echo esc_html__('Hiya Tiger! I\'m just a plugin, not much I can do when you call me directly.','simple-meta-tags-for-pages-and-post');
			exit;
		}

		
		
	//plugin main file path
		define( 'DP_META_TAGS_PLUGIN_FILE', __FILE__ );
		
		
		
	//add settings link to plugin page
		function dp_metatags_actions( $links, $file ) {
			if( $file == plugin_basename( DP_META_TAGS_PLUGIN_FILE ) && function_exists( 'admin_url' ) ) {
				$settings_link = '<a href="' . admin_url( 'options-general.php?page=meta-tags-options' ) . '">' . __('Set up tags','simple-meta-tags-for-pages-and-post') . '</a>';				
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
		add_filter( 'plugin_action_links', 'dp_metatags_actions', 10, 2 );
	
	
	
	//add notice to theme page
		function dp_metatags_notice() {				
			global $pagenow;			
			
			if($pagenow == 'theme-install.php' || $pagenow == 'themes.php'){
				echo '<div class="notice notice-success is-dismissible">
					<p>'.__('Starting a new Blog. Need more audiance with guest Blogging? <a href="https://www.lonare.com/guest-post-2/" target="_blank">Have a look around here!','simple-meta-tags-for-pages-and-post').'</a></p>			
				</div>';			
			}
		}
		add_action( 'admin_notices', 'dp_metatags_notice' );
		
		
		
	//add notice on plugin activation				
		register_activation_hook( __FILE__, 'dp_metatags_notice_activation_hook' );		 
		function dp_metatags_notice_activation_hook() {
			set_transient( 'dp-metatags-activation-notice', true, 5 );
		}
		 
		 
		add_action( 'admin_notices', 'dp_metatags_activation_notice' );		 
		function dp_metatags_activation_notice(){			
			if( get_transient( 'dp-metatags-activation-notice' ) ){
				
				echo '<div class="updated notice is-dismissible">
					<p>'.__('Thank you for using our plugin.','simple-meta-tags-for-pages-and-post').'</a></p>			
				</div>';
				
				delete_transient( 'dp-metatags-activation-notice' );
			}
		}	
		
		 
	
	 
	//settings page in admin 		
		function dp_metatags_admin_option(){	 
	         add_menu_page( 'Simple Meta Tag', 'Simple Meta Tag', 'dashicons-share-alt', 'simple-meta-tag', 'meta_init' );
			 add_submenu_page(
				'options-general.php',
				esc_html__( 'Simple Meta tags', 'simple-meta-tags-for-pages-and-post' ),
				esc_html__( 'Simple Meta tags', 'simple-meta-tags-for-pages-and-post' ),
				'manage_options',
				'simple-meta-tags-for-pages-and-post-options',
				'dp_metatags_settings_page'
			);
			
		}
		add_action('admin_menu', 'dp_metatags_admin_option');
		 

		function dp_metatags_settings_page(){			
		
			//check user permission
				if(!current_user_can('administrator')){				
					return;
				}

			
			if(!empty($_POST['submit']) && check_admin_referer('dp_metatags_save_settings', 'dp-metatags-nonce')){
				//save changes
				
				if(!empty($_POST['dp-metatags-general-description'])){ update_option('dp-metatags-general-description',sanitize_text_field($_POST['dp-metatags-general-description'])); }
				if(!empty($_POST['dp-metatags-general-keywords'])){ update_option('dp-metatags-general-keywords',sanitize_text_field($_POST['dp-metatags-general-keywords'])); }
				if(!empty($_POST['dp-metatags-general-title'])){ update_option('dp-metatags-general-title',sanitize_text_field($_POST['dp-metatags-general-title'])); }
				
				if(!empty($_POST['dp-metatags-og-title'])){ update_option('dp-metatags-og-title',sanitize_text_field($_POST['dp-metatags-og-title'])); }
				if(!empty($_POST['dp-metatags-og-type'])){ update_option('dp-metatags-og-type',sanitize_text_field($_POST['dp-metatags-og-type'])); }
				if(!empty($_POST['dp-metatags-og-audio'])){ update_option('dp-metatags-og-audio',esc_url($_POST['dp-metatags-og-audio'])); }
				if(!empty($_POST['dp-metatags-og-image'])){ update_option('dp-metatags-og-image',esc_url($_POST['dp-metatags-og-image'])); }
				if(!empty($_POST['dp-metatags-og-video'])){ update_option('dp-metatags-og-video',esc_url($_POST['dp-metatags-og-video'])); }
				if(!empty($_POST['dp-metatags-og-url'])){ update_option('dp-metatags-og-url',esc_url($_POST['dp-metatags-og-url'])); }
				if(!empty($_POST['dp-metatags-og-description'])){ update_option('dp-metatags-og-description',sanitize_text_field($_POST['dp-metatags-og-description'])); }
				
				if(!empty($_POST['dp-metatags-twitter-card'])){ update_option('dp-metatags-twitter-card',sanitize_text_field($_POST['dp-metatags-twitter-card'])); }
				if(!empty($_POST['dp-metatags-twitter-title'])){ update_option('dp-metatags-twitter-title',sanitize_text_field($_POST['dp-metatags-twitter-title'])); }
				if(!empty($_POST['dp-metatags-twitter-description'])){ update_option('dp-metatags-twitter-description',sanitize_text_field($_POST['dp-metatags-twitter-description'])); }
				if(!empty($_POST['dp-metatags-twitter-image'])){ update_option('dp-metatags-twitter-image',esc_url($_POST['dp-metatags-twitter-image'])); }
				
				$allowed_html = array(
					'meta' => array(
						'name' => array(),
						'property' => array(),
						'content' => array(),						
						'http-equiv' => array()
					)
				);
				if(!empty($_POST['dp-metatags-custom'])){ update_option('dp-metatags-custom',wp_kses( $_POST['dp-metatags-custom'], $allowed_html )); }
				
			}
		
			$dp_metatags_general_description = get_option('dp-metatags-general-description');
			$dp_metatags_general_keywords = get_option('dp-metatags-general-keywords');
			$dp_metatags_general_title = get_option('dp-metatags-general-title');
			
			$dp_metatags_og_title = get_option('dp-metatags-og-title');
			$dp_metatags_og_type = get_option('dp-metatags-og-type');
			$dp_metatags_og_audio = get_option('dp-metatags-og-audio');
			$dp_metatags_og_image = get_option('dp-metatags-og-image');
			$dp_metatags_og_video = get_option('dp-metatags-og-video');
			$dp_metatags_og_url = get_option('dp-metatags-og-url');
			$dp_metatags_og_description = get_option('dp-metatags-og-description');
			
			$dp_metatags_twitter_card = get_option('dp-metatags-twitter-card');			
			$dp_metatags_twitter_title = get_option('dp-metatags-twitter-title');
			$dp_metatags_twitter_description = get_option('dp-metatags-twitter-description');
			$dp_metatags_twitter_image = get_option('dp-metatags-twitter-image');
			
			$dp_metatags_custom = get_option('dp-metatags-custom');
			
			$page_on_front = get_option('page_on_front');
		
		
			echo '<h1>'.esc_html__('Meta Tags','simple-meta-tags-for-pages-and-post').'</h1>';
			
			if($page_on_front == '0'){
				//frontpage shows latest posts				
				echo '<p>'.__('It seems the frontpage shows your latest posts (based on <b>Settings - Reading</b>). Here you can set up meta tags for the frontpage.').'<br />'
				.__('For the rest please visit the page/post editor where you can add specific meta tags for each of them in the <b>Meta Tag Editor</b> box.','simple-meta-tags-for-pages-and-post').'</p>
				
				<p>&nbsp;</p>
				
				<form method="post" action="options-general.php?page=simple-meta-tags-for-pages-and-post-options" novalidate="novalidate">';
				
				
				//add nonce
					wp_nonce_field( 'dp_metatags_save_settings', 'dp-metatags-nonce' );
				
				
				//general meta tags
					echo'
					<h2 class="title">'.esc_html__('General meta tags','simple-meta-tags-for-pages-and-post').'</h2>
					
					<div style="margin-left: 20px;">
						<p><label for="dp-metatags-general-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('This text will appear below your title in Google search results. Describe this page/post in 155 maximum characters. Note: Google will not consider this in its search ranking algorithm.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-general-description" name="dp-metatags-general-description" class="regular-text" value="'.(!empty($dp_metatags_general_description) ? esc_attr($dp_metatags_general_description) : '').'" /></p>
						
						<p><label for="dp-metatags-general-keywords"><b>'.__('Keywords','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Improper or spammy use most likely will hurt you with some search engines. Google will not consider this in its search ranking algorithm, so it\'s not really recommended.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-general-keywords" name="dp-metatags-general-keywords" class="regular-text" value="'.(!empty($dp_metatags_general_keywords) ? esc_attr($dp_metatags_general_keywords) : '').'" /></p>
						
						<p><label for="dp-metatags-general-title"><b>'.__('Page title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Make page titles as keyword-relevant as possible and up to 70 characters. Longer titles are oftentimes chopped down or rewritten algorithmically.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-general-title" name="dp-metatags-general-title" class="regular-text" value="'.(!empty($dp_metatags_general_title) ? esc_attr($dp_metatags_general_title) : '').'" /></p>
					</div>
					
					<p>&nbsp;</p>
					<hr />
					';
			
			
				//Facebook's OpenGraph meta tags
					echo '
					<h2 class="title">'.esc_html__('Facebook\'s OpenGraph meta tags','simple-meta-tags-for-pages-and-post').'</h2>
					<p>'.esc_html__('Open Graph has become very popular, so most social networks default to Open Graph if no other meta tags are present.','simple-meta-tags-for-pages-and-post').'</p>
					
					<div style="margin-left: 20px;">
						<p><label for="dp-metatags-og-title"><b>'.__('Title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('The headline.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-title" name="dp-metatags-og-title" class="regular-text" value="'.(!empty($dp_metatags_og_title) ? esc_attr($dp_metatags_og_title) : '').'" /></p>
						
						<p><label for="dp-metatags-og-type"><b>'.__('Type','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Article, website or other. Here is a list of all available types: <a href="http://ogp.me/#types" target="_blank">http://ogp.me/#types</a>','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-type" name="dp-metatags-og-type" class="regular-text" value="'.(!empty($dp_metatags_og_type) ? esc_attr($dp_metatags_og_type) : '').'" /></p>
						
						<p><label for="dp-metatags-og-audio"><b>'.__('Audio','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s audio.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-audio" name="dp-metatags-og-audio" class="regular-text" value="'.(!empty($dp_metatags_og_audio) ? esc_attr($dp_metatags_og_audio) : '').'" /></p>
						
						<p><label for="dp-metatags-og-image"><b>'.__('Image','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s image. It should be at least 600x315 pixels, but 1200x630 or larger is preferred (up to 5MB). Stay close to a 1.91:1 aspect ratio to avoid cropping.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-image" name="dp-metatags-og-image" class="regular-text" value="'.(!empty($dp_metatags_og_image) ? esc_attr($dp_metatags_og_image) : '').'" /></p>
						
						<p><label for="dp-metatags-og-video"><b>'.__('Video','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s video. Videos need an og:image tag to be displayed in News Feed.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-video" name="dp-metatags-og-video" class="regular-text" value="'.(!empty($dp_metatags_og_video) ? esc_attr($dp_metatags_og_video) : '').'" /></p>
						
						<p><label for="dp-metatags-og-url"><b>'.__('URL','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('The URL of your page. Use the canonical URL for this tag (the search engine friendly URL that you want the search engines to treat as authoritative).','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-url" name="dp-metatags-og-url" class="regular-text" value="'.(!empty($dp_metatags_og_url) ? esc_attr($dp_metatags_og_url) : '').'" /></p>
						
						<p><label for="dp-metatags-og-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('A short summary about the content.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-og-description" name="dp-metatags-og-description" class="regular-text" value="'.(!empty($dp_metatags_og_description) ? esc_attr($dp_metatags_og_description) : '').'" /></p>
						
					</div>
					
					<p>&nbsp;</p>
					<hr />
					';
				
				
				//Twitter meta tags
					echo '
					<h2 class="title">'.esc_html__('Twitter cards','simple-meta-tags-for-pages-and-post').'</h2>
					
					<div style="margin-left: 20px;">
						<p><label for="dp-metatags-twitter-card"><b>'.__('Card','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('This is the card type. Your options are summary, photo or player. Twitter will default to "summary" if it is not specified.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-twitter-card" name="dp-metatags-twitter-card" class="regular-text" value="'.(!empty($dp_metatags_twitter_card) ? esc_attr($dp_metatags_twitter_card) : '').'" /></p>
						
						<p><label for="dp-metatags-twitter-title"><b>'.__('Title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('A concise title for the related content.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-twitter-title" name="dp-metatags-twitter-title" class="regular-text" value="'.(!empty($dp_metatags_twitter_title) ? esc_attr($dp_metatags_twitter_title) : '').'" /></p>
						
						<p><label for="dp-metatags-twitter-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Summary of content.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-twitter-description" name="dp-metatags-twitter-description" class="regular-text" value="'.(!empty($dp_metatags_twitter_description) ? esc_attr($dp_metatags_twitter_description) : '').'" /></p>					
						
						<p><label for="dp-metatags-twitter-image"><b>'.__('Image','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Image representing the content. Use aspect ratio of 1:1 with minimum dimensions of 144x144 or maximum of 4096x4096 pixels. Images must be less than 5MB in size.','simple-meta-tags-for-pages-and-post').'</span></p>
						<p><input type="text" id="dp-metatags-twitter-image" name="dp-metatags-twitter-image" class="regular-text" value="'.(!empty($dp_metatags_twitter_image) ? esc_attr($dp_metatags_twitter_image) : '').'" /></p>
						
					</div>
					
					<p>&nbsp;</p>
					<hr />
					';
							
				
				//Custom meta tags 			
					echo '
					<h2 class="title">'.esc_html__('Custom meta tags','simple-meta-tags-for-pages-and-post').'</h2>			
					
					<div style="margin-left: 20px;">					
						<textarea id="dp-metatags-custom" name="dp-metatags-custom" class="regular-text code">'.(!empty($dp_metatags_custom) ? esc_textarea($dp_metatags_custom) : '').'</textarea>
					</div>
					
					
					<p class="submit"><input name="submit" id="submit" class="button button-primary" value="'.__('Save Changes','simple-meta-tags-for-pages-and-post').'" type="submit"></p>
					</form>
					';
				
			}else{
				//frontpage shows a specific page				
				echo '<p>'.__('Go to your page/post editor and you will find a new <b>Meta Tag Editor</b> box where you can add specific meta tags for each of them. here is a video explaining it: https://www.lonare.com/simple-meta-tag-for-pages-and-post/','simple-meta-tags-for-pages-and-post'). '</p>';
			}
			
			
		}
	
		
	
	
	//register metabox
		function dp_metatags_metabox(){	
			if(function_exists('add_meta_box')){		
				add_meta_box( 'dp-metatags', esc_html__('Meta Tag Editor','simple-meta-tags-for-pages-and-post'), 'dp_metatags_editor', 'page', 'normal' );						
				add_meta_box( 'dp-metatags', esc_html__('Meta Tag Editor','simple-meta-tags-for-pages-and-post'), 'dp_metatags_editor', 'post', 'normal' );						
			}
		}		
		add_action('admin_menu', 'dp_metatags_metabox');
	
	

	//meta tag editor 
		function dp_metatags_editor(){
			global $post;
		
			//load saved values
			$dp_metatags_general_description = get_post_meta($post->ID, 'dp-metatags-general-description', true);
			$dp_metatags_general_keywords = get_post_meta($post->ID, 'dp-metatags-general-keywords', true);
			$dp_metatags_general_title = get_post_meta($post->ID, 'dp-metatags-general-title', true);
			$dp_metatags_og_title = get_post_meta($post->ID, 'dp-metatags-og-title', true);
			$dp_metatags_og_type = get_post_meta($post->ID, 'dp-metatags-og-type', true);
			$dp_metatags_og_audio = get_post_meta($post->ID, 'dp-metatags-og-audio', true);
			$dp_metatags_og_image = get_post_meta($post->ID, 'dp-metatags-og-image', true);
			$dp_metatags_og_video = get_post_meta($post->ID, 'dp-metatags-og-video', true);
			$dp_metatags_og_url = get_post_meta($post->ID, 'dp-metatags-og-url', true);
			$dp_metatags_og_description = get_post_meta($post->ID, 'dp-metatags-og-description', true);
			$dp_metatags_twitter_card = get_post_meta($post->ID, 'dp-metatags-twitter-card', true);			
			$dp_metatags_twitter_title = get_post_meta($post->ID, 'dp-metatags-twitter-title', true);
			$dp_metatags_twitter_description = get_post_meta($post->ID, 'dp-metatags-twitter-description', true);
			$dp_metatags_twitter_image = get_post_meta($post->ID, 'dp-metatags-twitter-image', true);
			$dp_metatags_custom = get_post_meta($post->ID, 'dp-metatags-custom', true);
		
		
			//general meta tags
				echo'
				<p><b>'.esc_html__('General meta tags','simple-meta-tags-for-pages-and-post').'</b></p>
				
				<div style="margin-left: 20px;">
					<p><label for="dp-metatags-general-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('This text will appear below your title in Google search results. Describe this page/post in 155 maximum characters. Note: Google will not consider this in its search ranking algorithm.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-general-description" name="dp-metatags-general-description" class="regular-text" value="'.(!empty($dp_metatags_general_description) ? esc_attr($dp_metatags_general_description) : '').'" /></p>
					
					<p><label for="dp-metatags-general-keywords"><b>'.__('Keywords','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Improper or spammy use most likely will hurt you with some search engines. Google will not consider this in its search ranking algorithm, so it\'s not really recommended.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-general-keywords" name="dp-metatags-general-keywords" class="regular-text" value="'.(!empty($dp_metatags_general_keywords) ? esc_attr($dp_metatags_general_keywords) : '').'" /></p>
					
					<p><label for="dp-metatags-general-title"><b>'.__('Page title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Make page titles as keyword-relevant as possible and up to 70 characters. Longer titles are oftentimes chopped down or rewritten algorithmically.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-general-title" name="dp-metatags-general-title" class="regular-text" value="'.(!empty($dp_metatags_general_title) ? esc_attr($dp_metatags_general_title) : '').'" /></p>
				</div>
				
				<p>&nbsp;</p>
				<hr />
				';
			
			
			//Facebook's OpenGraph meta tags
				echo '
				<p><b>'.esc_html__('Facebook\'s OpenGraph meta tags','simple-meta-tags-for-pages-and-post').'</b></p>
				<p>'.esc_html__('Open Graph has become very popular, so most social networks default to Open Graph if no other meta tags are present.','simple-meta-tags-for-pages-and-post').'</p>
				
				<div style="margin-left: 20px;">
					<p><label for="dp-metatags-og-title"><b>'.__('Title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('The headline.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-title" name="dp-metatags-og-title" class="regular-text" value="'.(!empty($dp_metatags_og_title) ? esc_attr($dp_metatags_og_title) : '').'" /></p>
					
					<p><label for="dp-metatags-og-type"><b>'.__('Type','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Article, website or other. Here is a list of all available types: <a href="http://ogp.me/#types" target="_blank">http://ogp.me/#types</a>','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-type" name="dp-metatags-og-type" class="regular-text" value="'.(!empty($dp_metatags_og_type) ? esc_attr($dp_metatags_og_type) : '').'" /></p>
					
					<p><label for="dp-metatags-og-audio"><b>'.__('Audio','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s audio.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-audio" name="dp-metatags-og-audio" class="regular-text" value="'.(!empty($dp_metatags_og_audio) ? esc_attr($dp_metatags_og_audio) : '').'" /></p>
					
					<p><label for="dp-metatags-og-image"><b>'.__('Image','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s image. It should be at least 600x315 pixels, but 1200x630 or larger is preferred (up to 5MB). Stay close to a 1.91:1 aspect ratio to avoid cropping.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-image" name="dp-metatags-og-image" class="regular-text" value="'.(!empty($dp_metatags_og_image) ? esc_attr($dp_metatags_og_image) : '').'" /></p>
					
					<p><label for="dp-metatags-og-video"><b>'.__('Video','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('URL to your content\'s video. Videos need an og:image tag to be displayed in News Feed.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-video" name="dp-metatags-og-video" class="regular-text" value="'.(!empty($dp_metatags_og_video) ? esc_attr($dp_metatags_og_video) : '').'" /></p>
					
					<p><label for="dp-metatags-og-url"><b>'.__('URL','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('The URL of your page. Use the canonical URL for this tag (the search engine friendly URL that you want the search engines to treat as authoritative).','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-url" name="dp-metatags-og-url" class="regular-text" value="'.(!empty($dp_metatags_og_url) ? esc_attr($dp_metatags_og_url) : '').'" /></p>
					
					<p><label for="dp-metatags-og-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('A short summary about the content.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-og-description" name="dp-metatags-og-description" class="regular-text" value="'.(!empty($dp_metatags_og_description) ? esc_attr($dp_metatags_og_description) : '').'" /></p>
					
				</div>
				
				<p>&nbsp;</p>
				<hr />
				';
			
			
			//Twitter meta tags
				echo '
				<p><b>'.esc_html__('Twitter cards','simple-meta-tags-for-pages-and-post').'</b></p>
				
				<div style="margin-left: 20px;">
					<p><label for="dp-metatags-twitter-card"><b>'.__('Card','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('This is the card type. Your options are summary, photo or player. Twitter will default to "summary" if it is not specified.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-twitter-card" name="dp-metatags-twitter-card" class="regular-text" value="'.(!empty($dp_metatags_twitter_card) ? esc_attr($dp_metatags_twitter_card) : '').'" /></p>
					
					<p><label for="dp-metatags-twitter-title"><b>'.__('Title','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('A concise title for the related content.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-twitter-title" name="dp-metatags-twitter-title" class="regular-text" value="'.(!empty($dp_metatags_twitter_title) ? esc_attr($dp_metatags_twitter_title) : '').'" /></p>
					
					<p><label for="dp-metatags-twitter-description"><b>'.__('Description','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Summary of content.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-twitter-description" name="dp-metatags-twitter-description" class="regular-text" value="'.(!empty($dp_metatags_twitter_description) ? esc_attr($dp_metatags_twitter_description) : '').'" /></p>					
					
					<p><label for="dp-metatags-twitter-image"><b>'.__('Image','simple-meta-tags-for-pages-and-post').'</b></label><br /><span class="description">'.__('Image representing the content. Use aspect ratio of 1:1 with minimum dimensions of 144x144 or maximum of 4096x4096 pixels. Images must be less than 5MB in size.','simple-meta-tags-for-pages-and-post').'</span></p>
					<p><input type="text" id="dp-metatags-twitter-image" name="dp-metatags-twitter-image" class="regular-text" value="'.(!empty($dp_metatags_twitter_image) ? esc_attr($dp_metatags_twitter_image) : '').'" /></p>
					
				</div>
				
				<p>&nbsp;</p>
				<hr />
				';
						
			
			//Custom meta tags 			
				echo '
				<p><b>'.esc_html__('Custom meta tags','simple-meta-tags-for-pages-and-post').'</b></p>			
				
				<div style="margin-left: 20px;">					
					<textarea id="dp-metatags-custom" name="dp-metatags-custom" class="regular-text code">'.(!empty($dp_metatags_custom) ? esc_textarea($dp_metatags_custom) : '').'</textarea>
				</div>
				';
				
			
			echo '<p>&nbsp;</p>';
		}
		
		
		
	//save tags
		function dp_metatags_save(){			
			global $post;
			
			if(!empty($_POST['dp-metatags-general-description'])){
				update_post_meta($post->ID,'dp-metatags-general-description',sanitize_text_field($_POST['dp-metatags-general-description']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-general-description'); 
			}
			
			if(!empty($_POST['dp-metatags-general-keywords'])){	
				update_post_meta($post->ID,'dp-metatags-general-keywords',sanitize_text_field($_POST['dp-metatags-general-keywords']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-general-keywords'); 
			}
			
			if(!empty($_POST['dp-metatags-general-title'])){	
				update_post_meta($post->ID,'dp-metatags-general-title',sanitize_text_field($_POST['dp-metatags-general-title']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-general-title'); 
			}			
			
			
			
			if(!empty($_POST['dp-metatags-og-title'])){	
				update_post_meta($post->ID,'dp-metatags-og-title',sanitize_text_field($_POST['dp-metatags-og-title']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-title'); 
			}
			
			if(!empty($_POST['dp-metatags-og-type'])){	
				update_post_meta($post->ID,'dp-metatags-og-type',sanitize_text_field($_POST['dp-metatags-og-type']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-type'); 
			}
			
			if(!empty($_POST['dp-metatags-og-audio'])){	
				update_post_meta($post->ID,'dp-metatags-og-audio',esc_url($_POST['dp-metatags-og-audio']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-audio'); 
			}
			
			if(!empty($_POST['dp-metatags-og-image'])){	
				update_post_meta($post->ID,'dp-metatags-og-image',esc_url($_POST['dp-metatags-og-image']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-image'); 
			}
			
			if(!empty($_POST['dp-metatags-og-video'])){	
				update_post_meta($post->ID,'dp-metatags-og-video',esc_url($_POST['dp-metatags-og-video']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-video'); 
			}
			
			if(!empty($_POST['dp-metatags-og-url'])){	
				update_post_meta($post->ID,'dp-metatags-og-url',esc_url($_POST['dp-metatags-og-url']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-url'); 
			}
			
			if(!empty($_POST['dp-metatags-og-description'])){	
				update_post_meta($post->ID,'dp-metatags-og-description',sanitize_text_field($_POST['dp-metatags-og-description']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-og-description'); 
			}
			
			
			
			
			
			if(!empty($_POST['dp-metatags-twitter-card'])){	
				update_post_meta($post->ID,'dp-metatags-twitter-card',sanitize_text_field($_POST['dp-metatags-twitter-card']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-twitter-card'); 
			}
			
			if(!empty($_POST['dp-metatags-twitter-title'])){	
				update_post_meta($post->ID,'dp-metatags-twitter-title',sanitize_text_field($_POST['dp-metatags-twitter-title']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-twitter-title'); 
			}
			
			if(!empty($_POST['dp-metatags-twitter-description'])){	
				update_post_meta($post->ID,'dp-metatags-twitter-description',sanitize_text_field($_POST['dp-metatags-twitter-description']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-twitter-description'); 
			}
			
			if(!empty($_POST['dp-metatags-twitter-image'])){	
				update_post_meta($post->ID,'dp-metatags-twitter-image',esc_url($_POST['dp-metatags-twitter-image']));
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-twitter-image'); 
			}
			
			
			
			if(!empty($_POST['dp-metatags-custom'])){	
				$allowed_html = array(
					'meta' => array(
						'name' => array(),
						'property' => array(),
						'content' => array(),						
						'http-equiv' => array()
					)
				);
				
				update_post_meta($post->ID,'dp-metatags-custom',wp_kses( $_POST['dp-metatags-custom'], $allowed_html ) );
			}elseif(!empty($post->ID)){								
				delete_post_meta($post->ID,'dp-metatags-custom'); 
			}
			
			
			
			
			
		}
		add_action('save_post', 'dp_metatags_save');
	
	
	
	//frontend echo
		function dp_metatags_echo(){
			global $post;
			
			$page_on_front = get_option('page_on_front');
		
		
			echo '			
	<!-- META TAGS PLUGIN START -->';
			
			if($page_on_front == 0 && is_front_page()){
			//latest posts on front page
				$dp_metatags_general_description = get_option('dp-metatags-general-description');
				$dp_metatags_general_keywords = get_option('dp-metatags-general-keywords');
				$dp_metatags_general_title = get_option('dp-metatags-general-title');
				
				$dp_metatags_og_title = get_option('dp-metatags-og-title');
				$dp_metatags_og_type = get_option('dp-metatags-og-type');
				$dp_metatags_og_audio = get_option('dp-metatags-og-audio');
				$dp_metatags_og_image = get_option('dp-metatags-og-image');
				$dp_metatags_og_video = get_option('dp-metatags-og-video');
				$dp_metatags_og_url = get_option('dp-metatags-og-url');
				$dp_metatags_og_description = get_option('dp-metatags-og-description');
				
				$dp_metatags_twitter_card = get_option('dp-metatags-twitter-card');			
				$dp_metatags_twitter_title = get_option('dp-metatags-twitter-title');
				$dp_metatags_twitter_description = get_option('dp-metatags-twitter-description');
				$dp_metatags_twitter_image = get_option('dp-metatags-twitter-image');
				
				$dp_metatags_custom = get_option('dp-metatags-custom');
				
				
				
			
				if(!empty($dp_metatags_general_description)){ echo '
	<meta name="description" content="'.esc_attr($dp_metatags_general_description).'" />'; }

				if(!empty($dp_metatags_general_keywords)){ echo '
	<meta name="keywords" content="'.esc_attr($dp_metatags_general_keywords).'" />'; }				
				if(!empty($dp_metatags_general_title)){ 					
					add_filter('pre_get_document_title', 'dp_metatags_title');
					function dp_metatags_title($title) {			
						global $post;
						$dp_metatags_general_title = get_option('dp-metatags-general-title');
						return esc_html($dp_metatags_general_title);						
					}				
				}
				
				
				if(!empty($dp_metatags_og_title)){ echo '
	<meta property="og:title" content="'.esc_attr($dp_metatags_og_title).'" />'; }

				if(!empty($dp_metatags_og_type)){ echo '
	<meta property="og:type" content="'.esc_attr($dp_metatags_og_type).'" />'; }
				
				if(!empty($dp_metatags_og_audio)){ echo '
	<meta property="og:audio" content="'.esc_attr($dp_metatags_og_audio).'" />'; }
				
				if(!empty($dp_metatags_og_image)){ echo '
	<meta property="og:image" content="'.esc_attr($dp_metatags_og_image).'" />'; }
				
				if(!empty($dp_metatags_og_video)){ echo '
	<meta property="og:video" content="'.esc_attr($dp_metatags_og_video).'" />'; }
				
				if(!empty($dp_metatags_og_url)){ echo '
	<meta property="og:url" content="'.esc_attr($dp_metatags_og_url).'" />'; }
				
				if(!empty($dp_metatags_og_description)){ echo '
	<meta property="og:description" content="'.esc_attr($dp_metatags_og_description).'" />'; }
				
				
				
				if(!empty($dp_metatags_twitter_card)){ echo '
	<meta name="twitter:card" content="'.esc_attr($dp_metatags_twitter_card).'" />'; }
				
				if(!empty($dp_metatags_twitter_title)){ echo '
	<meta name="twitter:title" content="'.esc_attr($dp_metatags_twitter_title).'" />'; }
				
				if(!empty($dp_metatags_twitter_description)){ echo '
	<meta name="twitter:description" content="'.esc_attr($dp_metatags_twitter_description).'" />'; }
				
				if(!empty($dp_metatags_twitter_image)){ echo '
	<meta name="twitter:image" content="'.esc_attr($dp_metatags_twitter_image).'" />'; }
				
				
				if(!empty($dp_metatags_custom)){ 
					$allowed_html = array(
						'meta' => array(
							'name' => array(),
							'property' => array(),
							'content' => array(),						
							'http-equiv' => array()
						)
					);
					
					echo '
	'.wp_kses( $dp_metatags_custom, $allowed_html );					
				}
				
				
				
				
				
			}else{	
			//load actual page settings
				$dp_metatags_general_description = get_post_meta($post->ID, 'dp-metatags-general-description', true);
				$dp_metatags_general_keywords = get_post_meta($post->ID, 'dp-metatags-general-keywords', true);
				$dp_metatags_general_title = get_post_meta($post->ID, 'dp-metatags-general-title', true);
				
				$dp_metatags_og_title = get_post_meta($post->ID, 'dp-metatags-og-title', true);
				$dp_metatags_og_type = get_post_meta($post->ID, 'dp-metatags-og-type', true);
				$dp_metatags_og_audio = get_post_meta($post->ID, 'dp-metatags-og-audio', true);
				$dp_metatags_og_image = get_post_meta($post->ID, 'dp-metatags-og-image', true);
				$dp_metatags_og_video = get_post_meta($post->ID, 'dp-metatags-og-video', true);
				$dp_metatags_og_url = get_post_meta($post->ID, 'ddp-metatags-og-url', true);
				$dp_metatags_og_description = get_post_meta($post->ID, 'dp-metatags-og-description', true);
				
				$dp_metatags_twitter_card = get_post_meta($post->ID, 'dp-metatags-twitter-card', true);			
				$dp_metatags_twitter_title = get_post_meta($post->ID, 'dp-metatags-twitter-title', true);
				$dp_metatags_twitter_description = get_post_meta($post->ID, 'dp-metatags-twitter-description', true);
				$dp_metatags_twitter_image = get_post_meta($post->ID, 'dp-metatags-twitter-image', true);
				
				$dp_metatags_custom = get_post_meta($post->ID, 'dp-metatags-custom', true);
				
				
				
			
				if(!empty($dp_metatags_general_description)){ echo '
	<meta name="description" content="'.esc_attr($dp_metatags_general_description).'" />'; }

				if(!empty($dp_metatags_general_keywords)){ echo '
	<meta name="keywords" content="'.esc_attr($dp_metatags_general_keywords).'" />'; }				
				if(!empty($dp_metatags_general_title)){ 					
					add_filter('pre_get_document_title', 'dp_metatags_title');
					function dp_metatags_title($title) {			
						global $post;
						$dp_metatags_general_title = get_post_meta($post->ID, 'dp-metatags-general-title', true);
						return esc_html($dp_metatags_general_title);						
					}				
				}
				
				
				if(!empty($dp_metatags_og_title)){ echo '
	<meta property="og:title" content="'.esc_attr($dp_metatags_og_title).'" />'; }

				if(!empty($dp_metatags_og_type)){ echo '
	<meta property="og:type" content="'.esc_attr($dp_metatags_og_type).'" />'; }
				
				if(!empty($dp_metatags_og_audio)){ echo '
	<meta property="og:audio" content="'.esc_attr($dp_metatags_og_audio).'" />'; }
				
				if(!empty($dp_metatags_og_image)){ echo '
	<meta property="og:image" content="'.esc_attr($dp_metatags_og_image).'" />'; }
				
				if(!empty($dp_metatags_og_video)){ echo '
	<meta property="og:video" content="'.esc_attr($dp_metatags_og_video).'" />'; }
				
				if(!empty($dp_metatags_og_url)){ echo '
	<meta property="og:url" content="'.esc_attr($dp_metatags_og_url).'" />'; }
				
				if(!empty($dp_metatags_og_description)){ echo '
	<meta property="og:description" content="'.esc_attr($dp_metatags_og_description).'" />'; }
				
				
				
				if(!empty($dp_metatags_twitter_card)){ echo '
	<meta name="twitter:card" content="'.esc_attr($dp_metatags_twitter_card).'" />'; }
				
				if(!empty($dp_metatags_twitter_title)){ echo '
	<meta name="twitter:title" content="'.esc_attr($dp_metatags_twitter_title).'" />'; }
				
				if(!empty($dp_metatags_twitter_description)){ echo '
	<meta name="twitter:description" content="'.esc_attr($dp_metatags_twitter_description).'" />'; }
				
				if(!empty($dp_metatags_twitter_image)){ echo '
	<meta name="twitter:image" content="'.esc_attr($dp_metatags_twitter_image).'" />'; }
				
				
				if(!empty($dp_metatags_custom)){ 
					$allowed_html = array(
						'meta' => array(
							'name' => array(),
							'property' => array(),
							'content' => array(),						
							'http-equiv' => array()
						)
					);
					
					echo '
	'.wp_kses( $dp_metatags_custom, $allowed_html );					
				}
				
				
			}	
			
			
			echo '
	<!-- META TAGS PLUGIN END -->
			
			';
		}
		add_action('wp_head', 'dp_metatags_echo', 0);
	
	

	
	
?>