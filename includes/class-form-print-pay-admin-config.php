<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 29/08/17
 * Time: 09:32 AM
 */

class Form_Print_Pay_Admin_Config
{
	public function configInit()
	{
        fpp_form_print_pay()->tabs->page();
	}

	public function content()
    {


        $events = get_posts( array ( 'post_type' => 'fpp_form_print_pay' ) );

        if ( isset($events) ) {

	        foreach ( $events as $event ) {
			    $custom_meta = get_post_meta($event->ID, 'fpp_form_print_pay_meta', true );

			    if (isset($custom_meta['pending']) && array_key_exists(0,$custom_meta['pending'])){
			    	foreach ($custom_meta['pending'] as $pendiente){
			    	    if (isset($pendiente['transactionid'])) {

					        ?>
                            <div class="statusOrder-form">
                                <form>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <th>Transaction id</th>
                                            <th>Estado</th>
                                        </tr>
                                        <tr>
                                            <td><input type="hidden" name="id" value="<?php echo $event->ID; ?>">
										        <?php if ( isset( $pendiente['uniquid'] ) ) {
											        echo "<input type='hidden' name='uniquid' value='{$pendiente['uniquid']}'>";
										        } ?>
                                                <input type="hidden" name="email"
                                                       value="<?php if ( isset( $pendiente['email'] ) ) {
											               echo $pendiente['email'];
										               } ?>">
                                                <input type="text" name="transactionid"
                                                       value="<?php if ( isset( $pendiente['transactionid'] ) ) {
											               echo $pendiente['transactionid'];
										               } ?>" readonly></td>
                                            <td>
                                                <select name="statusorder">
                                                    <option value="" selected>Seleccione</option>
                                                    <option value="completed">Completar</option>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
					        <?php
				        }
				    }
			    }

		    }
		    ?>
            <div class="overlay-form-print-pay" style="display: none;">
                <div class="overlay-content-form-print-pay">
                    <img src="<?php echo fpp_form_print_pay()->plugin_url . 'assets/img/loading.gif';?>" alt="">
                </div>
            </div>
        <?php
	    }else{
		    echo sprintf(
			    '<h2>%s</h2>',
			    __( 'No hay ordenes pendientes: ', 'form-print-pay' )
		    );
        }
    }
}