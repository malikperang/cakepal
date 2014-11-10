<h1>CakePal CakePHP Paypal Plugin </h1>

<h1>Tested on</h1>:
CakePHP version 2.4 >=.

<h1>Install</h1>
Clone CakePal repositories

$git clone git@github.com:malikperang/cakepal.git Plugin/CakePal

<h1>Tutorial : How to use</h1>

<h3>SetExpressCheckout</h3>
Case : You want to use it inside view() function.

1. Create form in your view.ctp with this code
  	<code><?php 
							//create paypal form
							echo $this->Form->create('Post',array());
										echo $this->Form->input('id',array('value'=>$post['Post']['id'],'type'=>'hidden'));
										echo $this->Form->input('name',array('type'=>'hidden','value'=>$post['Post']['title']));
										echo $this->Form->input('description',array('type'=>'hidden','value'=>$post['Post']['content']));
										echo $this->Form->input('booknumber',array('type'=>'hidden'));
										echo $this->Form->input('quantity',array('type'=>'hidden','value'=>1));
										echo $this->Form->input('price',array('type'=>'hidden','value'=>$post['Post']['price']));

									
							
										echo $this->Form->submit('Paypal',array('class'=>'large paypal-button pull-right'));
										echo $this->Form->end();
									?></code>

