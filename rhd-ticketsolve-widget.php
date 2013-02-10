<?php

class RHD_TS_Widget extends WP_Widget 
{

	public function RHD_TS_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'box-office', 'description' => 'A list of forthcoming shows from your Ticketsolve box office.' );

		/* Widget control settings. */
//		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'rhd-ts' );

		/* Create the widget. */
		$this->WP_Widget( 'box-office', 'Box Office', $widget_ops );
	}

	public function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'Box Office') );

		$title = htmlspecialchars($instance['title']);
//		$lineOne = htmlspecialchars($instance['lineOne']);
//		$lineTwo = htmlspecialchars($instance['lineTwo']);

		# Output the options
		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('title') . '">' . __('Title:') . ' <input id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
		# Text line 1
//		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('lineOne') . '">' . __('Line 1 text:') . ' <input style="width: 200px;" id="' . $this->get_field_id('lineOne') . '" name="' . $this->get_field_name('lineOne') . '" type="text" value="' . $lineOne . '" /></label></p>';
		# Text line 2
//		echo '<p style="text-align:right;"><label for="' . $this->get_field_name('lineTwo') . '">' . __('Line 2 text:') . ' <input style="width: 200px;" id="' . $this->get_field_id('lineTwo') . '" name="' . $this->get_field_name('lineTwo') . '" type="text" value="' . $lineTwo . '" /></label></p>';
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
//		$instance['lineOne'] = strip_tags(stripslashes($new_instance['lineOne']));
//		$instance['lineTwo'] = strip_tags(stripslashes($new_instance['lineTwo']));

		return $instance;
	}

	public function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);

		# Before the widget
		echo $before_widget;

		# The title
		if ( $title )
		echo $before_title . $title . $after_title;

		rhd_upcomingshows();

		# After the widget
		echo $after_widget;
	}

}

function rhd_ts_widget_init()
{
	register_widget('RHD_TS_Widget');
}

?>