<?php
/*
Plugin Name: Share Post
Description: 
Version: 1.0
Author: 
Author URI: 
*/

//Catch anyone trying to directly acess the plugin - which isn't allowed
if (!function_exists('add_action')) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

//Check if the the class already exists
if (!class_exists("SharePostPlugin")) {
	class SharePostPlugin {

		private $_VersionNumber = "1.0";
		private $_LoadsysURL = "";

		public function __construct() {
		}
		
		public function add_menu()
		{
			add_menu_page('Share Post', 'Share Post', 'administrator', 'plugin', array($this, 'options_page'));
		}
		
		public function options_page()
		{
			if(!current_user_can('manage_options'))
			{
				wp_die('You do not have sufficient permissions to access this page.');  
			}
		
			?>
				<div class="wrap">
				<?php screen_icon('plugins'); ?>
				<h2>Syndicate Plugin Options</h2>
				<?php

				$user = wp_get_current_user();				
				
				if(isset($_POST['update_email']) && !empty($_POST['update_email']))
				{
					update_user_meta($user->ID, 'sharepost_email', $_POST['email']);
					update_user_meta($user->ID, 'sharepost_from', $_POST['from']);
				?>
				<div id="message" class="updated">Settings saved</div>
				<?php
				}
				?>
				<form method="post" action="">
				<?php 
				settings_fields('sharepost-optionsgroup'); 
				do_settings_sections('sharepost-optionsgroup');
				?>
				<table class='form-table' width='100%' cellpadding='10'>
					<tbody>
						<tr>
							<td scope="row" align="left">
								<label>E-mail To</label>
							</td>
						</tr>
						<tr>
							<td scope="row" align="left">
								<input type="text" name="email" value="<?php echo get_user_meta($user->ID, 'sharepost_email', true); ?>" />
							</td>
						</tr>
						<tr>
							<td scope="row" align="left">
								<label>Send From</label>
							</td>
						</tr>
						<tr>
							<td scope="row" align="left">
								<input type="text" name="from" value="<?php echo get_user_meta($user->ID, 'sharepost_from', true); ?>" />
							</td>
						</tr>
						<tr>
							<td scope="row" align="left">
								<?php submit_button(); ?>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="update_email" value="update">
				</form>
				</div>
			<?php
		}
		
		public function meta_button()
		{
			global $pagenow;
			
			if('post.php' == $pagenow)
			{
				global $post;
				
				$user = wp_get_current_user();
				
				$posto = array('user' => $user->ID, 'id' => $post->ID, 'title' => $post->post_title,  'content' => $post->post_content, 'emailScript' => plugins_url('/email.php', __FILE__), 'mailto' => get_user_meta($user->ID, 'sharepost_email', true), 'mailfrom' => get_option($user->ID, 'sharepost_from', true));
				
				wp_localize_script('sharepost-script', 'post', $posto);
				
				echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';

				?>
					<form>
						<div id="shareStatus"></div>
						<?php 
						$syndicated = get_user_meta($user->ID, $post->ID, true);
						
						if(empty($syndicated))
						{
						?>
							<input type="button" id="share" class="button button-primary" value="Publish via E-mail" style="float:right;"><div class="clear"></div>
						<?php
						}
						else
						{
						?>
							<input type="button" id="share" class="button button-primary" value="Publish via E-mail" style="float:right;" disabled><div class="clear"></div>
						<?php
						}
						?>
					</form>
				<?php
				
				echo '</div>';
			}
		}
		
		public function enqueue()
		{
			wp_enqueue_script('sharepost-script', plugins_url('/sharepost.js', __FILE__), array('jquery'), '0', true); 
		}
	}
}

if (class_exists("SharePostPlugin")) {
	$s_Plugin = new SharePostPlugin();
}

if (isset($s_Plugin)) {
	add_action('admin_menu', array($s_Plugin, 'add_menu'));
	if('post.php' == $pagenow)
	{
		add_action('post_submitbox_misc_actions', array($s_Plugin, 'meta_button'));
		add_action('admin_enqueue_scripts', array($s_Plugin, 'enqueue'));
	}
}

?>