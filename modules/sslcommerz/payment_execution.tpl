{capture name=path}{l s='Shipping'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div align="center">Please Wait... <br/> <img src="wait_clock.gif" /> </div>
<form action="https://www.sslcommerz.com.bd/gwprocess/index.php" method="POST" name="myForm" id="myForm" >
	<table border="0">

		<tr>

			<td>

				{l s='Order Time:' mod='sslcommerz'}
			</td>
			<td>
				<input type="hidden" name="ordertime" id="ordertime" value="{$ordertime}"/> <br />
                <!-- <input type="text" name="total_amount" id="ordertime" value="{$amount}"/>-->
                <input type="hidden" name="total_amount" id="ordertime" value="{$total}"/>
                <input type="hidden" name="tran_id" value="{$cart_id}">
                <input type="hidden" name="store_id" value="test"> <!-- put your store ID here -->
        		<input type="hidden" name="success_url" value="{$this_path_ssl}validation.php">       
        		<input type="hidden" name="fail_url" value="{$this_path_ssl}failed.php"> 
                <input type="hidden" name="cancel_url" value="{$this_path_ssl}cancel.php">
               
			</td>

		</tr>

		<tr>
			<td>
				{l s='Payment Status:' mod='sslcommerz'}
			</td>
			<td>
				<input type="hidden" name="status" id="status" value="{$status}" />
			</td>
		</tr>
		
	</table>


</form>
<script type='text/javascript'>document.myForm.submit();</script>