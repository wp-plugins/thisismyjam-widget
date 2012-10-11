<?php
/*
Plugin Name: ThisIsMyJam Widget
Plugin URI: http://andrewnorcross.com/plugins/timj-widget
Description: Creates a widget provided by ThisIsMyJam.com with available options.
Version: 1.0
Author: Andrew Norcross
Author URI: http://andrewnorcross.com
License: GPL v2

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

class rkv_recent_jams extends WP_Widget {
	function rkv_recent_jams() {
		$widget_ops = array( 'classname' => 'recent_jam', 'description' => 'Displays details of recent jam' );
		$this->WP_Widget( 'recent_jam', 'TIMJ Recent Jam', $widget_ops );

        if ( is_active_widget(false, false, $this->id_base, true) )
        	wp_enqueue_style( 'timj', plugins_url('/css/timj.css', __FILE__), array(), null, 'all' );

	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		// load variables with fallback conditionals
		$username	= empty($instance['username'])										? 'jamoftheday'	: $instance['username'];
		$show_text	= isset($instance['show_text'])  && $instance['show_text'] == 1		? 'true'		: 'false';
		$show_image	= isset($instance['show_image']) && $instance['show_image'] == 1	? 'true'		: 'false';
		$image_size	= empty($instance['image_size'])									? 'medium'		: $instance['image_size'];

		// output variables
		echo '<script src="http://www.thisismyjam.com/includes/js/medallion.js"></script>';
		echo '<script>Jam.Medallion.insert({username: "'.$username.'",text: '.$show_text.', image: '.$show_image.',imageSize: "'.$image_size.'"})</script>';

		echo $after_widget;

		?>
        
        <?php }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
	$instance = $old_instance;
	$instance['title']		= strip_tags($new_instance['title']);
	$instance['username']	= strip_tags($new_instance['username']);
	$instance['show_text']	= !empty($new_instance['show_text'])	? 1 : 0;
	$instance['show_image']	= !empty($new_instance['show_image'])	? 1 : 0;
		if ( in_array( $new_instance['image_size'], array( 'small', 'medium', 'large' ) ) ) {
			$instance['image_size'] = $new_instance['image_size'];
		} else {
			$instance['image_size'] = 'medium';
		}
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $instance = wp_parse_args( (array) $instance, array( 
			'title'			=> 'Recent Jam',
			'username'		=> '',
			'show_text'		=> 0,
			'show_image'	=> 1,
			'image_size'	=> 'medium'
			));
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}        
		$title		= esc_attr( $instance['title'] );
		$username	= esc_attr( $instance['username'] );
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
		</p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_text'], true) ?> id="<?php echo $this->get_field_id('show_text'); ?>" name="<?php echo $this->get_field_name('show_text'); ?>" />
		<label for="<?php echo $this->get_field_id('show_text'); ?>"><?php _e('Display description below album'); ?></label>
        </p>
        <p>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_image'], true) ?> id="<?php echo $this->get_field_id('show_image'); ?>" name="<?php echo $this->get_field_name('show_image'); ?>" />
		<label for="<?php echo $this->get_field_id('show_image'); ?>"><?php _e('Display album image'); ?></label>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e( 'Album Image Size' ); ?></label>
			<select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>" class="widefat">
				<option value="small"<?php selected( $instance['image_size'], 'small' ); ?>><?php _e('Small'); ?></option>
				<option value="medium"<?php selected( $instance['image_size'], 'medium' ); ?>><?php _e('Medium'); ?></option>
				<option value="large"<?php selected( $instance['image_size'], 'large' ); ?>><?php _e( 'Large' ); ?></option>
			</select>
		</p>


		<?php }

} // class 

add_action( 'widgets_init', create_function( '', "register_widget('rkv_recent_jams');" ) );

function timj_style() {
	echo '<style media="screen" type="text/css">img.jam-jamvatar { margin: 0 auto; display: block;}</style>';
}
	
/*
	// check for active widget, and load CSS to center image
	function check_widget() {
    	if( is_active_widget( '', '', 'recent_jam' ) ) // check if search widget is used
       		wp_enqueue_style( 'timj', plugins_url('/css/timj.css', __FILE__), array(), null, 'all' );
    	}
	add_action( 'init', 'check_widget' );
*/	
