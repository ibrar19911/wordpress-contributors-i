<?php
/**
 * Plugin Name: WordPress Contributors
 * Plugin URI: https://www.example.com
 * Description: This is an assignment plugin with which we can assign multiple authors to one post.
 * Version: 1.0.0
 * Author: Muhammad Ibrar
 * Author URI: https://profiles.wordpress.org/ibrar1991/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordpress-contributors
 * Domain Path: /languages
 *
 * @package WordPress Contributors
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Important Constants.
define( 'WPCI_CSS_URL', plugins_url( 'wordpress-contributors-i' ) . '/css/' );

// File Includes.
require_once 'includes/class-wpci-add-meta-boxes.php';
require_once 'includes/class-wpci-enqueue-styles-and-scripts.php';
require_once 'includes/class-wpci-the-content-filters.php';
