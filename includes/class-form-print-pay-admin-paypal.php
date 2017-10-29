<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 3/09/17
 * Time: 03:08 PM
 */

class Form_Print_Pay_admin_Paypal
{
	public function configInit()
	{
		fpp_form_print_pay()->tabs->page();
	}
	public function content()
	{
		$paypal = get_option('form-print_pay_paypal');
		$test_paypal = $paypal['test'];
		$api_user = $paypal['api_user'];
		$api_password = $paypal['api_password'];
		$form_signature = $paypal['api_signature'];
		$form_currency = $paypal['currency'];
		?>
            <form id="form-print-paypal">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th><?php echo __('Modo pruebas','form-print-pay');?></th>
                        <td>
                            <select name="test-form-print" id="test-form-print">
                                <option value="sandbox" <?php if ($test_paypal == 'sandbox') echo 'selected'; ?>><?php echo __('SI','form-print-pay');?></option>
                                <option value="live" <?php if ($test_paypal == 'live') echo 'selected'; ?>><?php echo __('NO','form-print-pay');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('API_USER','form-print-pay');?></th>
                        <td>
                            <input type="text" name="form-api-user" value="<?php echo $api_user; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('API_PASSWORD','form-print-pay');?></th>
                        <td>
                            <input type="text" name="form-api-password" value="<?php echo $api_password; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('API_SIGNATURE','form-print-pay');?></th>
                        <td>
                            <input type="text" name="form-api-signature" value="<?php echo $form_signature; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('Moneda','form-print-pay');?></th>
                        <td>
                            <select name="form-currency" id="test-form-print">
                                <option value="EUR" <?php if ($form_currency == 'EUR') echo 'selected'; ?>><?php echo __('Euro','form-print-pay');?></option>
                                <option value="USD" <?php if ($form_currency == 'USD') echo 'selected'; ?>><?php echo __('Dolar estadounidense','form-print-pay');?></option>
                                <option value="MXN" <?php if ($form_currency == 'MXN') echo 'selected'; ?>><?php echo __('Peso Mexicano','form-print-pay');?></option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
				<?php submit_button(); ?>
            </form>
            <div class="overlay-form-print-pay" style="display: none;">
                <div class="overlay-content-form-print-pay">
                    <img src="<?php echo fpp_form_print_pay()->plugin_url . 'assets/img/loading.gif';?>" alt="">
                </div>
            </div>
		<?php
	}
}