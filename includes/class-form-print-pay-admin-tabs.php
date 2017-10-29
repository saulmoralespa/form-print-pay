<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 29/08/17
 * Time: 11:57 AM
 */

class Form_Print_Pay_Admin_Tabs
{
	public function page()
	{

		$this->name = fpp_form_print_pay()->name;
		if ($_GET['page'] == "config-form print pay") {
			$this->tab = 'general';
		}elseif ($_GET['page'] == "configpaypal-form print pay") {
			$this->tab = 'paypal';
		}elseif($_GET['page'] == "configemail-form print pay") {
			$this->tab = 'email';
		}elseif($_GET['page'] == "configpdf-form print pay") {
			$this->tab = 'pdf';
		}

		$this->page_tabs($this->tab);

		if($this->tab == 'general' ) {
			$config = fpp_form_print_pay()->Config;
			$config->content();
		}

		if($this->tab == 'paypal') {
			$paypal = fpp_form_print_pay()->configPaypal;
			$paypal->content();
		}
		if ($this->tab == 'email') {
			$email = fpp_form_print_pay()->configEmail;
			$email->content();
		}
		if ($this->tab == 'pdf') {
			$pdf = fpp_form_print_pay()->configPdf;
			$pdf->content();

		}
	}

	public function page_tabs($current = 'general')
	{
		$tabs = array(
			'general'   => array('config-' . $this->name, __("General", 'form-print-pay')),
			'paypal'  => array('configpaypal-' . $this->name, __("Paypal", 'form-print-pay')),
			'email'  => array('configemail-' . $this->name, __("Email", 'form-print-pay')),
			'pdf'  => array('configpdf-' . $this->name, __("PDF", 'form-print-pay')),
		);
		$html =  '<h2 class="nav-tab-wrapper">';
		foreach( $tabs as $tab => $name ){
			$class = ($tab == $current) ? 'nav-tab-active' : '';
			$html .=  '<a class="nav-tab ' . $class . '" href="?page='.$name[0].'&tab=' . $tab . '">' . $name[1] . '</a>';
		}
		$html .= '</h2>';
		echo $html;
	}
}