<?php  

	include_once 'header.php';
?>
		<table>

			<tr>
				<th> Your Order Details :  </th>
			</tr>

			<tr>
				<td>	Order Id : 			</td>
				<td> 	<?= $order->id ?> 	</td>
			</tr>

			<tr>
				<td>	Status :	 		</td>
				<td>	<?= $order->status ?>	</td>
			</tr>

			<tr>
				<td>	Order Date :				</td>
				<td>	<?= $order->order_date ?>	</td>
			</tr>

			<tr>
				<td>	Total Amount :			</td>
				<td>	<?= $total ?>	</td>
			</tr>
		</table>

        <h3> Thanks for purchasing our products.. </h3>

        Back To <a href="/CI/home"> Home </a>

 </body>
 </html>