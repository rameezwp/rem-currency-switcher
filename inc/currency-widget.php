<?php
/**
* REM - Recent Properties Widget Class
* since 10.7.0
*/

class REM_Currency_Switcher_Widget extends WP_Widget {

	/**
	 * Register rem_recent_properties_widget widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rem_currency_switcher_widget', // Base ID
			__( 'REM - Currency Switcher', 'rem-currency-switcher' ), // Name
			array( 'description' => __( 'Displays Curency dropdown', 'rem-currency-switcher' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($instance);
		$savedOptions = get_option( 'rem_currency_options' );
		?>
			<div class="rem-currency-switcher ich-settings-main-wrap ">
			 	<?php
					if ( isset($instance['title']) ) {
						echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
					} 
					if ($savedOptions && is_array($savedOptions)) {
						$default_currency_code = rem_get_option('currency', 'USD');
					?>
		 			<div class="rem-widget-currency rem-widget-list-<?php echo get_the_id(); ?>">
						<form action="">
						<div class="form-group">
							<select name="currency" id="" class="form-control" onchange="this.form.submit()">
								<option value="<?php echo esc_attr( $default_currency_code ) ?>"><?php echo esc_html( rem_get_currency_name( $default_currency_code ) .' ('. rem_get_currency_symbol( $default_currency_code ) .')' )  ?></option>
								<?php foreach ($savedOptions as $data) { ?>
								<option value="<?php echo esc_attr( $data['code'] ) ?>"><?php echo esc_html( rem_get_currency_name( $data['code'] ) .' ('. rem_get_currency_symbol( $data['code'] ) .')' )  ?></option>
								<?php } ?>
							</select>
						</div>
						</form>
					</div>
					<?php } ?>
			</div>
		<?php
	}

	public function form( $instance ) {
		extract($instance);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title','rem-currency-switcher' ); ?></label> 
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text" value="<?php echo (isset($instance['title'])) ? $instance['title'] : '' ; ?>"
			>
		</p>
		
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

}

if (! function_exists ( 'rem_register_widget_currency_switcher' )) :
	function rem_register_widget_currency_switcher() {
	    register_widget( 'REM_Currency_Switcher_Widget' );
	}
endif;
add_action( 'widgets_init', 'rem_register_widget_currency_switcher' );
?>