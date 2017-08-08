<?php

include_once 'header.php';

?>
	<table>
		<form action="<?= base_url('cart/add') ?>" method="GET">
			<?php
		    	$imgurl = '/CI/img/';
	    	?>
		        <tr>
		            <td>
		            	<input type="hidden" name="product_id" value="<?= $product->id ?>" />
	                    Product ID : <?= $product->id ?> <br>
	                    Poduct Name : <?= $product->name ?> <br>
	                    Price : <?= $product->price ?> <br>
	                    Description :- <br> <?= $product->description ?> <br><br>
	                    Quantity : <input type="number" name="quantity" value="1" min="1" max="10">
		            </td>
		            <td></td>
		            <td>
		                <img src=" <?= $imgurl.$product->image ?>" width="200" height="250" hspace="20" vspace="20">
		            </td>
		        </tr>
		        <tr>
		        	<td>
		        		<input type="submit" value="Add To Cart">
		        	</td>
		        </tr>
			</form>	
	    </table>
</body>
</html>