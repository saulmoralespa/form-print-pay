<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 3/09/17
 * Time: 04:23 PM
 */

class Form_Print_Pay_Admin_PDF
{
	public function configInit()
	{
		fpp_form_print_pay()->tabs->page();
	}
	public function content()
	{
		$pdf = get_option('config-pdf-form-print');
	    ?>
        <div id="config-pdf">
        <table class="form-table">
				<tbody id="logo-pdf">
				<tr>
					<th><?php echo __('Logo para el encabezado','form-print-pay');?></th>
				</tr>
				<?php if (file_exists(fpp_form_print_pay()->uploads_dir . "img/logopdf.jpg")){
					echo "<tr><td>Imágen actual &nbsp;&nbsp;<img src='".fpp_form_print_pay()->uploads_url . "img/logopdf.jpg'></td></tr>";
				}?>
                <tr>
                    <td>
                        <input type="file" name="logo-pdf-form-print" id="logo-pdf-form-print"  accept=".jpg, .jpeg">
	                    <?php wp_nonce_field(plugin_basename( fpp_form_print_pay()->file ), 'logo-pdf-form-print'); ?>
                    </td>
                </tr>
				</tbody>
			</table>
        <form id="forpdf-print-paypal">
            <table class="form-table">
                <tbody>
                <tr>
                    <th><?php echo __('Tipo de Font(letra)','form-print-pay');?></th>
                    <td>
                        <select name="font-form-print">
                            <option value="Arial" <?php if (isset($pdf['font-form-print']) && $pdf['font-form-print'] == 'Arial') echo 'selected'; ?>>Arial</option>
                            <option value="Courier" <?php if (isset($pdf['font-form-print']) && $pdf['font-form-print'] == 'Courier') echo 'selected'; ?>>Courier</option>
                            <option value="Symbol" <?php if (isset($pdf['font-form-print']) && $pdf['font-form-print'] == 'Symbol') echo 'selected'; ?>>Symbol</option>
                            <option value="Times" <?php if (isset($pdf['font-form-print']) && $pdf['font-form-print'] == 'Times') echo 'selected'; ?>>Times</option>
                            <option value="ZapfDingbats" <?php if (isset($pdf['font-form-print']) && $pdf['font-form-print'] == 'ZapfDingbats') echo 'selected'; ?>>ZapfDingbats</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Estilo de Font(letra)','form-print-pay');?></th>
                    <td>
                        <select name="font-style-form-print">
                            <option value="B" <?php if (isset($pdf['font-style-form-print']) && $pdf['font-style-form-print'] == 'B') echo 'selected'; ?>>Bold</option>
                            <option value="I" <?php if (isset($pdf['font-style-form-print']) && $pdf['font-style-form-print'] == 'I') echo 'selected'; ?>>Italic</option>
                            <option value="U" <?php if (isset($pdf['font-style-form-print']) && $pdf['font-style-form-print'] == 'U') echo 'selected'; ?>>underline</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __('Tamaño de Font(letra)','form-print-pay');?></th>
                    <td>
                        <select name="letter-size-form-print">
                            <option value="12" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '12') echo 'selected'; ?>>12</option>
                            <option value="13" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '13') echo 'selected'; ?>>13</option>
                            <option value="14" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '14') echo 'selected'; ?>>14</option>
                            <option value="15" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '15') echo 'selected'; ?>>15</option>
                            <option value="16" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '16') echo 'selected'; ?>>16</option>
                            <option value="17" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '17') echo 'selected'; ?>>17</option>
                            <option value="18" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '18') echo 'selected'; ?>>18</option>
                            <option value="19" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '19') echo 'selected'; ?>>19</option>
                            <option value="20" <?php if (isset($pdf['letter-size-form-print']) && $pdf['letter-size-form-print'] == '20') echo 'selected'; ?>>20</option>
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
                <div class="message"><strong></strong></div>
            </div>
        </div>
        </div>
		<?php
	}
}