<?php
/**
 * Plugin Name: Users Block
 * Description: A block to display users and their metas
 * Author: Riccardo Strobbia
 * License: GPL-3.0
 */

namespace Inpsyde\UsersBlock;

require_once __DIR__ . '/vendor/autoload.php';

function init_users_block() {
	$render_user_list = new RenderUsersList( new \WP_User_Query() );
	$extend_user_rest = new ExtendUserRest();
	$main = new Main( $extend_user_rest, $render_user_list );
	$main->add_hooks();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\init_users_block' );
