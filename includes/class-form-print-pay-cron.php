<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 11/09/17
 * Time: 07:51 AM
 */

class Form_Print_Pay_Cron
{

	public function __construct()
	{
		if ( ! wp_next_scheduled( 'order_pending_paypal' ) ) {
			wp_schedule_event( time(), 'hourly', 'order_pending_paypal' );
		}
		add_action( 'order_pending_paypal', 'executeCron' );
	}

	public function executeCron()
	{

		$events = get_posts( array ( 'post_type' => 'fpp_form_print_pay' ) );

		if ( isset($events) ) {

			foreach ( $events as $event ) {
				$custom_meta = get_post_meta($event->ID, 'fpp_form_print_pay_meta', true );

				if (isset($custom_meta['pending']) && array_key_exists(0,$custom_meta['pending'])){
					foreach ($custom_meta['pending'] as $pendiente){
						if (isset($pendiente['transactionid'])) {

							$paypal = fpp_form_print_pay()->Paypal;
							$paypal->DoExpressCheckoutPayment($pendiente['token'],$pendiente['payerid'],true,$event->ID,$pendiente['uniquid']);
						}
					}
				}else{
				    return;
                }

			}
		}else{
			return;
		}
	}
}