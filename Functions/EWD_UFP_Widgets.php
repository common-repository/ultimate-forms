<?php
class UFP_Contact_Form extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		parent::__construct(
			'ufp_contact_form_widget', // Base ID
			__('Contact Form', 'ultimate-forms'), // Name
			array( 'description' => __( 'Add a contact form', 'ultimate-forms' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		/*if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo __( 'Hello, World!', 'UPCP' );*/
		echo do_shortcode("[ultimate-forms form_id='". $instance['form_id'] . "']");
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$Forms = get_posts(array('post_type' => 'ufp_form', 'posts_per_page' => -1));

		$form_id = ! empty( $instance['form_id'] ) ? $instance['form_id'] : 0;
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'form_id' ); ?>"><?php _e( 'Form to display:', 'ultimate-forms' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'form_id' ); ?>" name="<?php echo $this->get_field_name( 'form_id' ); ?>">;
			<?php foreach ($Forms as $Form) { ?>
				<option value='<?php echo $Form->ID; ?>' <?php echo $form_id == $Form->ID ? 'selected' : '';  ?>><?php echo $Form->post_title; ?></option>
			<?php } ?>
		</select>
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['form_id'] = ( ! empty( $new_instance['form_id'] ) ) ? strip_tags( $new_instance['form_id'] ) : '';
		
		return $instance;
	}
}

function UFP_Register_Contact_Form() {
	return register_widget("UFP_Contact_Form");
}
add_action('widgets_init', 'UFP_Register_Contact_Form');



