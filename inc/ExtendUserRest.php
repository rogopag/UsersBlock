<?php

namespace Inpsyde\UsersBlock;

/**
 * Class ListUsers
 *
 * @package Inpsyde\UsersBlock
 * Extends REST API wp_json/wp/v2/users endpoint to support those custom fields natively for user REST API queries:
 * "first_name",
 * "last_name",
 * "github",
 * "linkedin",
 * "facebook",
 * "position",
 * "xing"
 *
 *
 */
class ExtendUserRest{
	// custom user fields to add to rest api call
	const USER_ADDITIONAL_FIELDS = [
			"position",
			"github",
			"linkedin",
			"facebook",
			"xing",
		];
	// default user fields to add to rest api call
	const USER_DEFAULT_FIELDS = [
		"first_name",
		"last_name",
	];
	/**
	 * ListUsers constructor.
	 */
	public function __construct() {

	}

	/**
	 * @return $this
	 */
	public function add_hooks() {
		add_filter( 'rest_user_query' , array( $this, 'custom_rest_user_query' ) );
		add_action( 'rest_api_init', array( $this, 'adding_user_metas_rest' ), 99 );
		return $this;
	}

	/**
	 * @param $prepared_args
	 * @param null $request
	 *
	 * @return mixed
	 */
	public function custom_rest_user_query( $prepared_args, $request = null ) {
		unset($prepared_args['has_published_posts']);
		return $prepared_args;
	}

	/**
	 * @return @void
	 * Add user fields to REST API calls
	 */
	public function adding_user_metas_rest() {
		$fields = self::USER_ADDITIONAL_FIELDS;
		foreach( $fields as $field ) {
			register_rest_field( 'user',
				$field,
				array(
					'get_callback'      => array( $this, 'user_meta_callback' ),
					'update_callback'   => array( $this, 'update_user_meta_callback' ),
					'schema'            => null,
				)
			);
		}
		$fields = self::USER_DEFAULT_FIELDS;
		foreach( $fields as $field ) {
			register_meta( 'user',
				$field,
				array(
					'type'      => 'string',
					'single'   => true,
					'show_in_rest'            => true,
				)
			);
		}
	}

	/**
	 * @param $user
	 * @param $field_name
	 * @param $request
	 *
	 * @return mixed
	 */
	public function user_meta_callback( $user, $field_name, $request) {
		$meta = get_user_meta( $user[ 'id' ], $field_name, true );
		return $meta;
	}

	/**
	 * @param $user
	 * @param $meta_key
	 * @param $meta_value
	 * @param $prev_value
	 *
	 * @return bool|int
	 */
	public function update_user_meta_callback( $user, $meta_key, $meta_value, $prev_value ) {
		$ret = update_user_meta( array( $user, $meta_key, $meta_value, $prev_value ) );
		return $ret;
	}
}
