<div class="wrap">
	<?php $savedSettings = get_option( 'rem_currency_settings' ); ?>
	<h2>REM - Currency Switcher</h2>
	<div class="ich-settings-main-wrap">
		<div class="alert alert-info">
			Real Estate Manager's default currency is set as <strong><?php echo $default_currency_code = rem_get_option('currency', 'USD'); ?></strong> and you don't need to add this to the list below. You can configure the default currency <a href="<?php echo admin_url('edit.php?post_type=rem_property&page=rem_settings'); ?>">here</a>.
		</div>
		<table class="table table-bordered">
			<tr>
				<th>Provider</th>
				<th>API Key</th>
				<th>Schedule (When to auto update)</th>
				<th>Switcher in Menu</th>
			</tr>
			<tr>
				<td>
					<select class="form-control api-provider">
						<option value="currencyapi" <?php echo (isset($savedSettings['provider']) && $savedSettings['provider'] == 'currencyapi') ? 'selected' : ''; ?>>currencyapi.com</option>
					</select>
				</td>
				<td>
					<input type="text" class="form-control api-key" value="<?php echo (isset($savedSettings['api'])) ? $savedSettings['api'] : '' ?>">
				</td>
				<td>
					<select class="form-control schedule">
						<option value="hourly" <?php echo (isset($savedSettings['schedule']) && $savedSettings['schedule'] == 'hourly') ? 'selected' : ''; ?>>Hourly</option>
						<option value="twicedaily" <?php echo (isset($savedSettings['schedule']) && $savedSettings['schedule'] == 'twicedaily') ? 'selected' : ''; ?>>Twice a Day</option>
						<option value="daily" <?php echo (isset($savedSettings['schedule']) && $savedSettings['schedule'] == 'daily') ? 'selected' : ''; ?>>Once in a Day</option>
					</select>
				</td>
				<td>
					<select class="form-control menu-switcher">
						<option value="disable" <?php echo (isset($savedSettings['menu']) && $savedSettings['menu'] == 'disable') ? 'selected' : ''; ?>>Disable</option>
						<option value="enable" <?php echo (isset($savedSettings['menu']) && $savedSettings['menu'] == 'enable') ? 'selected' : ''; ?>>Enable</option>
					</select>
				</td>
			</tr>
		</table>
		<table class="table table-bordered currency-table table-fixed">
			<tr>
				<th><?php esc_attr_e( 'Currency', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Currency Position', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Thousand Separator', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Decimal Separator', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Number of Decimals', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Rate', 'rem-currency-switcher' ); ?></th>
				<th><?php esc_attr_e( 'Action', 'rem-currency-switcher' ); ?></th>
			</tr>
			<?php
				$savedOptions = get_option( 'rem_currency_options' );
				if ($savedOptions && is_array($savedOptions)) {
					foreach ($savedOptions as $data) { ?>
					<tr class="currency-options">
						<td>
		                    <select class="currency-code form-control">
		                        <option value=""><?php _e( 'Choose a currency&hellip;', 'real-estate-manager' ); ?></option>
		                        <?php
		                        foreach ( rem_get_all_currencies() as $code => $name ) {
		                            echo '<option value="' . esc_attr( $code ) . '" ' . selected( $data['code'], $code, false ) . '>' . esc_html( $name . ' (' . rem_get_currency_symbol( $code ) . ')' ) . '</option>';
		                        }
		                        ?>
		                    </select>
						</td>
						<td>
							<select class="form-control currency-position">
								<option value="left" <?php echo ($data['position'] == 'left') ? 'selected' : ''; ?>>Left</option>
								<option value="right" <?php echo ($data['position'] == 'right') ? 'selected' : ''; ?>>Right</option>
								<option value="left_space" <?php echo ($data['position'] == 'left_space') ? 'selected' : ''; ?>>Left with Space</option>
								<option value="right_space" <?php echo ($data['position'] == 'right_space') ? 'selected' : ''; ?>>Right with Space</option>
							</select>
						</td>
						<td>
							<input type="text" class="form-control currency-sep-t" value="<?php echo $data['tsep']; ?>">
						</td>
						<td>
							<input type="text" class="form-control currency-sep-d" value="<?php echo $data['dsep']; ?>">
						</td>
						<td>
							<input type="number" class="form-control currency-decimals" value="<?php echo $data['decimals']; ?>">
						</td>
						<td>
							<input type="text" disabled class="form-control currency-rate" value="<?php echo $data['rate']; ?>">
						</td>
						<td>
							<a href="#" class="btn btn-danger delete-btn">Remove</a>
							<a href="#" class="btn btn-info add-btn">Add</a>
						</td>
					</tr>
					<?php }
				} else { ?>
					<tr class="currency-options">
						<td>
		                    <select class="currency-code form-control">
		                        <option value=""><?php _e( 'Choose a currency&hellip;', 'real-estate-manager' ); ?></option>
		                        <?php
		                        foreach ( rem_get_all_currencies() as $code => $name ) {
		                            echo '<option value="' . esc_attr( $code ) . '" ' . selected( $field_value, $code, false ) . '>' . esc_html( $name . ' (' . rem_get_currency_symbol( $code ) . ')' ) . '</option>';
		                        }
		                        ?>
		                    </select>
						</td>
						<td>
							<select class="form-control currency-position">
								<option value="left" selected="">Left</option>
								<option value="right">Right</option>
								<option value="left_space">Left with Space</option>
								<option value="right_space">Right with Space</option>
							</select>
						</td>
						<td>
							<input type="text" class="form-control currency-sep-t">
						</td>
						<td>
							<input type="text" class="form-control currency-sep-d">
						</td>
						<td>
							<input type="number" class="form-control currency-decimals">
						</td>
						<td>
							
						</td>
						<td>
							<a href="#" class="btn btn-danger delete-btn">Remove</a>
							<a href="#" class="btn btn-info add-btn">Add</a>
						</td>
					</tr>
				<?php } ?>
		</table>
		<a href="#" class="btn btn-success save-btn">Save Changes</a>
	</div>
</div>