<?php

	include_once 'header.php';
?>
	<table>
		<?php
			$imgurl = '/CI/img/';
			$grandtotal = 0 ;
			foreach ($cart as $product) {
				$total = $product['quantity'] * $product['detail']->price ;
		?>	
				<tr>
					<td>
	                    Name : <?= $product['detail']->name ?> <br>
	                    Price : <?= $product['detail']->price ?> <br>
	                    Quantity : <?= $product['quantity'] ?> <br>
	                    Total : <?= $total ?>
					</td>
					<td>
						<img src="<?= $imgurl . $product['detail']->image ?>" height="110" width="100">
					</td>
				</tr>

		<?php
			}
		?>

				<tr>
					<td> Grand Total : <?= $this->session->total ?> </td>
				</tr>

				<tr> <td></td> </tr>

				<tr>
					<td>
						<a href="/CI/checkout">
							<input type="submit" value="Checkout & Proceed">
						</a>
					</td>
					<td>
						<a href="/CI/home">
							<input type="submit" value="Continue Shopping">
						</a>
					</td>
				</tr>

	</table>

</body>
</html>