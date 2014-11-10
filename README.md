<h1>CakePal CakePHP Paypal Plugin </h1>

<h1>Tested on</h1>:
CakePHP version 2.4 >=.

<h1>Install</h1>
Clone CakePal repositories

<code>$git clone git@github.com:malikperang/cakepal.git Plugin/CakePal</code>

make sure to loaded CakePal plugin in your bootstrap.php:

<code>CakePlugin::load('CakePal', array('routes' => false));</code>

and include CakePal library in at the top of your controller:

<code>App::uses('CakePal','CakePal.Lib');</code>

create onetime configuration of your paypal at CakePal/Lib/CakePal.php
<code>
/* 
 *Create your onetime paypal api config for instance uses.
 */
#set your paypal api mode sandbox/live
private $PayPalMode 			= 'sandbox or live'; 
#set your paypal api username
private $PayPalApiUsername 		= 'your api username';
#set your paypal api password
private $PayPalApiPassword 		= 'your api key';
#set your paypal api signature
private $PayPalApiSignature 	= 'your api signature';
#set your paypal currency code
private $PayPalCurrencyCode 	= 'your currency';
#set your paypal return URL
private $PayPalReturnURL 		= 'after payment return URL.example : '; 
#set your paypal cancel URL
private $PayPalCancelURL 		= 'after payment cancel URL.example :'; 
</code>

Example configuration:
<code>
/* 
 *Create your onetime paypal api config for instance uses.
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
private $PayPalReturnURL 		= 'http://localhost/mycakeapp/books/pp_return'; 
#set your paypal cancel URL
private $PayPalCancelURL 		= 'http://localhost/mycakeapp/books/pp_cancel'; 
</code>

<h1>Basic Usage Tutorial</h1>

Case: You want to make your books/view/* controller have a paypal payment.

Method applied:
1.SetExpressCheckout
2.DoExpressCheckout
3.GetExpressCheckout

<h3>SetExpressCheckout</h3>


1. Create book form in your Books/view.ctp with this code
<code>
	<?php 
	//create book form
	echo $this->Form->create('Book');
	echo $this->Form->input('id',array('value'=>$book['Book']['id'],'type'=>'hidden'));
	echo $this->Form->input('name',array('type'=>'hidden','value'=>$book['Book']['title']));
	echo $this->Form->input('description',array('type'=>'hidden','value'=>$book['Book']['description']));
	echo $this->Form->input('quantity',array('type'=>'hidden','value'=>$book['Book']['quantity']));
	echo $this->Form->input('price',array('type'=>'hidden','value'=>$book['Book']['price']));
	echo $this->Form->submit('Paypal');
	echo $this->Form->end();
	?>
</code>

2. Add this code in your BooksController.php:
<code>
	if($this->request->is('post')){
		$order = array(
			'items'=>array($this->request->data), //get the data from the request data
			);
		$this->CakePal->setExpressCheckout($order);

	}
</code>



You can also include:-
-tax
-handling cost
-shipping cost
-shipping discount
-insurance cost

by adding this option in your $order array like this:
	<code>
	$order = array(
			'tax'		  	=>10.00,
			'handlingcost'	=>10.00,
			'shipcost'	  	=>10.00,
			'shipdiscount'	=>-2.00,
			'insurancecost' =>10.00,
			'items'			=>	array($this->request->data), //get the data from the request data
			);
	</code>


3.Suppose you had follow the paypal api config above,so your return url is 'http://localhost/mycakeapp/books/pp_return'.
Create <code>pp_return</code> function in your <code>BooksController.php</code>
<code>

	function pp_return(){
		
		//let say you want to execute DoExpressCheckout method here,
		if($this->request->is('get')){
			$this->CakePal->doExpressCheckoutPayment($this->params['url']);
		}

		//for more info about DoExpressCheckout method please continue scrolling.

	}
</code>

<h1>DoExpressCheckout</h1>










