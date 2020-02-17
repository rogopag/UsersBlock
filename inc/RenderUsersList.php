<?php
namespace Inpsyde\UsersBlock;

class RenderUsersList{
	private $selected_users;
	private $block_title = '';
	private $users_objects;
	private $user_query;

	public function __construct( \WP_User_Query $user_query ) {
		$this->user_query = $user_query;
		add_filter( 'inpsyde_render_users_list_block', array( $this, 'render_users_list_block_callback'), 10, 3 );
	}

	public function render_users_list_block( $attributes ) {
		$this->set_block_title( isset( $attributes['blockTitle'] ) ? $attributes['blockTitle'] : '' );
		$this->set_selected_users( isset( $attributes['selectedUsers'] ) ? $attributes['selectedUsers'] : [] );

		$users = $this->get_selected_users();

		if( empty( $users ) ) return apply_filters( 'inpsyde_render_users_list_block', [], $this->block_title );

		$this->set_user_query_query( $users );

		$this->set_users_objects( $this->get_user_query_results( ) );

		return apply_filters( 'inpsyde_render_users_list_block', '', $this->get_users_objects(), $this->block_title );
	}

	public function get_user_query(){
		return $this->user_query;
	}

	/**
	 * @return mixed
	 */
	public function get_selected_users() {
		return $this->selected_users;
	}
	/**
	 * @param mixed $selected_users
	 */
	public function set_selected_users( $selected_users ) {
		$this->selected_users = $selected_users;
	}

	/**
	 * @param string $block_title
	 */
	public function set_block_title( $block_title ) {
		$this->block_title = $block_title;
	}

	/**
	 * @param mixed $users_objects
	 */
	public function set_users_objects( $users_objects ) {
		$this->users_objects = $users_objects;
	}

	/**
	 * @return mixed
	 */
	public function get_users_objects() {
		return $this->users_objects;
	}

	/**
	 * @param array $args
	 */
	public function set_user_query_query( $user_ids = [] ) {
		$this->get_user_query()->prepare_query( array( 'include' => $user_ids ) );
	}

	/**
	 * @return array
	 */
	public function get_user_query_results( ) {
		$result = $this->user_query_do_query()->get_results( );
		return !empty( $result ) ? $result : [];
	}

	/**
	 * @return \WP_User_Query
	 */
	public function user_query_do_query() {
		$this->get_user_query()->query();
		return $this->get_user_query();
	}

	/**
	 * @param $user_objects
	 * @param $block_title
	 *
	 * @return string
	 */
	public function render_users_list_block_callback( $html = '', $user_objects = null, $block_title = '' ) {
		if( ! is_array( $user_objects ) || empty( $user_objects ) ){
			return "<div class='users-warning-container'>No users data found!</div>";
		}

		$title = $block_title ? "<h4>{$block_title}</h4>" : '';
		foreach( $user_objects as $user ) {
			$user_id = $user->get('id');
			$user_name = $this->get_user_formatted_name( $user );
			$avatar_html = $this->get_user_figure( $user_id, $user_name );
			$html .= $this->get_user_box( $user_id, $user_name, $avatar_html );
			$html .= $this->get_user_modal( $user_id, $user_name, $user, $avatar_html );
		}

		return "{$title}<div class='users-container'>{$html}</div>";
	}

	/**
	 * @param $user_id
	 * @param $user_name
	 *
	 * @return string
	 */
	public function get_user_figure( $user_id, $user_name ) {
		$avatar = get_avatar_url( $user_id );
		$avatar_html = "<div class='user-figure-box'><figure class='avatar-box'><img src='{$avatar}' alt='{$user_name}' /></figure></div>";
		return $avatar_html;
	}

	/**
	 * @param $user
	 *
	 * @return string
	 */
	public function get_user_formatted_name( \WP_User $user ) {
		return $user->get('first_name') && $user->get('last_name') ? $user->get('first_name') . ' ' . $user->get('last_name') : $user->get('display_name');
	}

	/**
	 * @param $user_id
	 * @param $user_name
	 * @param $avatar_html
	 *
	 * @return string
	 */
	public function get_user_box( $user_id, $user_name, $avatar_html ) {
		$name_box = "<div class='user-name-box'>{$user_name}</div>";
		$html = "<div id='user-box-{$user_id}' class='user-box inpsyde-users-block-user-box' data-toggle='modal' data-target='user-modal-{$user_id}' data-user_id='{$user_id}'>{$avatar_html}{$name_box}</div>";
		return $html;
	}

	/**
	 * @param $user_id
	 * @param $user_name
	 * @param $user
	 *
	 * @return false|string
	 */
	private function get_user_modal( $user_id, $user_name, \WP_User $user, $avatar_figure ) {
		ob_start();
		?>
		<div id="user-modal-<?php echo $user_id;?>" class="modal fade inpsyde-users-block-modal" role="dialog">
			<div class="modal-dialog modal-md">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close float-right" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><?php echo $avatar_figure;?><div class="user-name-box"><?php echo $user_name;?></div></h4>
					</div>
					<div class="modal-body">
						<?php echo $this->print_user_fields_box( $user, $user_name );?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * @param $user
	 * @param $user_name
	 *
	 * @return string
	 */
	private function print_user_fields_box( \WP_User  $user, $user_name ) {
		$fields = ExtendUserRest::USER_ADDITIONAL_FIELDS;
		$html = '';
		$have_social_title = false;
		$close = '';
		$count_fields = count($fields);
		$count = 0;
		foreach( $fields as $index => $field ) {
			$count++;
			$meta = $user->get( $field );
			if( $meta ) {
				$label = ucfirst($field);
				if( $index === 0 ){
					$html .= "<li class='user-{$field} user-info-field'><span class='user-field-title'>{$label}:</span><span class='user-field-value'> {$meta}</span></li>";
				} else {

					if( !$have_social_title ) {
						$title = "<li class='user-{$field} user-info-field-title'><span class='user-social-media-title'>Social Media Links:</span></li>";
						$have_social_title = true;
					} else {
						$title = "";
					}

					if( $count === $count_fields ) {
						$close = "</li>";
					}

					$html .= "{$title}<ul><li class='user-{$field} user-info-field'><span class='user-field-title'>{$label}:</span><span class='user-field-value'> {$meta}:</span></li></ul>{$close}";

				}
			}
		}
		return empty($html) ? "<div class='users-warning-container'>There are no custom infos for {$user_name} user!</div>" : "<ul class='user-info-list'>{$html}</ul>";
	}
}
