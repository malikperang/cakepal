<?php

//App::uses('AppController', 'Controller');
include_once('Config.php');
include_once('Core.php');

class CakePal {
	//public $paypalmode = $PayPalMode

	#################################   WARNING!! THIS SECTION IS JUST USE FOR DEVELOPMENT ##################
	public function test($ppdata){
		if(is_array($ppdata)):
				$ppconf = new Config(); //create config

				//echo $ppconf->PayPalMode; 	
				//echo $ppconf->PayPalCurrencyCode;
				//exit(1);
				//debug($pass);
				foreach ($ppdata['items'] as $n => $item) :
					
					foreach($item as $items):
					//	debug($ppdata);
					//	debug($n);
					//	debug($item);
					//	debug($items);
					//exit(1);
								//debug($pass['tax']);
								//done for extract
								//echo 'Item ' . ' ' . $m . ' is ' . $items['name'];

								
								
								
								/*switch ($pass) {
									case isset($pass['tax']):
										$ItemTotalPrice = $items['price'] + $pass['tax'];
										break;
									case isset($pass['shipcost']):
										$ItemTotalPrice = $items['price'] + $pass['shipcost'];
										break;
									case isset($pass['shipdiscount']):
										$ItemTotalPrice = $items['price'] + $pass['shipdiscount'];
										break;
									case isset($pass['handlingcost']):
										$ItemTotalPrice = $items['price'] + $pass['handlingcost'];
										break;
									case isset($pass['insurancecost']):
										$ItemTotalPrice = $items['price'] + $pass['insurancecost'];
										break;

									case isset($pass['tax']) && isset($pass['shipcost']):
										$ItemTotalPrice = $items['price'] + $pass['tax'] + $pass['shipcost'];
										break;
									case isset($pass['tax'] . $pass['shipcost'] . $pass['tax']):
										$ItemTotalPrice = $items['price'] + $pass['shipcost'] + $pass['tax'];
										break;
									default:
										$ItemTotalPrice = $items['price'];
										break;
								};*/
								
								#if tax enable
								if(isset($ppdata['tax'])):
									$tax = $ppdata['tax'];
								else:
									$tax = 0;
								endif;

								#if shipping enable
								if(isset($ppdata['shipcost'])):
									$shipcost = $ppdata['shipcost'];
								else:
									$shipcost = 0;
								endif;

								#if ship discount enable
								if(isset($ppdata['shipdiscount'])):
									$shipdiscount = $ppdata['shipdiscount'];
								else:
									$shipdiscount = 0;
								endif; 

								#if handling cost enable
								if(isset($ppdata['handlingcost'])):
									$handlingcost = $ppdata['handlingcost'];
								else:
									$handlingcost = 0;
								endif;

								#if insurance cost enable
								if(isset($ppdata['insurancecost'])):
									$insurancecost  = $ppdata['insurancecost'];
								else:
									$insurancecost = 0;
								endif;



								#if items quantity is more than 1
								$totalPrice = $items['price'] * $items['quantity'];

								$grandTotal = $items['price'] + $tax + $shipcost + $shipdiscount + $handlingcost + $insurancecost;
								
								echo $grandTotal . '<br />';

								$ppdata = "L_PAYMENTREQUEST_0_NAME$n=".urlencode($items['name']).
										  "L_PAYMENTREQUEST_0_NUMBER$n=".urlencode($n).	#number of item on cart
										  "L_PAYMENTREQUEST_0_DESC$n=".urlencode($items['description']).
					      				  "&L_PAYMENTREQUEST_0_AMT$n=".urlencode($items['price']).
						  				  "&L_PAYMENTREQUEST_0_QTY$n=". urlencode($items['quantity']).
						  				  "&PAYMENTREQUEST_0_TAXAMT$n=".urlencode($tax).
										  "&PAYMENTREQUEST_0_SHIPPINGAMT$n=".urlencode($shipcost).
										  "&PAYMENTREQUEST_0_SHIPDISCAMT$n=".urlencode($shipdiscount).
										  "&PAYMENTREQUEST_0_HANDLINGAMT$n=".urlencode($handlingcost).
										  "&PAYMENTREQUEST_0_INSURANCEAMT$n=".urlencode($insurancecost).
						  				  "&PAYMENTREQUEST_0_ITEMAMT$n=".urlencode($totalPrice).
						  				  "&PAYMENTREQUEST_0_AM$n=".urlencode($grandTotal).
										  "&PAYMENTREQUEST_0_CURRENCYCODE=".urlencode($ppconf->PayPalCurrencyCode)
										;
								
								#begin api contact,execute httpPost
										
								$paypal = new Core();
								$httpParsedResponseAr = $paypal->PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
								//Respond according to message we receive from Paypal
								if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
								{

										//Redirect user to PayPal store with Token received.
									 	$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
										header('Location',$paypalurl);
									 	exit;
								}else{
									//Show error message
									echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
									echo '<pre>';
									print_r($httpParsedResponseAr);
									echo '</pre>';
								}


								//$number = "L_PAYMENTREQUEST_0_NUMBER$n=".urlencode($n);
						  		//echo $number;
					endforeach;
					//$methodName = 'lalala';
					//$nvpreq = "METHOD=$methodName";
				
					//echo $nvpreq;	
				endforeach;

			
		else:
			return false;
		endif;
	
	
		
		//exit(1);
	}
	###########################################################################################################

	public function setExpressCheckout($ppdata){

		$ppconf = new Config(); //create config
		$paypalmode = ($ppconf->PayPalMode=='sandbox') ?	 '.sandbox' : '';
		//debug($ppconf->PayPalMode);

		//exit(1);

		if(is_array($ppdata)):
				foreach ($ppdata['items'] as $n => $item) :
					foreach($item as $items):

								#if tax enable
								if(isset($ppdata['tax'])):
									$tax = $ppdata['tax'];
								else:
									$tax = 0;
								endif;

								#if shipping enable
								if(isset($ppdata['shipcost'])):
									$shipcost = $ppdata['shipcost'];
								else:
									$shipcost = 0;
								endif;

								#if ship discount enable
								if(isset($ppdata['shipdiscount'])):
									$shipdiscount = $ppdata['shipdiscount'];
								else:
									$shipdiscount = 0;
								endif; 

								#if handling cost enable
								if(isset($ppdata['handlingcost'])):
									$handlingcost = $ppdata['handlingcost'];
								else:
									$handlingcost = 0;
								endif;

								#if insurance cost enable
								if(isset($ppdata['insurancecost'])):
									$insurancecost  = $ppdata['insurancecost'];
								else:
									$insurancecost = 0;
								endif;



								#if items quantity is more than 1
								$totalPrice = $items['price'] * $items['quantity'];

								$grandTotal = $items['price'] + $tax + $shipcost + $shipdiscount + $handlingcost + $insurancecost;
								
								//echo $grandTotal . '<br />';

								$ppdata = "&METHOD=SetExpressCheckout".
										  "&RETURNURL=".urlencode($ppconf->PayPalReturnURL).
										  "&CANCELURL=".urlencode($ppconf->PayPalCancelURL).
										  "&PAYMENTREQUEST_0_PAYMENTACTION=".urlencode("SALE").
				
										  "&L_PAYMENTREQUEST_0_NAME0=".urlencode($items['name']).
										  "&L_PAYMENTREQUEST_0_NUMBER0=".urlencode($n).	#number of item on cart
										  "&L_PAYMENTREQUEST_0_DESC0=".urlencode($items['description']).
					      				  "&L_PAYMENTREQUEST_0_AMT0=".urlencode($items['price']).
						  				  "&L_PAYMENTREQUEST_0_QTY0=". urlencode($items['quantity']).
						  				  "&NOSHIPPING=0".
						  				  "&PAYMENTREQUEST_0_ITEMAMT=".urlencode($totalPrice).
						  				  "&PAYMENTREQUEST_0_TAXAMT=".urlencode($tax).
										  "&PAYMENTREQUEST_0_SHIPPINGAMT=".urlencode($shipcost).
										  "&PAYMENTREQUEST_0_SHIPDISCAMT=".urlencode($shipdiscount).
										  "&PAYMENTREQUEST_0_HANDLINGAMT=".urlencode($handlingcost).
										  "&PAYMENTREQUEST_0_INSURANCEAMT=".urlencode($insurancecost).					  				
						  				  "&PAYMENTREQUEST_0_AMT=".urlencode($grandTotal).
										  "&PAYMENTREQUEST_0_CURRENCYCODE=".urlencode($ppconf->PayPalCurrencyCode).
										  "&LOCALECODE=GB". #PayPal pages to match the language on your website.
										  "&LOGOIMG=http://intllab.com/v6cake/theme/V6/img/images/logov2longB.png". #site logo
										  "&CARTBORDERCOLOR=FFFFFF". #border color of cart
										  "&ALLOWNOTE=1";

									//debug($ppdata);
									//	exit(1);
								
								#begin api contact,execute httpPost
										
								$paypal = new Core();
								$httpParsedResponseAr = $paypal->httpPost('SetExpressCheckout', $ppdata, $ppconf->PayPalApiUsername, $ppconf->PayPalApiPassword, $ppconf->PayPalApiSignature, $ppconf->PayPalMode);
		
								//Respond according to message we receive from Paypal
								if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
								{

										//Redirect user to PayPal store with Token received.
									 	$paypalurl ='https://www'.$paypalmode.'.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.$httpParsedResponseAr["TOKEN"].'';
										header('Location:'.$paypalurl);
										exit;
										
									 
								}else{
										//Show error message
										echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
										echo '<pre>';
										print_r($httpParsedResponseAr);
										echo '</pre>';
								}

								
					endforeach;
				endforeach;

			
		else:
			//return false;
		endif;
		
	}

	public function doExpressCheckoutPayment($returndata){
		debug($returndata);
	}
}

?>

