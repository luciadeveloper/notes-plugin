<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
// delete notes form data base
global $wpdb;
$wpdb->query("DELETE FROM `wp_posts` WHERE  `post_type` =  'note'");