<p class="payment_module">
	<a href="javascript:$('#sslcommerz_form').submit();" title="{l s='Pay with SSLCOMMERZ' mod='sslcommerz'}">
		<img src="{$module_template_dir}ssl_commerz_logo.png" alt="{l s='Pay with SSLCOMMERZ' mod='sslcommerz'}" />
		{l s='Pay with SSLCOMMERZ' mod='sslcommerz'}
	</a>
	
</p>

<form action="{$actionUrl}" method="post" id="sslcommerz_form" class="hidden">
	<input type="hidden" name="store_id" readonly	value="{$MerchantId}">
	<input type= "hidden" name="total_amount" readonly value="{$Amount}">
	<input type=hidden name="tran_id"	readonly value="{$OrderId}">
	<input type=hidden name="success_url"readonly value="{$Redirect_Url}">
	<input type=hidden name="fail_url"	readonly value="{$Fail_Url}">
	<input type=hidden name="cancel_url" readonly value="{$Cancel_Url}">
</form>
