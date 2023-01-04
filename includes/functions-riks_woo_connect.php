<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Database connect
function riks_wac_database(){
    global $wpdb;
    $table_name = $wpdb->prefix.'riks_wac';
    $connects = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
    $websites = array();
    foreach($connects as $connect){
        $websites[$connect->scode] = ['name'=>$connect->name, 'url'=>$connect->api_url, 'auth'=>$connect->api_key];
    }
    return $websites;
}
function riks_woo_connect_menu(){    
    $page_title = 'RiKS WooCommerce Connect';   
    $menu_title = 'RiKS';   
    $capability = 'manage_options';   
    $menu_slug  = 'riks-woo-connect-home';   
    $function   = 'riks_woo_connect_home';   
    $icon_url   = 'dashicons-admin-multisite';   
    $position   = 4;    
    add_menu_page( $page_title,$menu_title,$capability,$menu_slug,$function,$icon_url,$position ); 
}
function riks_woo_connect_setting_menu(){
    $parent_slug = 'riks-woo-connect-home';
    $page_title = 'RiKS Woo Connect - Settings';   
    $menu_title = 'Settings';   
    $capability = 'manage_options';   
    $menu_slug  = 'riks-woo-connect-settings';   
    $function   = 'riks_woo_connect_settings';   
    add_submenu_page( $parent_slug,$page_title,$menu_title,$capability,$menu_slug,$function ); 
}
function riks_woo_connect_menu_copy_to(){
    $parent_slug = 'riks-woo-connect-home';
    $page_title = 'RiKS Woo Connect - Copy From';   
    $menu_title = 'Copy to';   
    $capability = 'manage_options';   
    $menu_slug  = 'riks-woo-connect-copy-to';   
    $function   = 'riks_woo_connect_copy_to';   
    add_submenu_page( $parent_slug,$page_title,$menu_title,$capability,$menu_slug,$function ); 
}
function riks_woo_connect_menu_copy_from(){
    $parent_slug = 'riks-woo-connect-home';
    $page_title = 'RiKS Woo Connect - Copy From';   
    $menu_title = 'Copy from';   
    $capability = 'manage_options';   
    $menu_slug  = 'riks-woo-connect-copy-from';   
    $function   = 'riks_woo_connect_copy_from';   
    add_submenu_page( $parent_slug,$page_title,$menu_title,$capability,$menu_slug,$function ); 
}
function riks_woo_connect_settings(){
    global $wpdb;
    $table_name = $wpdb->prefix.'riks_wac';
    $results = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
    
    $riks_wac_htmls = new RiKS_WAC_htmls();
    //Display form to add new API
    echo $riks_wac_htmls->riks_wac_new_connection_html();
    //Display list of APIs recorded in database
    echo $riks_wac_htmls->riks_wac_connections($results);
    
    //Add connection settings to database
    if(array_key_exists('saveConnection', $_POST)){
        $name = $_POST['name'];
        $url = $_POST['url'];
        $nickname = $_POST['code'];
        $key = base64_encode($_POST['key'].":".$_POST['secret']);
        $wpdb->insert( 
        	$table_name, 
        	array( 
        		'name' => $name, 
        		'scode' => $nickname,
        		'api_url' => $url, 
        		'api_key' => $key, 
        	) 
        );
    }if(array_key_exists('delConnection', $_POST)){
        $id = $_POST['con_id'];
        $wpdb->delete( 
        	$table_name, 
        	array( 
        		'id' => $id, 
        	) 
        );
    }
}
//Copy from this site to other site
function riks_woo_connect_copy_to(){
    $websites = riks_wac_database();
    $riks_wac_htmls = new RiKS_WAC_htmls();
    echo $riks_wac_htmls->riks_wac_product_search_form($websites);

    $riks_wac_copy_to_obj = new RiKS_WAC_copy_to();
    if(array_key_exists('search', $_POST)){
        $website = $_POST['website'];
        $site_identity = $websites[$website];
        
        
        $product = $riks_wac_copy_to_obj -> riks_wac_local_product_by_id_sku($_POST['search_term']);
        if (!empty($product)){
            echo "<form method=\"post\">
        	    <div class=\"riks_woo_api_connect_text_label\">Name:</div> <div class=\"riks_woo_api_connect_textfield\"><input type='hidden' name='destination' value = '".$website."'><input type=\"text\" name=\"product_name\" value =\"".$product->get_title()."\" size = \"100\"></div>
        	    <div class=\"riks_woo_api_connect_text_label\">Sku: </div> <div class=\"riks_woo_api_connect_textfield\"><input type=\"text\" name=\"product_sku\" value =\"".$product->get_sku()."\" size = \"100\"><input type=\"hidden\" name=\"product_id\" value =\"".$product->get_id()."\"></div>
        	    <div class=\"riks_woo_api_connect_text_label\">Status: </div> <div class=\"riks_woo_api_connect_textfield\"><input type=\"radio\" name=\"status\" value =\"publish\" > Published <input type=\"radio\" name=\"status\" value =\"draft\" checked> Draft</div>
        	    <div class=\"riks_woo_api_connect_text_label\">Price: </div> <div class=\"riks_woo_api_connect_textfield\"><input type=\"text\" name=\"product_price\" value =\"".$product->get_price()."\"></div>
        	   
        	    <div class=\"riks_woo_api_connect_text_label\">Description: </div><div class=\"riks_woo_api_connect_textfield\">";
        	    wp_editor($product->get_description(),'product_description',$settings = array('textarea_name'=>'product_description'));
        	echo "</div><div class=\"riks_woo_api_connect_text_label\">Short Description: </div><div class=\"riks_woo_api_connect_textfield\">";
        	    wp_editor($product->get_short_description(),'short_description',$settings = array('textarea_name'=>'short_description'));
        	echo "</div>";
            echo "<div class='riks_wac_image_holder'>";
        	//Display product images
        	$main_image_id = $product->get_image_id();
        	echo "<input type='hidden' name='product_images[]' value = '".wp_get_attachment_url( $main_image_id )."'>";
        	echo "<img src=".wp_get_attachment_url( $main_image_id )." width=\"100px\"> <br/>";
        	
        	$gallery_image_ids = $product->get_gallery_image_ids();

            foreach( $gallery_image_ids as $gallery_image_id ) {
                echo "<input type='hidden' name='product_images[]' value = '".wp_get_attachment_url( $gallery_image_id )."'>";
                echo "<img src=".wp_get_attachment_url( $gallery_image_id )." width=\"100px\"> ";
            }
        	
        	 
        	echo "</div>";
        	//Display destination categories
        
            $product_categories1 = $riks_wac_copy_to_obj -> riks_wac_get_remote_categories($site_identity,"1");
            $product_categories2 = $riks_wac_copy_to_obj -> riks_wac_get_remote_categories($site_identity,"2");
            $product_categories3 = $riks_wac_copy_to_obj -> riks_wac_get_remote_categories($site_identity,"3");
            $product_categories = array_merge($product_categories1,$product_categories2,$product_categories3);
            $parent_cats = array();
            
            $category = array(
            	'categories' => array(),
            	'parent_cats' => array()
            );
            foreach($product_categories as $cat){
                $category['categories'][$cat['id']] = $cat;
            	$category['parent_cats'][$cat['parent']][] = $cat['id'];
            }
            echo "<p><b>Destination Category</b></p><div class='riks_wac_category_wraper'>";
            echo riks_builtCategoryTree(0,$category);
            echo "</div>";
        	echo "<input type=\"submit\" name=\"remote_copy\" class=\"button\" value=\"Copy Product\" />";
        	echo "</form>";
        }
        else{
            echo "Product not found";
        }
    }
    else if (array_key_exists('remote_copy', $_POST)){
        
        $product = $riks_wac_copy_to_obj -> riks_wac_local_product_by_id_sku($_POST['product_id']);
        $metas = $product -> get_meta_data();
        $meta_no_id = array();
        foreach ($metas as $meta){
            array_push($meta_no_id,['key'=>$meta->key,'value'=>$meta->value]);
        }
        $product_categories = array();
        foreach ($_POST['product_categories'] as $product_cat){
            array_push($product_categories,['id'=> $product_cat]);
        }
        $product_images = array();
        foreach ($_POST['product_images'] as $image){
            array_push($product_images,['src' => $image]);
        }
        $product_data = [
            'name'          => $_POST['product_name'],
            'description'   => $_POST['product_description'],
            'short_description' => $_POST['short_description'],
            'regular_price' => $_POST['product_price'],
            'status'        => $_POST['status'],
            'images'        => $product_images,
            'categories'    => $product_categories,
            'sku'           => $_POST['product_sku'],
            'attributes'    => $product->get_attributes(),
            'backorders'    => $product->get_backorders(),
            'manage_stock'    => $product->get_manage_stock(),
            'meta_data'     => $meta_no_id
        ];
        $website = $_POST['destination'];
        $site_identity = $websites[$website];
        $riks_wac_copy_to_obj -> riks_wac_put_remote_product($site_identity,json_encode($product_data));
        echo "SKU ".$_POST['product_sku']."Copied";
    }
}

function riks_woo_connect_home(){
    //echo "Home here";
    $riks_wac_htmls = new RiKS_WAC_htmls();
    $request = wp_remote_get( 'https://wordpress.riksiot.com/wp-json/wp/v2/pages/?slug=welcome' );
    echo $riks_wac_htmls->riks_wac_channel_news_html($request);

}

//Copy from other site to this site
function riks_woo_connect_copy_from(){
   $websites = riks_wac_database();
   $riks_wac_copy_from_obj = new RiKS_WAC_copy_from();
   $riks_wac_htmls = new RiKS_WAC_htmls();
   
   //Select source to list new products
    echo "<div class='riks_wac_form_wraper'>
        <form method=\"post\">
        <select name='website' class = 'riks_wac_input_select'>";
        foreach($websites as $website){
            $site_index = array_search( $website['name'], array_column( $websites, 'name' ) );
            $site_id = array_keys($websites)[$site_index];
    echo "
          <option value=\"".$site_id."\">".$website['name']."</option>";
        }
    echo "
        </select>
        <input type='hidden' name='page' value='1'>
        <input type='submit' name='listProduct' class='riks_wac_submit_button' value='View Products' >
    </form>"; 
    if(array_key_exists('listProduct', $_POST)) {
        $page           = $_POST['page'];
        $website        = $_POST['website'];
        $site_identity  = $websites[$website];
        $products = $riks_wac_copy_from_obj -> riks_wac_get_products($site_identity,$page);
        echo $riks_wac_htmls -> riks_wac_product_list($products,$website,$page);
    }
    //Start copying
    if(array_key_exists('copy', $_POST)) {
        
        if(riks_woo_api_connect_check_existed_product($_POST['product_sku'])){
            echo "Product Existed!";
        }
        else {
    	    //get productdetail
    	    $website = $_POST['website'];
            $site_identity = $websites[$website];
    	    $product = riks_woo_api_connect_product_by_id($_POST['product_id'],$site_identity);
    	    
    	    //get images
    	    $images =  $product['images'];
    	    include_once( ABSPATH . 'wp-admin/includes/image.php' );
            $product_images = [];
            $i=0;
    	    foreach ($images as $image){
        	    $full_url = $image['src'];
                array_push($product_images,['src' => $full_url,'position'=>$i]);
                $i++;
        	    
        	}
        	
        	//Assign product categories
            $product_categories = [];
        	$selected_cats = $_POST['product_categories'];
        	foreach($selected_cats as $selected_cat){
        	    array_push($product_categories,['id' =>$selected_cat]);
        	}
        	$meta_datas = $product['meta_data'];
        	$metas = [];
        	foreach ($meta_datas as $meta){
        	    array_push($metas,['key' =>$meta['key'],'value'=>$meta['value']]);
        	}
        	$product_attributes = $product['attributes'];
        	$attributes = [];
        	foreach($product_attributes as $product_attribute){
        	    array_push($attributes,
            	   [
        	        'name' =>$product_attribute['name'],
        	        'position'=>$product_attribute['position'],
        	        'visible'=>$product_attribute['visible'],
        	        'variation'=>$product_attribute['variation'],
        	        'options'=>$product_attribute['options']
            	   ]
        	   );
        	}
    	    $product_data = [
                'name'          => $_POST['product_name'],
                'description'   => $_POST['product_description'],
                'short_description' => $_POST['short_description'],
                'regular_price' => $_POST['product_price'],
                'status'        => $_POST['status'],
                'images'        =>$product_images,
                'categories'    => $product_categories,
                'sku'           => $_POST['product_sku'],
                'meta_data'     => $metas ,
                'attributes'    => $attributes,
                'backorders'    => $product['backorders'],
                'manage_stock'    => $product['manage_stock'],
            ];
            //Created product
            riks_woo_api_connect_create_product($product_data);
            echo "Product copied";
        }
    }  
    
    //Search product
    if(array_key_exists('search', $_POST)) {
        //$riks_wac_editor = new RiKS_WAC_editor();
        $website = $_POST['website'];
        if(!empty($website)){
            $site_identity = $websites[$website];
            $product = riks_woo_api_connect_product_by_id($_POST['product_id'],$site_identity);
            echo $riks_wac_htmls -> riks_wac_product_editor($product,$website);
        }
    }

}


function riks_woo_api_connect_product_by_id($id,$site_identity){
    $url = $site_identity['url'];
    $key = $site_identity['auth'];
    $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, ''.$url.'/wp-json/wc/v3/products/'.$id);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	$headers = array();
	$headers[] = 'Authorization: Basic '.$key.'';
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	curl_close($ch);
	$product = json_decode($result, true);
    return $product;
    
}
function riks_woo_api_connect_product_by_sku($sku){
    
}

function riks_woo_api_connect_create_product($data){
    $request = new WP_REST_Request( 'POST' );
    $request->set_body_params( $data );
    $products_controller = new WC_REST_Products_Controller;
    $response = $products_controller->create_item( $request );
    return $response;
}
function riks_woo_api_connect_check_existed_product($sku){
    if ($sku == wc_get_product_id_by_sku( $sku )){
        return true;
    }
    else
        return false;
}
function riks_woo_api_connect_update_product($data){
    
}
function riks_builtCategoryTree($parent,$category){
	$html = "";
    if(isset($category['parent_cats'][$parent])){
    	$html .= "<ul class='riks_css_ul'>\n";
        foreach($category['parent_cats'][$parent] as $cat_id){
        	if (!isset($category['parent_cats'][$cat_id])) {
	            $html .= "<li>\n <input type='checkbox' name='product_categories[]' value='".$category['categories'][$cat_id]['id']."'>" . $category['categories'][$cat_id]['name'] . "</li> \n";
            }
            if (isset($category['parent_cats'][$cat_id])) {
					$html .= "<li>\n <input type='checkbox' name='product_categories[]' value='".$category['categories'][$cat_id]['id']."'>" . $category['categories'][$cat_id]['name'] ;
					$html .= riks_builtCategoryTree($cat_id, $category);
					$html .= "</li> \n";
			}
        }
        $html .= "</ul> \n";
    }
    return $html;

}
function riks_wac_bulk_copy(){
    $websites = riks_wac_database();
    $website = $_POST['website'];
    $site_identity = $websites[$website];
    
    $productids=$_POST['submitedIDs'];
    $submited_products= sizeof($productids);
    $skus = array();
    foreach($productids as $id){
        $remote_product = riks_woo_api_connect_product_by_id($id,$site_identity);
        $images =  $remote_product['images'];
	    include_once( ABSPATH . 'wp-admin/includes/image.php' );
        $product_images = [];
        $i=0;
	    foreach ($images as $image){
    	    $full_url = $image['src'];
            array_push($product_images,['src' => $full_url,'position'=>$i]);
            $i++;
    	}
    	
    	
    	$meta_datas = $remote_product['meta_data'];
    	$metas = [];
    	foreach ($meta_datas as $meta){
    	    array_push($metas,['key' =>$meta['key'],'value'=>$meta['value']]);
    	}
    	
    	$product_attributes = $remote_product['attributes'];
    	$attributes = [];
    	foreach($product_attributes as $product_attribute){
    	    array_push($attributes,
        	   [
    	        'name' =>$product_attribute['name'],
    	        'position'=>$product_attribute['position'],
    	        'visible'=>$product_attribute['visible'],
    	        'variation'=>$product_attribute['variation'],
    	        'options'=>$product_attribute['options']
        	   ]
    	   );
    	}
    	
        $product_data = [
            'name'              => $remote_product['name'],
            'description'       => $remote_product['description'],
            'short_description' => $remote_product['short_description'],
            'regular_price'     => $remote_product['regular_price'],
            'status'            => $remote_product['status'],
            'images'            =>$product_images,
            'sku'               => $remote_product['sku'],
            'meta_data'         => $metas ,
            'attributes'        => $attributes,
            'backorders'        => $remote_product['backorders'],
            'manage_stock'      => $remote_product['manage_stock'],
        ];
        
        //Created product
        $wp_response = riks_woo_api_connect_create_product($product_data);
        if(is_wp_error($wp_response)){
            echo "There is something wrong! Product could not be coppied";
        }
        else{
            array_push($skus,['sku'=>$remote_product['sku'],'name'=>$remote_product['name']]);
        }
    }
    if(!empty($skus)){
        foreach ($skus as $sku){
            
            echo "SKU: ".$sku['sku']." Successfully copied <br>";
        }
    }
}
add_action('wp_ajax_riks_wac_bulk_copy', 'riks_wac_bulk_copy');

?>