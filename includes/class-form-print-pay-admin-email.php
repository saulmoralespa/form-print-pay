<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 3/09/17
 * Time: 04:21 PM
 */

class Form_Print_Pay_Admin_Email
{
	public function configInit()
	{
		fpp_form_print_pay()->tabs->page();
	}

	public function content()
	{
		$email = get_option('form-print-email');
	    ?>
		<h2>Email para el usuario</h2>
        <form id="formemail-print-paypal">
            <table class="form-table">
                <tbody>
                <tr>
                    <th><?php echo __('Asunto','form-print-pay');?></th>
                    <td>
                        <input type="text" name="usersuject" required value="<?php if (isset($email['usersuject'])){echo $email['usersuject'];} ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Mensaje','form-print-pay');?></th>
                    <td>
                        <textarea name="usermessage"  cols="30" rows="5" required><?php if (isset($email['usermessage'])){echo $email['usermessage'];} ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>Email administrador</th>
                </tr>
                <tr>
                    <th><?php echo __('Emails: (Introduzca uno o más separados por comas)','form-print-pay');?></th>
                    <td>
                        <input type="text" name="adminemail" required value="<?php if (isset($email['adminemail'])){echo $email['adminemail'];} ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Asunto','form-print-pay');?></th>
                    <td>
                        <input type="text" name="adminsuject" required value="<?php if (isset($email['adminsuject'])){echo $email['adminsuject'];} ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Mensaje','form-print-pay');?></th>
                    <td>
                        <textarea name="adminmessage"  cols="30" rows="5" required><?php if (isset($email['adminmessage'])){echo $email['adminmessage'];}?></textarea>
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