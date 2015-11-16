<?php

class sslcommerz extends PaymentModule
{
	
	private $_html = '';
	private $_postErrors = array();
	
	function __construct()
	{
		$this->name = 'sslcommerz';
		$this->tab = 'payments_gateways';
		$this->version = 1.0;
		$this->author = 'SSL_Commerz';
		$this->need_instance = 0;
		

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('SSLCOMMERZ Payment Gateway');
		$this->description = $this->l('A payment module for SSLCOMMERZ');
 
	}

	/**
	*	Function install()
	*	Is called when 'Install' in on this module within administration page
	*/
	    
	public function install()
	{
		if (!parent::install()
			OR !$this->createPaymentcardtbl() //calls function to create payment card table
            OR !$this->registerHook('invoice')
			OR !$this->registerHook('payment')
			OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}
    

	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}
	
	/*
	 * This function will check display the card details form payment_execution.tpl
	 * It will check if the submit button on the form has been pressed and submit the card details to the database 
	 */
	
	public function execPayment($cart)
	{
		if (!$this->active)
			return ;
   			
		global $cookie, $smarty;

		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment_execution.tpl');
	}
	
	/**
	*	hookPayment($params)
	*	Called in Front Office at Payment Screen - displays user this module as payment option
	*/
	function hookPayment($params)
	{
		global $smarty;
		
		$smarty->assign(array(
            'this_path' 		=> $this->_path,
            'this_path_ssl' 	=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"));
			
		return $this->display(__FILE__, 'payment.tpl');
	}

	function hookInvoice($params)
	{
		$id_order = $params['id_order'];
		
			global $smarty;
			$paymentCarddetails = $this->readPaymentcarddetails($id_order);
			
			$smarty->assign(array(
			    'ordertime'  	        => $paymentCarddetails['ordertime'],
				'status' 		        => $paymentCarddetails['status'],
				'id_order'					=> $id_order,
				'this_page'					=> $_SERVER['REQUEST_URI'],
				'this_path' 				=> $this->_path,
            	'this_path_ssl' 			=> Configuration::get('PS_FO_PROTOCOL').$_SERVER['HTTP_HOST'].__PS_BASE_URI__."modules/{$this->name}/"));
			return $this->display(__FILE__, 'invoice_block.tpl');

	}
	
		
	
	function createPaymentcardtbl()
	{
			/**Function called by install - 
			 * creates the "order_paymentcard" table required for storing payment card details
		     * Column Descriptions: id_payment the primary key. 
		     * id order: Stores the order number associated with this payment card
		     * cardholder_name: Stores the card holder name
		     * status: Stores the card number
		     * expiry date: Stores date the card expires
		     */
		    		    
                    $db = Db::getInstance(); 
            		$query = "CREATE TABLE `"._DB_PREFIX_."order_paymentcard` (
					`id_payment` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`id_order` INT NOT NULL ,
					`ordertime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
					`status` VARCHAR( 15 ) NOT NULL DEFAULT 'onstack' 
					) ENGINE = MYISAM ";
            		
	 		        $db->Execute($query);
		
			return true;
	}
		
	/*
     *  Call this function to save the payment card details to the payment card table
     */
	 

	function writePaymentcarddetails($id_order, $status, $tran_id)
	{
		$db = Db::getInstance();
		$query1="UPDATE ps_order_payment
SET transaction_id=".$tran_id." WHERE id_order_payment=".($id_order-1).";";
		$db->Execute($query1);
		/*$result = $db->Execute('
		UPDATE ps_order_payment
		SET transaction_id="'.$tran_id.'"
		WHERE id_order_payment="'.$id_order.'"');*/
		$result = $db->Execute('
		INSERT INTO `ps_order_paymentcard`
		( `id_order`,`status`)
		VALUES
		("'.intval($id_order).'","'.$status.'")');
		return;
	}
	
    /*
     *  Call this function to read the payment card details from the payment card table
     */
	function readPaymentcarddetails($id_order)
	{
		$db = Db::getInstance();
		$result = $db->ExecuteS('
		SELECT * FROM `ps_order_paymentcard`
		WHERE `id_order` ="'.intval($id_order).'";');
		return $result[0];
	}
	
}	

?>
