<form method="post">
	    <div class="riks_woo_api_connect_text_label">Name:</div> 
	    <div class="riks_woo_api_connect_textfield"><input type="text" name="product_name" value ="<?php echo $product['name'];?>" size = "100"></div>
	    <div class="riks_woo_api_connect_text_label">Sku: </div> 
	    <div class="riks_woo_api_connect_textfield">
	        <input type="text" name="product_sku" value ="<?php echo $product['sku'];?>" size = "100">
	        <input type="hidden" name="product_id" value ="<?php echo $product['id'];?>">
        </div>
	    <div class="riks_woo_api_connect_text_label">Status: </div> 
	    <div class="riks_woo_api_connect_textfield"><input type="radio" name="status" value ="publish" > Published <input type="radio" name="status" value ="draft" checked> Draft</div>
	    <div class="riks_woo_api_connect_text_label">Price: </div> 
	    <div class="riks_woo_api_connect_textfield"><input type="text" name="product_price" value ="<?php echo $product['regular_price'];?>"></div>
	   
	    <div class="riks_woo_api_connect_text_label">Description: </div>
	    <div class="riks_woo_api_connect_textfield"><?php echo wp_editor($product['description'],'product_description',$settings = array('textarea_name'=>'product_description'));?></div>
	    <div class="riks_woo_api_connect_text_label">Short Description: </div>
	    <div class="riks_woo_api_connect_textfield"><?php echo wp_editor($product['short_description'],'short_description',$settings = array('textarea_name'=>'short_description'));?></div>
	    