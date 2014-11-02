<?php

class Config{
		public $PayPalMode 				= 'sandbox'; // sandbox or live
		public $PayPalApiUsername 		= 'malikp_api1.gmail.com'; //PayPal API Username
		public $PayPalApiPassword 		= 'K7V4FCKQLHAGMK9K'; //Paypal API password
		public $PayPalApiSignature 		= 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-ArSXZfAdR7eZYEqEDQsFq3Z5LW31'; //Paypal API Signature
		public $PayPalCurrencyCode 		= 'USD'; //Paypal Currency Code
		public $PayPalReturnURL 		= 'http://localhost/CakeLearning/cakernd/posts/pp_success/'; //Point to process.php page
		public $PayPalCancelURL 		= 'http://localhost/CakeLearning/cakernd/post/'; //Cancel URL if user clicks cancel
}

?>