<?php
/**
 * Main class to register menu and handling front conversion
 */
class REM_Currency_Switcher
{
	
	function __construct(){
		add_action( 'admin_menu', array( $this, 'menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts' ) );
		add_action( 'wp_ajax_rem_currency_options_save', array($this, 'save_currency_options' ) );
        add_action( 'rem_currency_switcher_live_fetch', array($this, 'fetch_live_rates' ) );
		add_filter( 'rem_property_price', array($this, 'render_converted_price'), 20, 4 );

        add_filter('wp_nav_menu_items', array($this, 'currency_switcher_menu'), 10, 2);
	}

    function currency_switcher_menu( $items, $args ) {
        $savedCurrencies = get_option( 'rem_currency_options' );
        $settings = get_option( 'rem_currency_settings' );

        if (isset($settings['menu']) && $settings['menu'] == 'enable') {
            $currencies = rem_get_all_currencies();
            if ($savedCurrencies && is_array($savedCurrencies)) {
                $default_currency_code = rem_get_option('currency', 'USD');
            
            if ($savedCurrencies && is_array($savedCurrencies)) {
                $default_currency_code = rem_get_option('currency', 'USD');
                ob_start();
            ?>
            <li class="menu-item rem-currency-switcher-menu">
                <form action="">
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
                </form>
            </li>
            <?php }
                $html = ob_get_clean();
            }

            return $items.$html;
            
        } else {
            return $items;
        }
    }
	function menu_pages(){
		add_submenu_page( 'edit.php?post_type=rem_property', 'Currency Switcher', __( 'Currency Switcher', 'rem-currency-switcher' ), 'manage_options', 'rem_currency_switcher', array($this, 'render_cs_menu_page') );
	}

	function render_cs_menu_page(){
		include_once REM_CS_PATH. '/inc/settings.php';
	}

    function admin_scripts($check){
    	if ($check == 'rem_property_page_rem_currency_switcher') {
            wp_enqueue_script( 'sweet-alerts', REM_URL . '/assets/admin/js/sweetalert.min.js' , array('jquery'));
    		wp_enqueue_style( 'rem-bootstrap', REM_URL . '/assets/admin/css/bootstrap.min.css' );
    		wp_enqueue_style( 'font-awesome-rem', REM_URL . '/assets/front/css/font-awesome.min.css' );
    		wp_enqueue_script( 'rem-cs', REM_CS_URL . '/js/settings.js'  , array('jquery'));
    	}
    }

    function save_currency_options(){
    	if (isset($_REQUEST['data'])) {
    		update_option( 'rem_currency_options', $_REQUEST['data'] );
    		echo 'Settings Saved';
    	}

    	if (isset($_REQUEST['settings'])) {
    		update_option( 'rem_currency_settings', $_REQUEST['settings'] );
    	}

        if (isset($_REQUEST['settings']['schedule'])) {
            wp_clear_scheduled_hook( 'rem_currency_switcher_live_fetch' );
            wp_schedule_event( time(), $_REQUEST['settings']['schedule'], 'rem_currency_switcher_live_fetch' );
        }
        
        $this->fetch_live_rates();
    	die(0);
    }

    function render_converted_price($return, $price, $args, $price_digits = ''){
    	if (isset($_GET['rem_currency']) && $_GET['rem_currency'] != '') {
    		$code = $_GET['rem_currency'];
            $currencies = get_option( 'rem_currency_options' );
    		if (isset($currencies[$code]['rate'])) {
                $new_price =  $price_digits * $currencies[$code]['rate'];
    			$formatted_price = $this->get_formatted_price($new_price, $code);
    			return $formatted_price;
            } else {
                return $return;
            }
    	} else {
    		return $return;
    	}
    }

    function fetch_live_rates(){
        $settings =  get_option( 'rem_currency_settings' );
        $currencies = get_option( 'rem_currency_options' );
        $api = $settings['api'];
        $provider = $settings['provider'];
        foreach ($currencies as $to_currency => $data) {
            $rate = $this->get_from_provider($provider, $to_currency, $api);
            if ($rate) {
                $currencies[$to_currency]['rate'] = $rate;
            } else {
                $currencies[$to_currency]['rate'] = 1;
            }
        }
        update_option('rem_currency_options', $currencies);
    }

    function get_from_provider($provider, $to_currency, $api){
        $from_currency = urlencode(rem_get_option('currency', 'USD'));
        $to_currency = urlencode($to_currency);

		switch ($provider) {
			case 'yahoo':
                $date = current_time('timestamp', true);
                $yql_query_url = 'https://query1.finance.yahoo.com/v8/finance/chart/' . $from_currency . $this->escape($to_currency) . '=X?symbol=' . $from_currency . $this->escape($to_currency) . '%3DX&period1=' . ( $date - 60 * 86400 ) . '&period2=' . $date . '&interval=1d&includePrePost=false&events=div%7Csplit%7Cearn&lang=en-US&region=US&corsDomain=finance.yahoo.com';
                if (function_exists('curl_init')) {
                    $res = $this->file_get_contents_curl($yql_query_url);
                } else {
                    $res = file_get_contents($yql_query_url);
                }

                $data = json_decode($res, true);
                $result = isset($data['chart']['result'][0]['indicators']['quote'][0]['open']) ? $data['chart']['result'][0]['indicators']['quote'][0]['open'] : ( isset($data['chart']['result'][0]['meta']['previousClose']) ? array($data['chart']['result'][0]['meta']['previousClose']) : array() );

                if (count($result) && is_array($result)) {
                    $request = end($result);
                }
				break;

            case 'google':
                $url = 'https://www.google.com/async/currency_update?yv=2&async=source_amount:1,source_currency:' . $from_currency . ',target_currency:' . $to_currency . ',chart_width:270,chart_height:94,lang:en,country:vn,_fmt:jspb';
                $html = $this->get_response($url);
                if ($html) {
                    preg_match('/CurrencyUpdate\":\[\[(.+?)\,/', $html, $matches);

                    if (count($matches) > 0) {
                        $request = isset($matches[1]) ? $matches[1] : 1;
                    } else {
                        $request = false;
                    }
                }

                break;

            case 'appspot':
                $url = 'http://rate-exchange.appspot.com/currency?from=' . $from_currency . '&to=' . $to_currency;

                if (function_exists('curl_init')) {
                    $res = $this->file_get_contents_curl($url);
                } else {
                    $res = file_get_contents($url);
                }


                $res = json_decode($res);
                if (isset($res->rate)) {
                    $request = floatval($res->rate);
                } else {
                    $request = false;
                }
                break;

			case 'free-currency':
                $query_str = sprintf("%s_%s", $from_currency, $to_currency);
                $key = $api;
                if (!$key) {
                    $request = esc_html__("Please use the API key", 'rem-currency-switcher');
                    break;
                }
                $url = "http://free.currencyconverterapi.com/api/v3/convert?q={$query_str}&compact=y&apiKey={$key}";

                if (function_exists('curl_init')) {
                    $res = $this->file_get_contents_curl($url);
                } else {
                    $res = file_get_contents($url);
                }

                $currency_data = json_decode($res, true);

                if (!empty($currency_data[$query_str]['val'])) {
                    $request = $currency_data[$query_str]['val'];
                } else {
                    $request = false;
                }
				break;
			
			default:
				
				break;

		}

		return $request;
    }

	function get_formatted_price( $price, $code ) {
		$saved_options = get_option( 'rem_currency_options' );

		if(isset($saved_options[$code])){
			$settings = $saved_options[$code];
			$decimals = $settings['decimals'];
			$decimal_separator = $settings['dsep'];
			$thousand_separator = $settings['tsep'];
			$price_format = $this->get_price_format($settings['position']);
			$price   = apply_filters( 'formatted_rem_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

			if ( apply_filters( 'rem_price_trim_zeros', false ) && $decimals > 0 ) {
				$price = wc_trim_zeros( $price );
			}

			$formatted_price = sprintf( $price_format, '<span class="rem-currency-symbol">' . rem_get_currency_symbol( $code ) . '</span>', $price );
			$return          = '<span class="rem-price-amount">' . $formatted_price . '</span>';

			return $return;
		}
	}

	function get_price_format($currency_pos) {
		$format = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left' :
				$format = '%1$s%2$s';
			break;
			case 'right' :
				$format = '%2$s%1$s';
			break;
			case 'left_space' :
				$format = '%1$s&nbsp;%2$s';
			break;
			case 'right_space' :
				$format = '%2$s&nbsp;%1$s';
			break;
		}

		return $format;
	}

    public function file_get_contents_curl($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function escape($value) {
        return sanitize_text_field(esc_html($value));
    }
}
?>