<?php
include_once 'header.php';
?>
	<div class="row">
    <?php
    	$imgurl = '/CI/img/';
       	foreach ($products as $row) { 
    ?>
            <div class="box">

                <a href="products/show/<?= $row->id ?>">
                    <img src="<?= $imgurl . $row->image ?>" />
                    <h3>
                        <?= $row->name ?>
                    </h3>
                    <p>
                        <?= $row->price ?> 
                    </p>
                </a>

            </div>
    <?php
      	}
    ?>

    </div>

</body>
</html>