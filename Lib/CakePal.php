<?php
/*
 *
 *
 */
include_once('Config.php');
include_once('Core.php');

class CakePalException extends CakeException{}
	

class CakePal {

	private $ppconf;

	function __construct(){
		$this->ppconf = new Config(); 
	}

	public function testsetExpressCheckout($ppdata){
		//create config
		$paypalmode = ($this->ppconf->PayPalMode=='sandbox') ?	 '.sandbox' : '';
		//debug($this->ppconf->PayPalMode);

		//exit(1);

		if(is_array($ppdata)):
			//destroy all session first()
			CakeSession::delete('SessionData');
				foreach ($ppdata['items'] as $n => $item) :
					foreach($item as $items):

								#if tax enable
								if(isset($ppdata['tax'])):
									$tax = $ppdata['tax'];
									//CakeSession::write('tax',$tax);
								else:
									$tax = null;
								endif;

								#if shipping enable
								if(isset($ppdata['shipcost'])):
									$shipcost = $ppdata['shipcost'];
								else:
									$shipcost = null;
								endif;

								#if ship discount enable
								if(isset($ppdata['shipdiscount'])):
									$shipdiscount = $ppdata['shipdiscount'];
									CakeSession::write('shipdiscount',$shipdiscount);
								else:
									$shipdiscount = null;
								endif; 

								#if handling cost enable
								if(isset($ppdata['handlingcost'])):
									$handlingcost = $ppdata['handlingcost'];
									CakeSession::write('handlingcost',$handlingcost);
								else:
									$handlingcost = null;
								endif;

								#if insurance cost enable
								if(isset($ppdata['insurancecost'])):
									$insurancecost  = $ppdata['insurancecost'];
									CakeSession::write('insurancecost',$insurancecost);
								else:
									$insurancecost = null;
								endif;


								

								#if items quantity is more than 1
								$totalprice = $items['price'] * $items['quantity'];

								$grandtotal = $items['price'] + $tax + $shipcost + $shipdiscount + $handlingcost + $insurancecost;
								
								//echo $grandTotal . '<br />';

								$ppdata = "&METHOD=SetExpressCheckout".
										  "&RETURNURL=".urlencode($this->ppconf->PayPalReturnURL).
										  "&CANCELURL=".urlencode($this->ppconf->PayPalCancelURL).
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
										  "&PAYMENTREQUEST_0_CURRENCYCODE=".urlencode($this->ppconf->PayPalCurrencyCode).
										  "&LOCALECODE=GB". #PayPal pages to match the language on your website.
										  "&LOGOIMG=http://intllab.com/v6cake/theme/V6/img/images/logov2longB.png". #site logo
										  "&CARTBORDERCOLOR=FFFFFF". #border color of cart
										  "&ALLOWNOTE=1";

									//debug($ppdata);
									//	exit(1);

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
								
								#begin api contact,execute httpPost
										
								$paypal = new Core();
								$httpParsedResponseAr = $paypal->httpPost('SetExpressCheckout', $ppdata, $this->ppconf->PayPalApiUsername, $this->ppconf->PayPalApiPassword, $this->ppconf->PayPalApiSignature, $this->ppconf->PayPalMode);
		
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

	public function testdoExpressCheckoutPayment($credential){
		$this->ppconf = new Config();
		debug($credential);
		$sessionData = CakeSession::read('SessionData');
		//debug($tax['tax']);


		$ppdata = 	'&TOKEN='.urlencode($credential['token']).
					'&PAYERID='.urlencode($credential['PayerID']).
					'&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
				
					//set item info here, otherwise we won't see product details later	
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
					'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($this->ppconf->PayPalCurrencyCode);

		$paypal= new Core();
		$httpParsedResponseAr = $paypal->httpPost('DoExpressCheckoutPayment', $ppdata, $this->ppconf->PayPalApiUsername, $this->ppconf->PayPalApiPassword, $this->ppconf->PayPalApiSignature, $this->ppconf->PayPalMode);
		
		//Check if everything went ok..
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		{

			echo '<h2>Success</h2>';
			echo 'Your Transaction ID : '.urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
		/*
				//Sometimes Payment are kept pending even when transaction is complete. 
				//hence we need to notify user about it and ask him manually approve the transiction
				*/
				
				if('Completed' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
				{
					echo '<div style="color:green">Payment Received! Your product will be sent to you very soon!</div>';
				}
				elseif('Pending' == $httpParsedResponseAr["PAYMENTINFO_0_PAYMENTSTATUS"])
				{
					echo '<div style="color:red">Transaction Complete, but payment is still pending! '.
					'You need to manually authorize this payment in your <a target="_new" href="http://www.paypal.com">Paypal Account</a></div>';
				}

				// we can retrive transection details using either GetTransactionDetails or GetExpressCheckoutDetails
				// GetTransactionDetails requires a Transaction ID, and GetExpressCheckoutDetails requires Token returned by SetExpressCheckOut
				$ppdata = 	'&TOKEN='.urlencode($credential['token']);
				$paypal= new Core();
				$httpParsedResponseAr = $paypal->httpPost('GetExpressCheckoutDetails', $ppdata, $this->ppconf->PayPalApiUsername, $this->ppconf->PayPalApiPassword, $this->ppconf->PayPalApiSignature, $this->ppconf->PayPalMode);

				if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
				{
					
					echo '<br /><b>Stuff to store in database :</b><br /><pre>';
					/*
					#### SAVE BUYER INFORMATION IN DATABASE ###
					//see (http://www.sanwebe.com/2013/03/basic-php-mysqli-usage) for mysqli usage
					
					$buyerName = $httpParsedResponseAr["FIRSTNAME"].' '.$httpParsedResponseAr["LASTNAME"];
					$buyerEmail = $httpParsedResponseAr["EMAIL"];
					
					//Open a new connection to the MySQL server
					$mysqli = new mysqli('host','username','password','database_name');
					
					//Output any connection error
					if ($mysqli->connect_error) {
						die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
					}		
					
					$insert_row = $mysqli->query("INSERT INTO BuyerTable 
					(BuyerName,BuyerEmail,TransactionID,ItemName,ItemNumber, ItemAmount,ItemQTY)
					VALUES ('$buyerName','$buyerEmail','$transactionID','$ItemName',$ItemNumber, $ItemTotalPrice,$ItemQTY)");
					
					if($insert_row){
						print 'Success! ID of last inserted record is : ' .$mysqli->insert_id .'<br />'; 
					}else{
						die('Error : ('. $mysqli->errno .') '. $mysqli->error);
					}
					
					*/
					
					echo '<pre>';
					print_r($httpParsedResponseAr);
					echo '</pre>';
				} else  {
					echo '<div style="color:red"><b>GetTransactionDetails failed:</b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
					echo '<pre>';
					print_r($httpParsedResponseAr);
					echo '</pre>';

				}

			}

			else{
				echo '<div style="color:red"><b>Error : </b>'.urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
				echo '<pre>';
				print_r($httpParsedResponseAr);
				echo '</pre>';
	}
	}

	###########################################################################################################

	public function setExpressCheckout($ppdata){

		$this->ppconf = new Config(); //create config
		$paypalmode = ($this->ppconf->PayPalMode=='sandbox') ?	 '.sandbox' : '';
		//debug($this->ppconf->PayPalMode);

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
										  "&RETURNURL=".urlencode($this->ppconf->PayPalReturnURL).
										  "&CANCELURL=".urlencode($this->ppconf->PayPalCancelURL).
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
										  "&PAYMENTREQUEST_0_CURRENCYCODE=".urlencode($this->ppconf->PayPalCurrencyCode).
										  "&LOCALECODE=GB". #PayPal pages to match the language on your website.
										  "&LOGOIMG=http://intllab.com/v6cake/theme/V6/img/images/logov2longB.png". #site logo
										  "&CARTBORDERCOLOR=FFFFFF". #border color of cart
										  "&ALLOWNOTE=1";

									//debug($ppdata);
									//	exit(1);

								#write session
								
								#begin api contact,execute httpPost
										
								$paypal = new Core();
								$httpParsedResponseAr = $paypal->httpPost('SetExpressCheckout', $ppdata, $this->ppconf->PayPalApiUsername, $this->ppconf->PayPalApiPassword, $this->ppconf->PayPalApiSignature, $this->ppconf->PayPalMode);
		
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

	public function doExpressCheckoutPayment($returnData,$sessionData){
		debug($returndata);
	}
}

?>

