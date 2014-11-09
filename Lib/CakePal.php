<?php
/* Paypal Express Chekout Plugin v1.0
* Script by 	: Malik Perang
* Contact me 	: malikperang@gmail.com
* Github		: @malikperang
* Twitter		: @malikperang
* @since 2014
*/

class CakePal {

/* 
 *Create your onetime paypal api config,
 */
#set your paypal api mode sandbox/live
private $PayPalMode 			= 'sandbox'; 
#set your paypal api username
private $PayPalApiUsername 		= 'malikp_api1.gmail.com';
#set your paypal api password
private $PayPalApiPassword 		= 'K7V4FCKQLHAGMK9K';
#set your paypal api signature
private $PayPalApiSignature 	= 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-ArSXZfAdR7eZYEqEDQsFq3Z5LW31';
#set your paypal currency code
private $PayPalCurrencyCode 	= 'USD';
#set your paypal return URL
private $PayPalReturnURL 		= 'http://localhost/CakeLearning/cakernd/posts/pp_success/'; 
#set your paypal cancel URL
private $PayPalCancelURL 		= 'http://localhost/CakeLearning/cakernd/post/'; 

public function beforeFilter(){
		$this->Auth->allow('setExpressCheckout');
}

public function setExpressCheckout($ppdata){
	$paypalmode = ($this->PayPalMode=='sandbox') ?	 '.sandbox' : '';
	if(is_array($ppdata)){
		CakeSession::delete('SessionData');
		foreach ($ppdata['items'] as $n => $item) {
			foreach($item as $items){
						#if tax enable
						if(isset($ppdata['tax'])){
							$tax = $ppdata['tax'];									
						}else{
							$tax = null;
						}

						#if shipping enable
						if(isset($ppdata['shipcost'])){
							$shipcost = $ppdata['shipcost'];
						}else{
							$shipcost = null;
						}
						
						#if ship discount enable
						if(isset($ppdata['shipdiscount'])){
							$shipdiscount = $ppdata['shipdiscount'];
						}else{
							$shipdiscount = null;
						}

						#if handling cost enable
						if(isset($ppdata['handlingcost'])){
							$handlingcost = $ppdata['handlingcost'];
						}else{
							$handlingcost = null;
						}		

						#if insurance cost enable
						if(isset($ppdata['insurancecost'])){
							$insurancecost  = $ppdata['insurancecost'];
						}else{
							$insurancecost = null;
						}

						#if items quantity is more than 1
						$totalprice = $items['price'] * $items['quantity'];

						$grandtotal = $items['price'] + $tax + $shipcost + $shipdiscount + $handlingcost + $insurancecost;
						

						$ppdata = "&METHOD=SetExpressCheckout".
								  "&RETURNURL=".urlencode($this->PayPalReturnURL).
								  "&CANCELURL=".urlencode($this->PayPalCancelURL).
								  "&PAYMENTREQUEST_0_PAYMENTACTION=".urlencode("SALE").
		
								  "&L_PAYMENTREQUEST_0_NAME0=".urlencode($items['name']).
								  "&L_PAYMENTREQUEST_0_NUMBER0=".urlencode($n).	#number of item on cart
								  "&L_PAYMENTREQUEST_0_DESC0=".urlencode($items['description']).
			      				  "&L_PAYMENTREQUEST_0_AMT0=".urlencode($items['price']).
				  				  "&L_PAYMENTREQUEST_0_QTY0=". urlencode($items['quantity']).
				  				  "&NOSHIPPING=0".
				  				  "&PAYMENTREQUEST_0_ITEMAMT=".urlencode($totalprice).
				  				  "&PAYMENTREQUEST_0_TAXAMT=".urlencode($tax).
								  "&PAYMENTREQUEST_0_SHIPPINGAMT=".urlencode($shipcost).
								  "&PAYMENTREQUEST_0_SHIPDISCAMT=".urlencode($shipdiscount).
								  "&PAYMENTREQUEST_0_HANDLINGAMT=".urlencode($handlingcost).
								  "&PAYMENTREQUEST_0_INSURANCEAMT=".urlencode($insurancecost).					  				
				  				  "&PAYMENTREQUEST_0_AMT=".urlencode($grandtotal).
								  "&PAYMENTREQUEST_0_CURRENCYCODE=".urlencode($this->PayPalCurrencyCode).
								  "&LOCALECODE=GB". 
								  "&LOGOIMG=http://intllab.com/v6cake/theme/V6/img/images/logov2longB.png". 
								  #site logo
								  "&CARTBORDERCOLOR=FFFFFF".
								   #border color of cart
								  "&ALLOWNOTE=1";


						#write session
						CakeSession::write('SessionData',array(
							'item_name'=>$items['name'],
							'item_number'=>$n,
							'item_description'=>$items['description'],
							'item_price'=>$items['price'],
							'item_quantity' => $items['quantity'],
							'totalprice'=>$totalprice,
							'tax'=>$tax,
							'shipcost' => $shipcost,
							'shipdiscount'=>$shipdiscount,
							'handlingcost'=>$handlingcost,
							'insurancecost'=>$insurancecost,
							'grandtotal'=>$grandtotal,
							
							));
						
						#begin api contact,execute setExpressCheckout						
						$httpParsedResponseAr = $this->httpPost('SetExpressCheckout', $ppdata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);
						
						if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])){
							 	$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
								header('Location:'.$paypalurl);				
								exit;
						}else{
								echo 'From CakePal: Error! '.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
						}
					}
				}
	}else{
		return false;
	}

}

public function doExpressCheckoutPayment($credential){
			$sessionData = CakeSession::read('SessionData');
			$ppdata = 	'&TOKEN='.urlencode($credential['token']).
				'&PAYERID='.urlencode($credential['PayerID']).
				'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
				'&L_PAYMENTREQUEST_0_NAME0='.urlencode($sessionData['item_name']).
				'&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($sessionData['item_number']).
				'&L_PAYMENTREQUEST_0_DESC0='.urlencode($sessionData['item_description']).
				'&L_PAYMENTREQUEST_0_AMT0='.urlencode($sessionData['item_price']).
				'&L_PAYMENTREQUEST_0_QTY0='. urlencode($sessionData['item_quantity']).
				'&PAYMENTREQUEST_0_ITEMAMT='.urlencode($sessionData['totalprice']).
				'&PAYMENTREQUEST_0_TAXAMT='.urlencode($sessionData['tax']).
				'&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($sessionData['shipcost']).
				'&PAYMENTREQUEST_0_HANDLINGAMT='.urlencode($sessionData['handlingcost']).
				'&PAYMENTREQUEST_0_SHIPDISCAMT='.urlencode($sessionData['shipdiscount']).
				'&PAYMENTREQUEST_0_INSURANCEAMT='.urlencode($sessionData['insurancecost']).
				'&PAYMENTREQUEST_0_AMT='.urlencode($sessionData['grandtotal']).
				'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->PayPalCurrencyCode);

			#begin api contact,execute doExpressCheckoutPayment
			$httpParsedResponseAr = $this->httpPost('DoExpressCheckoutPayment', $ppdata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);

			if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			   return true;
			}else{
				return $httpParsedResponseAr["L_LONGMESSAGE0"];
			}
}

public function getExpressCheckoutDetails($credential){
		$ppdata = '&TOKEN='.urlencode($credential['token']);
		#begin api contact,execute setExpressCheckoutDetails
		$httpParsedResponseAr = $this->httpPost('GetExpressCheckoutDetails', $ppdata, $this->PayPalApiUsername, $this->PayPalApiPassword, $this->PayPalApiSignature, $this->PayPalMode);
		
		if(strtoupper($httpParsedResponseAr["ACK"]) == "SUCCESS" || strtoupper($httpParsedResponseAr["ACK"]) == "SUCCESSWITHWARNING"){
			return $httpParsedResponseAr;
		}else {
			return false;
		}
}


function httpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode) {
	$API_UserName = urlencode($this->PayPalApiUsername);
	$API_Password = urlencode($this->PayPalApiPassword);
	$API_Signature = urlencode($this->PayPalApiSignature);

	$paypalmode = ($this->PayPalMode =='sandbox') ? '.sandbox' : '';

	$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
	$version = urlencode('109.0');

	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

	
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

	
	$httpResponse = curl_exec($ch);

	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}


	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
	}
}

?>

