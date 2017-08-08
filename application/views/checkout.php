<?php  

	include_once 'header.php';

	echo "Item Selected : " .count($this->session->cart);
    echo "<br> Total Amount : " .$this->session->total;
?>

	<br><br>

	<form action="checkout/submit" method="GET" id="usrdtls" >
	    Provide your Details :- <br>
	    <table>
	            <tr>
	                <td>   Name :  </td>
	                <td>   <input type="text" name="name">    </td>
	            </tr>
	            <tr>
	                <td>   Mobile No : </td>
	                <td>   <input type="text" name="mobile">  </td>
	            </tr>
	            <tr>
	                <td>   Address : </td>
	                <td>  <textarea rows="4" cols="30" form="usrdtls" name="address"></textarea> </td>
	            </tr>
	            <tr>
	                <td>  <input type="submit" value="Submit">
	                <td>  <input type="reset"> </td>
	            </tr>
	    </table>
	</form>	

</body>
</html>