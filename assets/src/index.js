/**
 * Register Blocks.
 */

// Get block functions
const { registerBlockType } = wp.blocks;

// Import our blocks
import * as usersBlock from './block';

// Create a list of blocks to loop through
const blocks = [
	usersBlock,
];

// Register the blocks
blocks.forEach( ( { name, settings } ) => {
	registerBlockType( name, settings );
} );

/**
 * Register Plugins.
 */

// Get plugin functions
const { registerPlugin } = wp.plugins;

import * as usersBlockPlugin from './plugin';

const plugins = [
	usersBlockPlugin,
];

plugins.forEach( ( { name, settings } ) => {
	registerPlugin( name, settings );
} );
