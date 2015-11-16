<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/sslcommerz.php');

$smarty->assign(array(
	'amount' => floatval($cart->getOrderTotal(true, 4)),
	'total' => floatval($cart->getOrderTotal(true, 3)),
	'cart_id' => intval($cart->id)
));
if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
	
$sslcommerz = new sslcommerz();
echo $sslcommerz->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>