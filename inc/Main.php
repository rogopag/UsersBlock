<?php

namespace Inpsyde\UsersBlock;

class Main{
	const DEPENDENCIES_BE = [
		'wp-i18n',
		'wp-blocks',
		'wp-components',
		'wp-editor',
		'wp-plugins',
		'wp-edit-post',
	];
	const DEPENDENCIES_FE = [
		'jquery'
	];
	private $handle = 'users-block';
	private $plugin_dir_path;
	private $plugin_file_path;
	private $css;
	private $js;
	private $js_fe;
	private $bootstrap_css;
	private $bootstrap_js;
	private $render_users_list;
	private $extend_user_rest;

	public function __construct( ExtendUserRest $extend_user_rest, RenderUsersList $render_users_list) {
		$this->plugin_dir_path = dirname( __FILE__, 2 );
		$this->plugin_file_path = $plugin_file_path = "{$this->plugin_dir_path}/plugin.php";
		$this->css = $this->get_asset_data( 'assets/dist/style_fe.css' );
		$this->js = $this->get_asset_data( 'assets/dist/main.js' );
		$this->js_fe = $this->get_asset_data( 'assets/dist/front-end.js' );
		$this->bootstrap_css = $this->get_asset_data( 'assets/dist/lib/bootstrap/css/bootstrap.min.css' );
		$this->bootstrap_js = $this->get_asset_data( 'assets/dist/lib/bootstrap/js/bootstrap.min.js' );
		$this->js = $this->get_asset_data( 'assets/dist/main.js' );
		$this->render_users_list = $render_users_list;
		$this->extend_user_rest = $extend_user_rest;
	}

	/**
	 * @return $this
	 */
	public function add_hooks() {
		// Add block assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		// Enqueue front end assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		// Register blocks.
		add_action( 'init',  array( $this, 'register_blocks' ) );
		$this->extend_user_rest->add_hooks();
		return $this;
	}

	/**
	 * @return void
	 * Add admin scripts
	 */
	public function enqueue_editor_assets() {

		$locale_data = wp_set_script_translations( $this->handle );

		wp_enqueue_script( $this->handle, $this->js['src'], self::DEPENDENCIES_BE, $this->js['ver'], false );
		wp_add_inline_script( $this->handle, sprintf( 'wp.i18n.setLocaleData( %s, "users-block" );', wp_json_encode( $locale_data ) ), 'before' );
	}

	/**
	 * Returns an array of src and version for a given relative asset file path.
	 *
	 * @param string $path
	 * @return array
	 */
	public function get_asset_data( $path ) {

		// Get built file URL.
		$src = plugins_url( $path, $this->plugin_file_path );

		// Use webpack dev server if SCRIPT_DEBUG or speific debug constant is true.
		if ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'USERS_BLOCK_DEBUG' ) && USERS_BLOCK_DEBUG ) ) {
			$ver    = null;
		} else {
			$ver = filemtime( "{$this->plugin_dir_path}/{$path}" );
		}

		return [
			'src' => $src,
			'ver' => $ver,
		];
	}

	/**
	 * Enqueue front end styles and scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->handle . '-bootstrap-js', $this->bootstrap_js['src'], self::DEPENDENCIES_FE, $this->bootstrap_js['ver'], true );
		wp_enqueue_script( $this->handle . '-bootstrap-js' );
		wp_register_script( $this->handle . '-fe', $this->js_fe['src'], [], $this->js_fe['ver'], true );
		wp_enqueue_script( $this->handle . '-fe' );
	}

	/**
	 * Enqueue front end styles and scripts.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		if( $this->bootstrap_css ){
			wp_register_style( $this->handle . '-bootstrap', $this->bootstrap_css['src'], $this->bootstrap_css['ver'] );
			wp_enqueue_style( $this->handle . '-bootstrap' );
		}

		wp_enqueue_style( $this->handle, $this->css['src'], [], $this->css['ver'] );
	}

	/**
	 * Register dynamic blocks here.
	 *
	 * @return void
	 */
	public function register_blocks() {
		// Placeholder for dynamic blocks.
		register_block_type( 'users-block/' . $this->handle, array(
			'render_callback' => array( $this->render_users_list, 'render_users_list_block' ),
			'editor_script' => $this->handle,
		) );
	}
}
