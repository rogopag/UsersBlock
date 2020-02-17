var Inpsyde = Inpsyde || {};

Inpsyde.UsersBlock = Inpsyde.UsersBlock || {};

(function($){
	$(function(){
		Inpsyde.UsersBlock.frontEndHandler = new Inpsyde.UsersBlock.FrontEndHandler($);
	});
}(jQuery));

Inpsyde.UsersBlock.FrontEndHandler = function( $ ) {
	var self = this;
	/**
	 *
	 * @return {Inpsyde.UsersBlock.FrontEndHandler}
	 */
	self.init = function() {
		self.initUserModal().addEvents();
		return self;
	};
	/**
	 *
	 * @return {Inpsyde.UsersBlock.FrontEndHandler}
	 */
	self.initUserModal = function() {
		$('.inpsyde-users-block-modal').modal({
			show: false,
		});
		return self;
	};
	/**
	 *
	 * @return {Inpsyde.UsersBlock.FrontEndHandler}
	 */
	self.addEvents = function() {
		$('.inpsyde-users-block-user-box').on('click', function( event ) {
			var userId = $(this).data('user_id');
			$('#user-modal-'+userId).modal('toggle');
		});
		return self;
	};

	self.init();
};
