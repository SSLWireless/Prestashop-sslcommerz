<?php

class sslcommerz extends PaymentModule
{
	private	$_html = '';
	private $_postErrors = array();
	static $params1;
	public function __construct()
	{
		$this->name = 'sslcommerz';
		$this->tab = 'payments_gateways';
		$this->version = '1.6';
		$this->author = 'SSL_Commerz';
		$this->module_key = "dbac0b67b480ef11c7be4f2cd1b2d1ee";
                
		$this->currencies = true;
		$this->currencies_mode = 'radio';

        parent::__construct();

		$this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('sslcommerz');
        $this->description = $this->l('Accepts Payments by SSLCommerz');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
	}

	public function getsslcommerzUrl()
	{
			return Configuration::get('SSLCOMMERZ_SANDBOX') ? 'https://www.sslcommerz.com.bd/gwprocess/testbox/' : 'https://www.sslcommerz.com.bd/gwprocess/';
	}
        public function getsoapUrl()
	{
			return Configuration::get('SSLCOMMERZ_SANDBOX') ? 'yes' : 'no';
	}
//SSLCOMMERZ
	public function install()
	{
		if (!parent::install()
			OR !Configuration::updateValue('SSLCOMMERZ_MERCHANT_ID', 'test')
			OR !Configuration::updateValue('SSLCOMMERZ_WORKING_KEY', '123456')
			OR !Configuration::updateValue('SSLCOMMERZ_SANDBOX', 1)
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('SSLCOMMERZ_MERCHANT_ID')
		   OR !Configuration::deleteByName('SSLCOMMERZ_WORKING_KEY')
			OR !Configuration::deleteByName('SSLCOMMERZ_SANDBOX')
			OR !parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		$this->_html = '<h2>SSLCommerz</h2>';
		if (isset($_POST['submitsslcommerz']))
		{
			if (empty($_POST['business']))
				$this->_postErrors[] = $this->l('SSLCommerz Merchant ID is Required!');
		   if (empty($_POST['workingkey']))
				$this->_postErrors[] = $this->l('SSLCommerz Working Key is Required!');		
			if (!isset($_POST['sandbox']))
				$_POST['sandbox'] = 1;
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('SSLCOMMERZ_MERCHANT_ID', strval($_POST['business']));
				Configuration::updateValue('SSLCOMMERZ_SANDBOX', intval($_POST['sandbox']));
				Configuration::updateValue('SSLCOMMERZ_WORKING_KEY', strval($_POST['workingkey']));
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displaysslcommerz();
		$this->displayFormSettings();
		return $this->_html;
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}

	public function displayErrors()
	{
		$nbErrors = sizeof($this->_postErrors);
		$this->_html .= '
		<div class="alert error">
			<h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
			<ol>';
		foreach ($this->_postErrors AS $error)
			$this->_html .= '<li>'.$error.'</li>';
		$this->_html .= '
			</ol>
		</div>';
	}
	
	
	public function displaysslcommerz()
	{
	 // echo "ashwani";
		$this->_html .= '
		<div style="float: right; width: 440px; height: 150px; border: dashed 1px #666; padding: 8px; margin-left: 12px;">
			<h2>'.$this->l('Opening your sslcommerz account').'</h2>
			<div style="clear: both;"></div>
			<p>'.$this->l('SSLCOMMERZis the leading payment gateway in Bangladesh If you want to know more about sslcommerz please visit:http://sslcommerz.com.bd/').'</p>
			<p style="text-align: center;"><a href="http://sslcommerz.com.bd/"><img src="../modules/sslcommerz/ssl_commerz_logo.png" alt="PrestaShop & sslcommerz" style="margin-top: 12px;" /></a></p>
			<div style="clear: right;"></div>
		</div>
		<img src="../modules/sslcommerz/ssl_commerz_logo.png" style="float:left; margin-right:15px;" />
		<b>'.$this->l('This module allows you to accept payments by sslcommerz.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, your sslcommerz account will be automatically credited.').'<br />
		'.$this->l('You need to configure your sslcommerz account first before using this module.').'
		<div style="clear:both;">&nbsp;</div>';
	}

	public function displayFormSettings()
	{
		$conf = Configuration::getMultiple(array('SSLCOMMERZ_MERCHANT_ID', 'SSLCOMMERZ_SANDBOX', 'SSLCOMMERZ_WORKING_KEY'));
		$business = array_key_exists('business', $_POST) ? $_POST['business'] : (array_key_exists('SSLCOMMERZ_MERCHANT_ID', $conf) ? $conf['SSLCOMMERZ_MERCHANT_ID'] : '');
		$sandbox = array_key_exists('sandbox', $_POST) ? $_POST['sandbox'] : (array_key_exists('SSLCOMMERZ_SANDBOX', $conf) ? $conf['SSLCOMMERZ_SANDBOX'] : '');
		$workingkey = array_key_exists('workingkey', $_POST) ? $_POST['workingkey'] : (array_key_exists('SSLCOMMERZ_WORKING_KEY', $conf) ? $conf['SSLCOMMERZ_WORKING_KEY'] : '');

		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="clear: both;">
		<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Settings').'</legend>
			<label>'.$this->l('sslcommerz merchant id').'</label>
			<div class="margin-form"><input type="text" size="33" name="business" value="'.htmlentities($business, ENT_COMPAT, 'UTF-8').'" /></div>
			<label>'.$this->l('Sandbox mode').'</label>
			<div class="margin-form">
				<input type="radio" name="sandbox" value="1" '.($sandbox ? 'checked="checked"' : '').' /> '.$this->l('Yes').'
				<input type="radio" name="sandbox" value="0" '.(!$sandbox ? 'checked="checked"' : '').' /> '.$this->l('No').'
			</div>
			<label>'.$this->l('Validation Password').'</label>
			<div class="margin-form"><input type="text" size="82" name="workingkey" value="'.htmlentities($workingkey, ENT_COMPAT, 'UTF-8').'" />
			</div><br /><br /><br />
			<br /><center><input type="submit" name="submitsslcommerz" value="'.$this->l('Update settings').'" class="button" /></center>
		</fieldset>
		</form><br /><br />
		<fieldset class="width3">
			<legend><img src="../img/admin/warning.gif" />'.$this->l('Information').'</legend>
			'.$this->l('In order to use your sslcommerz payment module, you have to configure your sslcommerz account (sandbox account as well as live account). Log in to sslcommerz and follow these instructions.').'<br /><br />
			'.$this->l('In').' <i>'.$this->l('Profile > Selling Preferences > Website Payment Preferences').'</i>, '. $this->l('set:').'<br />
			- <b>'.$this->l('Auto Return').'</b> : '.$this->l('Off').',<br />
			- <b>'.$this->l('Payment Data Transfer').'</b> '.$this->l('to').' <b>Off</b>.<br /><br />
			'.$this->l('In').' <i>'.$this->l('Profile > Selling Preferences > Postage Calculations').'</i><br />
			- check <b>'.$this->l('Click here to allow transaction-based shipping values to override the profile shipping settings listed above').'</b><br /><br />
			<b style="color: red;">'.$this->l('All PrestaShop currencies must be also configured</b> inside Profile > Financial Information > Currency balances').'<br />
		</fieldset>';
	}
	
				
  
 public function hookPayment($params)
	{
	    //echo "ashwani";
		session_start();
		$_SESSION['params1']='';
		//$_SESSION['params1']='';
		$_SESSION['params1'] = $params;
		if (!$this->active)
			return ;

		global $smarty;

		$address = new Address(intval($params['cart']->id_address_invoice));
		$customer = new Customer(intval($params['cart']->id_customer));
		$MerchantId = trim(Configuration::get('SSLCOMMERZ_MERCHANT_ID'));
		$header = Configuration::get('SSLCOMMERZ_SANDBOX');
		$WorkingKey = trim(Configuration::get('SSLCOMMERZ_WORKING_KEY'));
		$currency = $this->getCurrency();
		//$_SESSION['sequrecode']=$customer->secure_key;
		//$order_id = "Asf123";
		 $currencyType=trim($currency->iso_code);
		// echo $total;
		$Amount = number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', '');
		 $OrderId= date('Ymdhis'). '-' .intval($params['cart']->id) ;
		 $Redirect_Url='http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sslcommerz/validation.php';
		
		
		$str ="$MerchantId|$OrderId|$Amount|$WorkingKey|$currencyType|$Redirect_Url";
		
		  $adler = 1;
		  $BASE =  65521 ;
		
			$s1 = $adler & 0xffff ;
			$s2 = ($adler >> 16) & 0xffff;
			for($i = 0 ; $i < strlen($str) ; $i++)
			{
				$s1 = ($s1 + Ord($str[$i])) % $BASE ;
				$s2 = ($s2 + $s1) % $BASE ;
					//echo "s1 : $s1 <BR> s2 : $s2 <BR>";
		
			}
		
		$str = $s2;
		$num = 16;
		//$str = $s2 + $s1;
			$str = DecBin($str);
		
			for( $i = 0 ; $i < (64 - strlen($str)) ; $i++)
				$str = "0".$str ;
		
			for($i = 0 ; $i < $num ; $i++) 
			{
				$str = $str."0";
				$str = substr($str , 1 ) ;
				//echo "str : $str <BR>";
			}
		$num=$str;
		for ($n = 0 ; $n < strlen($num) ; $n++)
			{
			   $temp = $num[$n] ;
			   $dec =  $dec + $temp*pow(2 , strlen($num) - $n - 1);
			}
			$Checksum = $dec + $s1;
			$AuthDesc = 'N';
			//echo $Checksum;
		
	//	if (!Validate::isEmail($business))
		//	return $this->l('ccavenue error: (invalid or undefined business id)');

		if (!Validate::isLoadedObject($address) OR !Validate::isLoadedObject($customer) OR !Validate::isLoadedObject($currency))
			return $this->l('sslcommerz error: (invalid address or customer)');
			
		$products = $params['cart']->getProducts();
         // print_r($products);
		foreach ($products as $key => $product)
		{
			$products[$key]['name'] = str_replace('"', '\'', $product['name']);
			if (isset($product['attributes']))
				$products[$key]['attributes'] = str_replace('"', '\'', $product['attributes']);
			$products[$key]['name'] = htmlentities(utf8_decode($product['name']));
			$products[$key]['ccavenueAmount'] = number_format(Tools::convertPrice($product['price_wt'], $currency), 2, '.', '');
		}
	  	
	//$Checksum = getCheckSum($Merchant_Id,$Order_Id,$Amount ,$WorkingKey,$Currency,$Redirect_Url);	
		$smarty->assign(array(
			
			'MerchantId' => $MerchantId,
			'header' => $header,
			'OrderId' => $OrderId,
			'actionUrl' => $this->getsslcommerzUrl(),
			// products + discounts - shipping cost
			'Amount' => $Amount,
			// shipping cost + wrapping
			
			// products + discounts + shipping cost
                                                            'Cancel_Url' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sslcommerz/cancel.php',
                                                            'Fail_Url' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sslcommerz/failed.php',
			'Redirect_Url' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sslcommerz/validation.php',
			
			'this_path' => $this->_path
		));
       // echo $id_cart;
		return $this->display(__FILE__, 'process.tpl');
	}

public function seccode()
	{
	   
      	$customer = new Customer(intval($_SESSION['params1']['cart']->id_customer)); 	
	  $sec=$customer->secure_key;
	  return $sec; 
	}




	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		return $this->display(__FILE__, 'confirmation.tpl');
	}

    


public function validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod = 'Unknown', $message = NULL, $extraVars = array(), $currency_special = NULL, $dont_touch_amount = false)
	{
		if (!$this->active)
			return ;

		$currency = $this->getCurrency();
		$cart = new Cart(intval($id_cart));
		$cart->id_currency = $currency->id;
		$cart->save();
		parent::validateOrder($id_cart, $id_order_state, $amountPaid, $paymentMethod, $message, $extraVars, $currency_special, true);
	}
	
}	

?>
