<?php
/**
 * Plugin Name: Flatsome Account Lightbox Ajax
 * Plugin URI: http://github.com/kenzouno1
 * Description: Custom flatsome account lightbox
 * Version: 1.0
 * Author: Dat Nguyen
 * Author URI:  http://github.com/kenzouno1
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
require_once ABSPATH . "wp-includes/pluggable.php";

$theme = wp_get_theme(); // gets the current theme

//for only Flatsome theme lightbox account option
if (('Flatsome' == $theme->name || 'Flatsome' == $theme->parent_theme)
	&& !class_exists('Flatsome_Account_Lightbox' && !is_user_logged_in()
		&& get_theme_mod('account_login_style', 'lightbox') == 'lightbox')) {
	class Flatsome_Account_Lightbox {
		function __construct() {
			add_action('wp_enqueue_scripts', array($this, 'load_my_script'), 99);
			add_action("wp_ajax_nopriv_fs_login", array($this, "custom_flatsome_ajax_login"));
			add_action("wp_ajax_nopriv_fs_register", array($this, "custom_flatsome_ajax_register"));
		}

		function load_my_script() {
			wp_enqueue_script('parsley', plugin_dir_url(__FILE__) . 'assets/js/parsley.min.js', array('jquery'), true);
			wp_enqueue_script('flatsome-account-js', plugin_dir_url(__FILE__) . 'assets/js/flatsome-account.min.js', array('jquery'), true);
			wp_enqueue_style('flatsome-account-css', plugin_dir_url(__FILE__) . 'assets/css/flatsome-account.css', true);
		}

		function custom_flatsome_ajax_login() {
			check_ajax_referer('woocommerce-login', '_wpnonce');
			$info = array(
				'user_login' => $_POST['username'],
				'user_password' => $_POST['password'],
				'remember' => isset($_POST['rememberme']),
			);
			$user_signon = wp_signon($info, false);
			if (is_wp_error($user_signon)) {
				echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.')));
			} else {
				echo json_encode(array('loggedin' => true, 'message' => __('Login successful, redirecting...')));
			}
			exit();
		}
		function custom_flatsome_ajax_register() {
			check_ajax_referer('woocommerce-register', '_wpnonce');
			$info = array();
			$info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['first_name'] = $info['user_login'] = sanitize_user($_POST['email']);
			$info['user_pass'] = sanitize_text_field($_POST['password']);
			$info['user_email'] = sanitize_email($_POST['email']);

			// Register the user
			$user_register = wp_insert_user($info);
			if (is_wp_error($user_register)) {
				$error = $user_register->get_error_codes();
				if (in_array('empty_user_login', $error)) {
					echo json_encode(array('loggedin' => false, 'message' => __($user_register->get_error_message('empty_user_login'))));
				} elseif (in_array('existing_user_login', $error)) {
					echo json_encode(array('loggedin' => false, 'message' => __('This username is already registered.')));
				} elseif (in_array('existing_user_email', $error)) {
					echo json_encode(array('loggedin' => false, 'message' => __('This email address is already registered.')));
				}
				echo json_encode($user_register->get_error_codes());
			} else {
				//login after register
				$login = array(
					'user_login' => $info['nickname'],
					'user_password' => $info['user_pass'],
					'remember' => true,
				);
				$user_signon = wp_signon($login, false);
				wp_set_current_user($user_signon->ID);
				echo json_encode(array('loggedin' => true, 'message' => __('Login successful, redirecting...')));
			}
			exit();
		}
	}
	new Flatsome_Account_Lightbox();
}