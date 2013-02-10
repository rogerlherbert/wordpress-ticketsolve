<?php
/*
Plugin Name: Ticketsolve Shows
Plugin URI: http://wordpress.org/extend/plugins/upcoming-ticketsolve-shows/
Description: Loads future shows from your <a href="http://www.ticketsolve.com/">Ticketsolve</a> box office server.
Version: 1.1
Author: Roger Herbert
Author URI: http://twitter.com/junap
License: GPL2

Copyright 2011  Roger Herbert  (email : rogerlherbert@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include 'rhd-ticketsolve-core.php';

include 'rhd-ticketsolve-options.php';

include 'rhd-ticketsolve-widget.php';

register_activation_hook( __FILE__ , 'rhd_ts_defaults');

add_action('admin_menu', 'rhd_ts_menu');
add_action('admin_init', 'rhd_ts_init');
add_action('widgets_init', 'rhd_ts_widget_init' );

?>