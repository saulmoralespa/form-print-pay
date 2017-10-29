<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 3/09/17
 * Time: 10:52 PM
 */

class Form_Print_Pay_Curl
{
	public function execute($url, $post = '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		if (isset($post)){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}else{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}