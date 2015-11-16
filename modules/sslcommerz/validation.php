<?php 
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/sslcommerz.php');
			
$ordertime     = "NULL";
$status        = "on validation";
if(isset($_POST['val_id'])){
$tran_id= $_POST['tran_id'];
$val_id = $_POST['val_id'];

$total = intval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));
$store_id = trim(Configuration::get('SSLCOMMERZ_MERCHANT_ID'));
$store_passwd = trim(Configuration::get('SSLCOMMERZ_WORKING_KEY'));
$sslcommerz = new sslcommerz();
 $soapURL=$sslcommerz->getsoapUrl();
 if($soapURL=='yes')
 {
    $requested_url = ("https://www.sslcommerz.com.bd/validator/api/testbox/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json"); 
 }else
 {
     $requested_url = ("https://www.sslcommerz.com.bd/validator/api/validationserverAPI.php?val_id=".$val_id."&Store_Id=".$store_id."&Store_Passwd=".$store_passwd."&v=1&format=json"); 
 }
 //echo $requested_url;exit;
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $requested_url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                $result = curl_exec($handle);
                $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                if($code == 200 && !( curl_errno($handle)))
                {
                    $result = json_decode($result);
	$status = $result->status;	
	$tran_date = $result->tran_date;
	$tran_id = $result->tran_id;
    $tran_id = trim(strstr($tran_id, '_',true));
	$val_id = $result->val_id;
	$amount = intval($result->amount);
	$store_amount = $result->store_amount;
	$bank_tran_id = $result->bank_tran_id;
	$card_type = $result->card_type;
	if(($status=='VALID')&&($amount==$total))
	{
                        $pay_status = 'success';
	}
	else
	{
                        $pay_status = 'failed';
	}
                }
	
if ($pay_status == 'success') {


$currency = new Currency(intval(isset($_POST['currency_payement']) ? $_POST['currency_payement'] : $cookie->id_currency));
$total = floatval(number_format($cart->getOrderTotal(true, 3), 2, '.', ''));


$sslcommerz->validateOrder($cart->id, _PS_OS_PAYMENT_, $total, $sslcommerz->displayName, NULL, NULL, $currency->id);
$order = new Order($sslcommerz->currentOrder);
//$status = "paid";
/*$sslcommerz->writePaymentcarddetails($order->id, $ordertime, $status);*/
//$sslcommerz->writePaymentcarddetails($order->id, $status, $tran_id);

	//die();
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$sslcommerz->id.'&id_order='.$sslcommerz->currentOrder.'&key='.$order->secure_key);


}

else
{
Tools::redirect('modules/sslcommerz/failed.php');
}


}

else
{
Tools::redirect('modules/sslcommerz/failed.php');
}


?>