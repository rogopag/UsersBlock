import React from 'react';
import UserSelector from './components/UserSelector';

// Add the CSS
import './style.css';

const { Component } = wp.element;
// Get translation functions
const { __ } = wp.i18n;

// Get built in editor components
const {
	PlainText,
} = wp.editor;

export const name = 'users-block/users-block';
/**
 *
 * @type {{keywords: [*], edit: settings.edit, icon: string, save(): null, description: *, attributes: {blockTitle: {default: string, type: string}, selectedUsers: {default: [], type: array}}, title: *, category: string}}
 */
export const settings = {

	// The title shown in the block picker
	title: __( 'Users Block', 'users-block' ),

	// A more detailed description
	description: __( 'A block to retrieve and display users infos', 'users-block' ),

	// The icon, from the list of WP default dashicons
	// https://developer.wordpress.org/resource/dashicons/#admin-users
	icon: 'admin-users',

	// The category is the section of the block picker where this shows
	category: 'widgets',

	// Keywords help users search for & find a block
	keywords: [
		__( 'custom block', 'users-block' ),
	],

	// Attributes define the data sources for the block
	attributes: {
		blockTitle: {
			type: 'string',
			default: ''
		},
		selectedUsers: {
			type: 'array',
			default: []
		},
	},
	/**
	 * @return {void}
	 * The block in edit mode
	 */
	edit: class extends Component {
		constructor(props) {
			super(...arguments);
			this.props = props;

			this.onTitleChange = this.onTitleChange.bind(this);
			this.updateSelectedUsers = this.updateSelectedUsers.bind(this);
		}

		onTitleChange(blockTitle) {
			this.props.setAttributes({ blockTitle });
		}

		updateSelectedUsers( selectedUsers ) {
			this.props.setAttributes({ selectedUsers: selectedUsers });
		}

		render() {
			const { className, attributes: { blockTitle = '' } = {} } = this.props;

			return (
				<div className={className}>
					<div className="title-wrapper">
						<PlainText
							value={blockTitle}
							onChange={this.onTitleChange}
						/>
					</div>
					<UserSelector
						selectedUsers={this.props.attributes.selectedUsers}
						updateSelectedUsers={this.updateSelectedUsers}
					/>
				</div>
			);
		}
	},
	/**
	 *
	 * @return {null}
	 */
	save() {
		return null;
	}

};
