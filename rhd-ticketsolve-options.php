<?php

/* Options page setup */

function rhd_ts_menu()
{
	add_options_page( 'Ticketsolve Shows', 'Ticketsolve Shows', 'manage_options', __FILE__, 'rhd_ts_settings_page');
}

function rhd_ts_settings_page()
{
?>
<div class="wrap">
	<div class="icon32" id="icon-options-general"><br/></div>
	<h2>Ticketsolve Shows</h2>

	<form method="post" action="options.php">

		<?php settings_fields( 'rhd_ts' );?>

		<?php do_settings_sections( __FILE__ );?>

		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

	</form>
</div>
<?php
}

function rhd_ts_init()
{
	register_setting( 'rhd_ts', 'rhd_ts_options' );
	add_settings_section('main_section', 'Main Settings', 'rhd_ts_section_main', __FILE__);
	add_settings_field('rhd_ts_subdomain', 'Account name', 'rhd_ts_field_subdomain', __FILE__, 'main_section');
	add_settings_field('rhd_ts_tag', 'Filter by show tag', 'rhd_ts_field_tag', __FILE__, 'main_section');
	add_settings_field('rhd_ts_category', 'Filter by show category', 'rhd_ts_field_category', __FILE__, 'main_section');
	add_settings_field('rhd_ts_count', 'Number of shows', 'rhd_ts_field_count', __FILE__, 'main_section');
	add_settings_field('rhd_ts_interval', 'Refresh rate', 'rhd_ts_field_interval', __FILE__, 'main_section');
}

function rhd_ts_section_main()
{
	echo "<p>Use the <a href=\"/wp-admin/widgets.php\">widget</a> or add <code>&lt;?php rhd_upcomingshows(); ?&gt;</code> to your theme code where you would like the list to appear.</p>";
}

function rhd_ts_field_subdomain()
{
	$options = get_option('rhd_ts_options');
	echo "<span class=\"description\">https://</span><input id='rhd_ts_subdomain' name='rhd_ts_options[subdomain]' size='20' type='text' value='{$options['subdomain']}' /><span class=\"description\">.ticketsolve.com/</span>";
}

function rhd_ts_field_tag()
{
	$options = get_option('rhd_ts_options');
	echo "<input id='rhd_ts_tag' name='rhd_ts_options[tag]' size='20' type='text' value='{$options['tag']}' />";
}

function rhd_ts_field_category()
{
	$options = get_option('rhd_ts_options');
	echo "<input id='rhd_ts_category' name='rhd_ts_options[category]' size='32' type='text' value='{$options['category']}' />";
}

function rhd_ts_field_count()
{
	$options = get_option('rhd_ts_options');
	echo "<span class=\"description\">Next </span><input id='rhd_ts_count' name='rhd_ts_options[count]' size='2' type='text' value='{$options['count']}' /><span class=\"description\"> shows. NB to keep server requests low, dates only available for 1st ten shows.</span>";
}

function rhd_ts_field_interval()
{
	$options = get_option('rhd_ts_options');
	echo "<span class=\"description\">Every </span><input id='rhd_ts_interval' name='rhd_ts_options[interval]' size='5' type='text' value='{$options['interval']}' /><span class=\"description\"> seconds. Show data is cached locally and only refreshed periodically.</span>";
}

function rhd_ts_defaults() 
{
	$arr = array(
		"subdomain" => "example", 
		"tag" => "",
		"category" => "",
		"count" => 5, 
		"interval" => 3600
		);

	update_option('rhd_ts_options', $arr);
}

?>