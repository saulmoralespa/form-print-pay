<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 29/08/17
 * Time: 11:44 AM
 */

class Form_Print_Pay_Shortcodes
{
	public function __construct() {

		add_shortcode( 'fpp_form_print_pay', array( $this,'fpp_form_print_pay_shortcode' ));
	}

	public function fpp_form_print_pay_shortcode($atts)
	{

		$atts = shortcode_atts(
			array(
				'id' => '',
			), $atts, 'fpp_form_print_pay' );

		$id = (int)$atts['id'];
		$meta_custom = get_post_meta($id, 'fpp_form_print_pay_meta', true );
		$paypal = get_option('form-print_pay_paypal');

		$html = 'nada que mostrar';

		if (!empty($meta_custom) && count($meta_custom) >= 5 && !empty($paypal) && !isset($_GET['token']) && !isset($_GET['f']) && !isset($_SESSION['status_payment_form_pay'])){

			$input = "<input type='hidden' name='url_form'><input type='hidden' name='uniquid_form_print' value='" . uniqid($id . '_') . "'>";
			$user = 'text_user';
			$type = 'type_user';
			$field = 'field_name';
			$value = 'default_value';
			for ($i=0; $i < count($meta_custom[$user]); $i++) {
				if ($meta_custom[$type][$i] == 'hidden'){
					$input .= "<input type='{$meta_custom[$type][$i]}' name='{$meta_custom[$field][$i]}' value='{$meta_custom[$value][$i]}'>";
				}


				if($meta_custom[$type][$i] == 'textarea'){
					$input .= "<label>{$meta_custom[$user][$i]}</label><textarea name='{$meta_custom[$field][$i]}' class='textarea-form-print' placeholder='{$meta_custom[$value][$i]}' required></textarea>";
				}


				if ($meta_custom[$type][$i] != 'textarea' && $meta_custom[$type][$i] != 'hidden' &&  $meta_custom[$type][$i] != 'select'){
					$input .= "<label>{$meta_custom[$user][$i]}</label><input type='{$meta_custom[$type][$i]}' name='{$meta_custom[$field][$i]}' class='input-form-print' placeholder='{$meta_custom[$value][$i]}' required>";
				}

				if($meta_custom[$type][$i] == 'select'){
					$input .= "<label>{$meta_custom[$user][$i]}</label><select name='{$meta_custom[$field][$i]}' class='select-form-print' data-select-print='{$meta_custom[$value][$i]}' required>
					<option value=''>".esc_html__( 'Seleccione', 'form-print-pay' )."</option>
					</select>";
				}
			}


			$html = "<form id='form-print-pay'>";
			$html .= $input;
			$html .= "<button type='submit' id='submit-form-print-pay'>" . __('Generar Documento', 'form-print-pay') . "</button>
			</form>";
			$html .= "<div class='overlay-form-print-pay' style='display: none;'>
                <div class='overlay-content-form-print-pay'>
                    <img src='" . fpp_form_print_pay()->plugin_url . "assets/img/loading29.gif" . "' alt='Loading ...'>
                </div>
            </div>";
		}elseif (count($meta_custom) < 5){
			 $html = __('The text for the PDF has not been set!','form-print-pay');
		}elseif (empty($paypal)){
			$html = __('Paypal has not been set up','form-print-pay');
		}elseif (isset($_GET['f']) && isset($_GET['token'])){
			$html = __('Payment canceled','form-print-pay');
		}elseif (isset($_GET['token']) && isset($_GET['PayerID']) && !isset($_SESSION['status_payment_form_pay'])) {
			$html = "<div class='messagetransaction'>
			<p><strong></strong></p>
			</div><form id='checkstatuspaypal'>
			<input type='hidden' name='tokenpaypal' value='{$_GET['token']}'>
			<input type='hidden' name='PayerIDpaypal' value='{$_GET['PayerID']}'>
			</form><div class='overlay-form-print-pay' style='display: none;'>
                <div class='overlay-content-form-print-pay'>
                    <img src='" . fpp_form_print_pay()->plugin_url . "assets/img/loading29.gif" . "' alt='Loading ...'>
                </div>
            </div>";
		}elseif (isset($_SESSION['status_payment_form_pay'])){
			$html = __('Payment is already processed','form-print-pay');
		}elseif(empty($meta_custom)){
			$html = __('Not working shortcode!, missing setting','form-print-pay');
		}
		return $html;
	}


}