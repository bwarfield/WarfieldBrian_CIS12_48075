<?php 
	$page_title = 'Product Administration';
	include('includes/header.html');

	#validate product ID, exit if invalid
	if($_SERVER['REQUEST_METHOD']=='POST' && !isset($_POST_["edit"])){
		if(isset($_POST['product_id']) && is_numeric($_POST['product_id'] )){
			$pro_id=$_POST['product_id'];
			@require("project_DBconnect.php");
		}else{
		 	echo '<h1 class="error">ERROR!</h1><p>This page has been reached in error. <a href="index.php">Please try again</a></p>';
		 	include('includes/footer.html');
		 	exit();
		}
	}
	#validate posted form values
	if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["edit"])){
		
		$errors = array();
		if(empty($_POST['name'])){
			$errors[] = "Missing Name.";
		}else{
			$name = mysqli_real_escape_string($dbc, trim($_POST['name']));
		}
		if(empty($_POST['sku'])){
			$errors[] = "Missing SKU.";
		}else{
			$sku = mysqli_real_escape_string($dbc, trim($_POST['sku']));
		}
		if(empty($_POST['short_description'])){
			$errors[] = "Missing Short Description.";
		}else{
			$sd = mysqli_real_escape_string($dbc, trim($_POST['short_description']));
		}
		if(empty($_POST['long_description'])){
			$errors[] = "Missing Long Description.";
		}else{
			$ld = mysqli_real_escape_string($dbc, trim($_POST['long_description']));
		}
		if(empty($_POST['on_hand_qty'])){
			$errors[] = "Missing On Hand Quantity.";
		}else{
			$oh_qty = mysqli_real_escape_string($dbc, trim($_POST['on_hand_qty']));
			if(!preg_match("/^\d+$/",$oh_qty)){
				$errors[] = "Positive integers only for On-Hand Quantity.";
			}
		}
		if(isset($_POST['taxable'])){#form input checkbox
			$tax = 1;
		}else{
			$tax = 0;
		}

		if(empty($_POST['price'])){
			$errors[] = "Missing Price.";
		}else{
			$price = mysqli_real_escape_string($dbc, trim($_POST['price']));
			if(!is_numeric($price)){
				$errors[] = "Price must be numeric value.";
			}
		}

		if(empty($_POST['cost'])){
			//$errors[] = "Missing Cost.";
		}else{
			$cost = mysqli_real_escape_string($dbc, trim($_POST['cost']));
			if(!is_numeric($cost)){
				$errors[] = "Cost must be numeric value.";
			}
		}

		if(empty($_POST['manufacturer_id'])){
			//$errors[] = "Missing Manufacturer.";
		}else{
			$man_id = mysqli_real_escape_string($dbc, trim($_POST['manufacturer_id']));
			if(!is_numeric($manufacturer_id)){
				$errors[] = "Manufacturer assignment error: Contact administrator.";
			}
		}
		if(empty($_POST['upc'])){
			//$errors[] = "Missing UPC.";
		}else{
			$upc = mysqli_real_escape_string($dbc, trim($_POST['upc']));
			if(!is_numeric($upc)){
				$errors[] = "UPC must be numeric value.";
			}
		}
		if(empty($_POST['shipping_weight'])){
			//$errors[] = "Missing Shipping Weight.";
		}else{
			$sw = mysqli_real_escape_string($dbc, trim($_POST['shipping_weight']));
			if(!is_numeric($sw)){
				$errors[] = "Shipping weight must be numeric value.";
			}
		}
		if(empty($_POST['country_id'])){
			//$errors[] = "Missing Manufacturer.";
		}else{
			$country_id = mysqli_real_escape_string($dbc, trim($_POST['country_id']));
			if(!is_numeric($country_id)){
				$errors[] = "Country assignment error: Contact administrator.";
			}
		}
		if(empty($errors)){#if no errors
			@require('project_DBconnect.php');
			#update DB record with validated values
			$q = "UPDATE entity_products SET `name` = $name, `sku` - $sku, `short_description` = $sd, `long_description` = $ld, `on_hand_qty` = $oh_qty, `taxable`= $tax, `price` = $price, `cost` = $cost, `manufacturer_id` = $man_id, `upc` = $upc, `shipping_weight` = $sw, `country_id` = $county_id WHERE `product_id` = $pro_id";
			echo '<p>'.$q.'</p>';

		}else{#Report all the errors
			echo '<h1 class="error">ERROR!ERROR!ERROR!ERROR!</h1>
			<p class="error">The following errors occured at the form validation<br /><br />';
			foreach($errors as $msg){
				echo ' - '.$msg.'<br />';
			}
			echo "</p><p>Please check the required fields and sumbit again.</p><p>&nbsp;</p>";
		}#end if (empty($errors)) check
	}#end if($_SERVER['REQUEST_METHOD']=='POST' && $_POST_["edit"])

	#get enumerated list of Coutry IDs
	$q = 'SELECT `country_id`, `country` FROM `bladeshop`.`enum_country` AS `enum_country`';
	$r = mysqli_query($dbc, $q);
	$enum_country = array();
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
		$enum_country[$row['country_id']]=$row['country'];
	}

	#get enumerated list of manufacturer IDs
	$q = 'SELECT `manufacturer_id`, `manufacturer_name` FROM `bladeshop`.`enum_manufacturer` AS `enum_country`';
	$r = mysqli_query($dbc, $q);
	$enum_manufacturer = array();
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
		$enum_manufacturer[$row['manufacturer_id']]=$row['manufacturer_name'];
	}	 
	 	


	$q = 'SELECT `entity_products`.`product_id`, `entity_products`.`name`, `entity_products`.`sku`, `entity_products`.`short_description`, `entity_products`.`long_description`, `entity_products`.`on_hand_qty`, `entity_products`.`taxable`, `entity_products`.`price`, `entity_products`.`cost`, `entity_products`.`manufacturer_id`, `enum_manufacturer`.`manufacturer_name`, `entity_products`.`upc`, `entity_products`.`shipping_weight`, `entity_products`.`country_id`, `enum_country`.`country` FROM `bladeshop`.`entity_products` AS `entity_products`, `bladeshop`.`enum_country` AS `enum_country`, `bladeshop`.`entity_atribute_set` AS `entity_atribute_set`, `bladeshop`.`enum_manufacturer` AS `enum_manufacturer` WHERE `entity_products`.`country_id` = `enum_country`.`country_id` AND `entity_products`.`atribute_set_id` = `entity_atribute_set`.`atribute_set_id` AND `entity_products`.`manufacturer_id` = `enum_manufacturer`.`manufacturer_id` AND `entity_products`.`product_id` = '.$pro_id.';';
	
	$r = mysqli_query($dbc, $q);

	echo '<form action="admin_edit_item.php" method="POST">';
	$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	echo '
	<p><label for="name">Name *:</label><input name="name" type="text" value="'.htmlspecialchars($row['name']).'"></p>
	<p><label for="sku">SKU *:</label><input name="sku" type="text" value="'.htmlspecialchars($row['sku']).'"></p>
	<p><label for="short_description">Short Description *</label><input name="short_description" type="text" value="'.htmlspecialchars($row['short_description']).'"></p>
	<p><label for="long_description">Long Description *:</label><input name="long_description" type="text" value="'.htmlspecialchars($row['long_description']).'"></p>
	<p><label for="on_hand_qty">On Hand Quantity *:</label><input name="on_hand_qty" type="text" value="'.$row['on_hand_qty'].'"></p>
	<p><label for="taxable">Taxable :</label><input name="taxable" type="checkbox" value="1" '.($row['taxable']?"checked":"").'></p>
	<p><label for="price">Price *:</label><input name="price" type="text" value="'.$row['price'].'"></p>
	<p><label for="cost">Cost :</label><input name="cost" type="text" value="'.$row['cost'].'"></p>
	<p><label for="cost">Manufacturer :</label><select name="manufacturer_id">';
		foreach ($enum_manufacturer as $key => $value) {
			//echo "<p>DEBUG: Key: $key Value: $value Row[id]: ".$row['manufacturer_id']."</p>";
			echo '<option value="'.$key.'" '.($row['manufacturer_id']==$key?'selected':'').'>'.$value.'</option>';
		}
	
		echo'
		</select></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	<p><label for="name">Name</label><input name="name" type="text" value="'.$row['name'].'"></p>
	 ';
	 	
	 echo '</form>';
	mysqli_free_result ($r);
	mysqli_close($dbc);
	include ('includes/footer.html');

?>
