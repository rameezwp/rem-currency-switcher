<?php
/**
 * Adds REM_Tags_Cloud_W widget.
 */
class REM_Currency_Switcher_Widget extends WP_Widget {

	/**
	 * Register rem_currency_switcher_widget widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rem_currency_switcher_widget', // Base ID
			__( 'REM - Currency Switcher', 'rem-currency-switcher' ),
			array( 'description' => __( 'Displays currency switcher', 'rem-currency-switcher' ), )
		);
	}

	public function widget( $args, $instance ) {

		extract($instance);
		$savedCurrencies = get_option( 'rem_currency_options' );
		$currencies = rem_get_all_currencies();

		echo '<div class="rem-currency-switcher ich-settings-main-wrap">';
		if ( isset($instance['title']) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		if ($savedCurrencies && is_array($savedCurrencies)) {
			$default_currency_code = rem_get_option('currency', 'USD');
		?>
			<div class="rem-widget-currency rem-currency-widget-<?php echo get_the_id(); ?>">
			<form action="">
				<div class="form-group">
					<select name="rem_currency" id="" class="form-control" onchange="this.form.submit()">
						<option value=""><?php _e( 'Default Currency', 'rem-currency-switcher' ) ?></option>
						<?php foreach ($currencies as $code => $label) {
							if (array_key_exists($code, $savedCurrencies)) { ?>
								<option value="<?php echo esc_attr( $code ) ?>" <?php echo (isset($_GET['rem_currency']) && $_GET['rem_currency'] == $code) ? 'selected' : '' ; ?>>
									<?php echo esc_html( $label );  ?>
									( <?php echo rem_get_currency_symbol( $code ); ?> )
								</option>								
							<?php }
						} ?>
					</select>
				</div>
			</form>
		</div>
		<?php }
		echo '</div>';
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

if (! function_exists ( 'rem_currency_switcher_widget' )) :
	function rem_currency_switcher_widget() {
	    register_widget( 'REM_Currency_Switcher_Widget' );
	}
endif;
add_action( 'widgets_init', 'rem_currency_switcher_widget' );

?>