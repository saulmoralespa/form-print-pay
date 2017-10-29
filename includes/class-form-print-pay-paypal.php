<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 2/09/17
 * Time: 03:41 PM
 */

class Form_Print_Pay_Paypal
{
	function __construct()
	{
		$this->paypal = get_option('form-print_pay_paypal');

	}

	function GetItemTotalPrice($item){

		//(Item Price x Quantity = Total) Get total amount of product;
		return $item['ItemPrice'] * $item['ItemQty'];
	}

	function GetProductsTotalAmount($products){

		$ProductsTotalAmount=0;

		foreach($products as $p => $item){

			$ProductsTotalAmount = $ProductsTotalAmount + $this -> GetItemTotalPrice($item);
		}

		return $ProductsTotalAmount;
	}

	function GetGrandTotal($products, $charges){

		//Grand total including all tax, insurance, shipping cost and discount

		$GrandTotal = $this -> GetProductsTotalAmount($products);

		foreach($charges as $charge){

			$GrandTotal = $GrandTotal + $charge;
		}

		return $GrandTotal;
	}

	function SetExpressCheckout($products, $noshipping='1'){

		//Parameters for SetExpressCheckout, which will be sent to PayPal

		$charges = $this->charges();

		$padata  = 	'&METHOD=SetExpressCheckout';
		$padata .=	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");

		foreach($products as $p => $item){

			$padata .= 	'&RETURNURL='.urlencode($item['url_form']);
			$padata .=	'&CANCELURL='.urlencode($item['url_form'] . '?f=cancel');
			$padata .=  '&PAYMENTREQUEST_0_CUSTOM='.urlencode($item['uniquid']);
			$padata .=  '&PAYMENTREQUEST_0_NOTIFYURL='.urlencode(home_url());
			$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
			$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
			$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
			$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
		}

		/*

		//Override the buyer's shipping address stored on PayPal, The buyer cannot edit the overridden address.

		$padata .=	'&ADDROVERRIDE=1';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTONAME=J Smith';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTREET=1 Main St';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOCITY=San Jose';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOSTATE=CA';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=US';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOZIP=95131';
		$padata .=	'&PAYMENTREQUEST_0_SHIPTOPHONENUM=408-967-4444';

		*/

		$padata .=	'&NOSHIPPING='.$noshipping; //set 1 to hide buyer's shipping address, in-case products that does not require shipping

		$localcode = explode(_,get_locale());
		$padata .=	'&LOCALECODE=' . $localcode[1];
		$padata .=	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));

		$padata .=	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
		$padata .=	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
		$padata .=	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
		$padata .=	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
		$padata .=	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
		$padata .=	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
		$padata .=	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->paypal['currency']);
		$padata .=	'&ALLOWNOTE=1';

		############# set session variable we need later for "DoExpressCheckoutPayment" #######

		$_SESSION['ppl_products'] =  $products;
		$_SESSION['status_payment_form_pay'] = null;

		$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $padata);

		//Respond according to message we receive from Paypal
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

			$paypalmode = ($this->paypal['test']=='sandbox') ? '.sandbox' : '';

			//Redirect user to PayPal store with Token received.

			$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';

			die(json_encode(array('status' => true, 'url'=> $paypalurl)));
		}
		else{

			//Show error message

			die(json_encode(array('status' => false, 'url'=> $httpParsedResponseAr["L_LONGMESSAGE0"])));

		}
	}


	function DoExpressCheckoutPayment($token,$payerID, $cron = false, $id =null, $uniquid = null){

		if(isset($_SESSION['ppl_products']) xor (isset($id) && isset($uniquid))){


			if (isset($uniquid)){
				$meta_custom = get_post_meta($id,'fpp_form_print_pay_meta',true);
				$array = $this->seachKey($meta_custom,$uniquid, $cron = false);
				$products = $array['products'];
			}else{
				$products = $_SESSION['ppl_products'];
			}

			$charges = $this->charges();
			$uniquid = '';
			$email = '';

			$padata  = 	'&TOKEN='.urlencode($token);
			$padata .= 	'&PAYERID='.urlencode($payerID);
			$padata .= 	'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE");

			//set item info here, otherwise we won't see product details later

			foreach($products as $p => $item){

				$uniquid .= $item['uniquid'];
				$email .= $item['email'];
				$padata .= 	'&RETURNURL='.urlencode($item['url_form']);
				$padata .=	'&CANCELURL='.urlencode($item['url_form'] . '?f=cancel');
				$padata .=  '&PAYMENTREQUEST_0_CUSTOM='.urlencode($item['url_form']);
				$padata .=  '&PAYMENTREQUEST_0_NOTIFYURL='.urlencode(home_url());
				$padata .=	'&L_PAYMENTREQUEST_0_NAME'.$p.'='.urlencode($item['ItemName']);
				$padata .=	'&L_PAYMENTREQUEST_0_DESC'.$p.'='.urlencode($item['ItemDesc']);
				$padata .=	'&L_PAYMENTREQUEST_0_AMT'.$p.'='.urlencode($item['ItemPrice']);
				$padata .=	'&L_PAYMENTREQUEST_0_QTY'.$p.'='. urlencode($item['ItemQty']);
			}

			$padata .= 	'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($this -> GetProductsTotalAmount($products));
			$padata .= 	'&PAYMENTREQUEST_0_TAXAMT='.urlencode($charges['TotalTaxAmount']);
			$padata .= 	'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($charges['ShippinCost']);
			$padata .= 	'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($charges['HandalingCost']);
			$padata .= 	'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($charges['ShippinDiscount']);
			$padata .= 	'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($charges['InsuranceCost']);
			$padata .= 	'&PAYMENTREQUEST_0_AMT='.urlencode($this->GetGrandTotal($products, $charges));
			$padata .= 	'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->paypal['currency']);


					//We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.

			$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $padata);

			if (isset($httpParsedResponseAr)){
				$this->action_status_paypal($httpParsedResponseAr,$uniquid,$email,$token,$payerID, $cron, $id);
			}else{
				$array = array('status' => false, 'message' => 'no params DoExpressCheckoutPayment');
				die(json_encode($array));
			}



		}else{
			die(json_encode(array('status' => false, 'message' => 'Not sessions')));
		}
	}

	function PPHttpPost($methodName_, $nvpStr_) {

		// Set up your API credentials, PayPal end point, and API version.
		$API_UserName = urlencode($this->paypal['api_user']);
		$API_Password = urlencode($this->paypal['api_password']);
		$API_Signature = urlencode($this->paypal['api_signature']);

		$paypalmode = ($this->paypal['test']=='sandbox') ? '.sandbox' : '';

		$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
		$version = urlencode('109.0');

		$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
		$httpResponse = fpp_form_print_pay()->cURL->execute($API_Endpoint,$nvpreq);


		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {

			$tmpAr = explode("=", $value);

			if(sizeof($tmpAr) > 1) {

				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {

			$message = "Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.";
			$array = array('status' => false, 'message' => $message);
			die(json_encode($array));
		}

		return $httpParsedResponseAr;
	}

	function createpdf($id){
		$idpost = explode('_',$id);
		$idpost = $idpost[0];
		$meta_custom = get_post_meta($idpost,'fpp_form_print_pay_meta', true);
		$textpdf = $meta_custom['text_pdf'];
		$uniquid = $meta_custom[$id];

		foreach ($uniquid as $key => $value) {
			$pos = strpos($textpdf,$key);
			if ($pos !== false){
				$textpdf = str_replace($key,$value,$textpdf);
			}
		}


		$pdfEdit = get_option('config-pdf-form-print');

		$font = isset($pdfEdit['font-form-print']) ? $pdfEdit['font-form-print'] : 'Arial';
		$fontStyle = isset($pdfEdit['font-style-form-print']) ? $pdfEdit['font-style-form-print'] : 'B';
		$fontSize = isset($pdfEdit['letter-size-form-print']) ? $pdfEdit['letter-size-form-print'] : '16';
		$title = $uniquid['nameproduct_print'];
		$title = iconv('UTF-8', 'windows-1252', $title);
		$textpdf = iconv('UTF-8', 'windows-1252', $textpdf);

		if (!is_dir(fpp_form_print_pay()->uploads_dir))
		fpp_form_print_pay()->createDirUploads(fpp_form_print_pay()->uploads_dir);
		$imageLogo = fpp_form_print_pay()->uploads_dir . 'img/logopdf.jpg';
		$pdfFile = fpp_form_print_pay()->uploads_dir . "pdfs/$id.pdf";

		$pdf = fpp_form_print_pay()->fpdf;
		$pdf->AddPage();
		$pdf->SetFont($font,$fontStyle,$fontSize);

		if (file_exists($imageLogo)){
			$image_format = strtolower(pathinfo($imageLogo, PATHINFO_EXTENSION));
			$pdf->Image($imageLogo,10,8,33,$image_format);
			$pdf->Cell(60);
			// Título
			$pdf->Cell(80,10,$title,1,0,'C');
			// Salto de línea
			$pdf->Ln(40);
		}

		$pdf->SetLeftMargin(45);
		$pdf->Write(5,$textpdf);
		$pdf->Output('F', $pdfFile);

		if(file_exists($pdfFile)){
			unset($meta_custom[$id]);
			update_post_meta($idpost,'fpp_form_print_pay_meta',$meta_custom);
			return true;
		}
		return false;
	}

	function sendEmail($id,$transactionid,$mail){

		$buyerEmail = $mail;

		$attachments = array( fpp_form_print_pay()->uploads_dir . "pdfs/$id.pdf" );

		$emailparams = get_option('form-print-email');

		if(!empty($emailparams)){
			$usersuject = $emailparams['usersuject'];
			$usermessage = $emailparams['usermessage'];
			$adminsuject =  $emailparams['adminsuject'];
			$adminmessage = $emailparams['adminmessage'];
			$emailadmin =  $emailparams['adminemail'];
			$emailadmin =  str_replace(' ', '',$emailadmin);
			$emailadmin =  (strpos($emailadmin,',') !== false ) ? explode(',',$emailadmin) : $emailadmin;
		}else{
			$usersuject = __('Documento generado ','form-print-pay') . home_url();
			$usermessage = __('Enhorabuena su docuemnto en pdf se adjunta');
			$adminsuject = __('Nuevo formulario generado') . home_url();
			$adminmessage = __('Transactionid paypal: ','form-print-pay') . $transactionid;
			$emailadmin  = get_bloginfo('admin_email');
		}


		$headers = array();
		$multiple_recipients = array(
			$buyerEmail,
			get_bloginfo('admin_email')
		);

		$user = wp_mail( $multiple_recipients, $usersuject, $usermessage, $headers,  $attachments );

		$admin = wp_mail( $emailadmin, $adminsuject, $usermessage, $adminmessage,  $attachments );

		if ($user && $admin){
			return true;
		}

		return false;

	}

	function action_status_paypal($httpParsedResponseAr,$uniquid,$email,$token = '',$payerid = '', $cron = false, $id = ''){
		if (isset($httpParsedResponseAr) && is_array($httpParsedResponseAr)) {


			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){

				$_SESSION['status_payment_form_pay'] = 	$httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"];




				$transactionid = $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"];
				$pdf = $this->createpdf($uniquid);


				if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"]){


					if ($pdf){

						if ($cron){
							$meta_custom = get_post_meta($id,'fpp_form_print_pay_meta',true);
							$metaChange = $this->seachKey($meta_custom,$uniquid);
							update_post_meta($id,'fpp_form_print_pay_meta',$metaChange);
						}

						if ($this->sendEmail($uniquid, $transactionid, $email)){
							$array = array('status' => 'completed', 'transactionid' => $transactionid, 'pdf' => $pdf, 'email' => $email);
							die(json_encode($array));
						}

					}

					$array = array('status' => 'completed', 'transactionid' => $transactionid, 'pdf' => $pdf, 'email' => null);
					die(json_encode($array));

				}elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"] && $cron === false){

					$idpost = explode('_',$uniquid);
					$idpost = $idpost[0];
					$meta_custom = get_post_meta($idpost,'fpp_form_print_pay_meta', true);


					$arraypend = array('token' => $token,'payerid' => $payerid, 'transactionid' => $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"], 'uniquid' => $uniquid, 'email' => $email, 'products' => $_SESSION['ppl_products']);

					if (isset($meta_custom['pending'])){
						$array = array_merge($meta_custom['pending'],array($arraypend));
						unset($meta_custom['pending']);
						$meta = array_merge($meta_custom,array('pending' => $array));
					}else{
						$meta = array_merge($meta_custom,array('pending' => array($arraypend)));
					}
					$endarray = array_merge($meta_custom, $meta);
					update_post_meta($idpost,'fpp_form_print_pay_meta', $endarray);

					$array = array('status' => 'pending', 'transactionid' => $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"], 'email' => $email, 'reason' => $httpParsedResponseAr["PAYMENTINFO_0_PENDINGREASON"]);
					die(json_encode($array));
				}elseif ('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"] && $cron){
					return;
				}


				unset($_SESSION['ppl_products']);

			}else{

				die(json_encode(array('status' => 'error', 'message' =>  urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]))));
			}

		}else{
			$meta_custom = get_post_meta($httpParsedResponseAr,'fpp_form_print_pay_meta', true);

			if ($this->sendEmail($uniquid, $token, $email)){
				$metaChange = $this->seachKey($meta_custom,$uniquid);
				$meta = update_post_meta($httpParsedResponseAr,'fpp_form_print_pay_meta',$metaChange);
				die(json_encode(array('status' => $meta, 'message' => __('Send email','form-print-pay'))));
			}else{
				die(json_encode(array('status' => false, 'message' => __('Not send email','form-print-pay'))));
			}

		}
	}

	function seachKey($meta_custom,$uniquid, $cron = false){
		foreach($meta_custom['pending'] as $key => $item){
			$keyuniquid = array_search($uniquid, $meta_custom['pending'][$key]);
			if (isset($keyuniquid) && $cron === false){
				unset($meta_custom['pending'][$key]);
				return $meta_custom;
			}else{
				return $meta_custom['pending'][$key];
			}
		}
	}

	function charges(){
		$charges = [];

		//Other important variables like tax, shipping cost
		$charges['TotalTaxAmount'] = 0;  //Sum of tax for all items in this order.
		$charges['HandalingCost'] = 0;  //Handling cost for this order.
		$charges['InsuranceCost'] = 0;  //shipping insurance cost for this order.
		$charges['ShippinDiscount'] = 0; //Shipping discount for this order. Specify this as negative number.
		$charges['ShippinCost'] = 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate
		return $charges;
	}
}