<?php
/*
Plugin Name: ThisIsMyJam Widget
Plugin URI: http://andrewnorcross.com/plugins/timj-widget
Description: Share your favorite song of the moment with This Is My Jam.
Version: 1.02
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

// add the widget
add_action( 'widgets_init', 'timj_register_widget' );

/**
 * register our custom widgets
 *
 * @return void
 *
 * @since 1.0
 */
function timj_register_widget() {
	register_widget( 'TIMJ_Recent_Jam_Widget' );
}

/**
 * our actual widget
 */
class TIMJ_Recent_Jam_Widget extends WP_Widget {

	/**
	 * [__construct description]
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'recent_jam', 'description' => __( 'Displays details of recent jam' ) );
		parent::__construct( 'recent_jam', __( 'TIMJ Recent Jam' ), $widget_ops );
		$this->alt_option_name = 'recent_jam';

		// load the CSS
		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			wp_enqueue_style( 'timj', plugins_url( '/css/timj.css', __FILE__ ), array(), null, 'all' );
		}
	}

	/**
	 * [widget description]
	 * @param  [type] $args     [description]
	 * @param  [type] $instance [description]
	 * @return [type]           [description]
	 */
	function widget( $args, $instance ) {

		// load variables with fallback conditionals
		$username   = ! empty( $instance['username'] ) ? $instance['username'] : 'TeamJamPicks';
		$show_text  = ! empty( $instance['show_text'] ) && $instance['show_text'] == 1 ? 'true' : 'false';
		$show_image = ! empty( $instance['show_image'] ) && $instance['show_image'] == 1 ? 'true' : 'false';
		$image_size = ! empty( $instance['image_size'] ) ? $instance['image_size'] : 'medium';

		// now do it
		echo $args['before_widget'];

		// set the title
		$title  = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );

		// output the title
		if ( ! empty( $title ) ) { echo $args['before_title'] . $title . $args['after_title']; };

		// output variables
		echo '<script src="https://www.thisismyjam.com/includes/js/medallion.js"></script>';
		echo '<script>Jam.Medallion.insert({ username:"' . esc_attr( $username ) . '", text:' . $show_text . ', image: ' . $show_image . ', imageSize:"' . $image_size . '" })</script>';

		// close the widget
		echo $args['after_widget'];
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']      = sanitize_text_field( $new_instance['title'] );
		$instance['username']   = sanitize_text_field( $new_instance['username'] );
		$instance['show_text']  = ! empty( $new_instance['show_text'] ) ? 1 : 0;
		$instance['show_image'] = ! empty( $new_instance['show_image'] ) ? 1 : 0;
		$instance['image_size'] = ! empty( $new_instance['image_size'] ) && in_array( $new_instance['image_size'], array( 'small', 'medium', 'large' ) ) ? sanitize_text_field( $new_instance['image_size'] ) : 'medium';

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {

		// parse some args
		$instance = wp_parse_args( (array) $instance, array(
			'title'         => __( 'Recent Jam' ),
			'username'      => 'TeamJamPicks',
			'show_text'     => 0,
			'show_image'    => 1,
			'image_size'    => 'medium'
		));

		// loop stuff
		foreach ( $instance as $field => $val ) {
			if ( isset($new_instance[$field]) )
				$instance[$field] = 1;
		}

		// others
		$title      = esc_attr( $instance['title'] );
		$username   = esc_attr( $instance['username'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget Title' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
		</p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked( $instance['show_text'], true ); ?> id="<?php echo $this->get_field_id( 'show_text' ); ?>" name="<?php echo $this->get_field_name( 'show_text' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_text' ); ?>"><?php _e( 'Display description below album' ); ?></label>
		</p>
		<p>
		<input class="checkbox" type="checkbox" <?php checked( $instance['show_image'], true ); ?> id="<?php echo $this->get_field_id( 'show_image' ); ?>" name="<?php echo $this->get_field_name( 'show_image' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Display album image' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Album Image Size' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="widefat">
				<option value="small"<?php selected( $instance['image_size'], 'small' ); ?>><?php _e( 'Small' ); ?></option>
				<option value="medium"<?php selected( $instance['image_size'], 'medium' ); ?>><?php _e( 'Medium' ); ?></option>
				<option value="large"<?php selected( $instance['image_size'], 'large' ); ?>><?php _e( 'Large' ); ?></option>
			</select>
		</p>


	<?php }

} // class

