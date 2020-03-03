# Gutenberg Users Block

A basic Gutenberg plugin to add users widgets in a page/post: you can select multiple users by name and picture and display them in a grid + modal in your post content.

## Features

- Webpack config and commands
- Defined as composer package one can get and run
- Test environment with phpunit and WP Mocks
- Bootstrap 4 to display users info in a simple modal
- 50 users + meta data to test the plugin in dev mode

## Usage

Clone from "git clone https://rogopag@bitbucket.org/rogopag/users-block.git" and then "composer install" on a Wordpress install. Activate plugin from wp-admin/plugins

### Prerequisites

1. Composer, Wordpress, php7

### Setting up and developing

1. `cd` to your plugins directory in your WordPress install
1. Run `git clone https://rogopag@bitbucket.org/rogopag/users-block.git`
1. `cd users-block`
1. `composer install`
1. `npm run-script dev`

1. Add `define( 'SCRIPT_DEBUG', true );` to your wp config (make sure you don't put this live!)
1. `npm run start` to start the dev server.
