<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class RiKS_WAC_htmls{
    //Product editor
    public function riks_wac_product_editor($product,$website){
        if($product){
            ob_start();
                wp_editor($product['description'],'product_description',$settings = array('textarea_name'=>'product_description'));
            $desc_editor = ob_get_clean();
            ob_start();
                wp_editor($product['short_description'],'short_description',$settings = array('textarea_name'=>'short_description'));
            $short_desc_editor = ob_get_clean();
            
            $html = "";
            $html .= "<div class = 'riks_wac_wrapper'><form method='post'>
                <input type=\"hidden\" name=\"website\" value =\"".$website."\">
        	    <div class='riks_wac_text_label'>Name:</div> <div class='riks_wac_textfield'><input type='text' name='product_name' value ='".$product['name']."' size = '100'></div>
        	    <div class='riks_wac_text_label'>Sku: </div> <div class='riks_wac_textfield'><input type='text' name='product_sku' value ='".$product['sku']."' size = '100'><input type='hidden' name='product_id' value ='".$product['id']."'></div>
        	    <div class='riks_wac_text_label'>Status: </div> <div class='riks_wac_textfield'><input type='radio' name='status' value ='publish' > Published <input type='radio' name='status' value ='draft' checked> Draft</div>
        	    <div class='riks_wac_text_label'>Price: </div> <div class='riks_wac_textfield'><input type='text' name='product_price' value ='".$product['regular_price']."'></div>
        	    <div class='riks_wac_text_label'>Description: </div><div class='riks_wac_textfield'>".$desc_editor."</div>";
        	    
        	$html .=  "<div class='riks_woo_api_connect_text_label'>Short Description: </div><div class='riks_wac_textfield'>".$short_desc_editor."</div>";
    	    
    	    //Display product images
        	$images =  $product['images'];
        	
        	//$images_array = json_decode($images, true);
        	$i = 0;
        	$html .="<div class='riks_woo_api_connect_text_label'><b>Product Images: </b></div><div class='riks_wac_img_holder'>";
        	foreach ($images as $image){
        	    $srcurl = $image['src'];
        	    $html .= "<img src=".$srcurl." width='100px'> ";
        	    $i++;
        	}    
        	$html .= "</div>";
        	//Display product categories
        	$args = array(
                'hide_empty' => false
                
            );
            
            $product_categories = get_terms( 'product_cat', $args );
            
            $args = array(
                'taxonomy'   => 'product_cat',
                
                'hide_empty' => false,
                'parent'=>0
            );
            $html .=  "<div class='riks_woo_api_connect_text_label'><b>Product Category: </b></div><div class='riks_wac_cat_holder'>";
            $product_categories = get_categories( $args );
            $html .= "<ul>";
            foreach( $product_categories as $cat ) { 
                $html .=  "<li><input type='checkbox' name='product_categories[]' value='".$cat->term_id."'>";
                $html .=  "<b>".$cat->name."</b>"; 
                $child_args = array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                    
                    'parent'=> $cat->term_id
                );
                $child_cats = get_categories($child_args);
                $html .=  "<ul>";
                foreach ($child_cats as $child_cat){
                    $html .=  "<li>&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='product_categories[]' value='".$child_cat->term_id."'>".$child_cat->name."</li>";
                }
                $html .=  "</ul></li>";
            }
            $html .=  "</ul></div>";
        	$html .=  "<input type='submit' name='copy' class='button' value='Copy Product' />";
    	    $html .=   "</form></div>";
            return $html;
        }
    }
    //Product table to display a list of products
    public function riks_wac_product_list($products,$website,$page){
        $nextpage=$page+1;
        if($page > 0){
            $previouspage = $page-1;
        }
        else{
            $previouspage = $page;
        }
        $html = "<input type='hidden' id='selectedWebsite' value='".$website."'>";
        $html .= "
        <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>
        <script type=\"text/javascript\">
        	$(document).ready(function () {
            	var product_ids=[];
            	//Check/uncheck all checkboxes
                $('#checkbox').click(function(){
                    $('input:checkbox').not(this).prop('checked', this.checked);
                });
                
                if($('#prevPage').val()==0){
                    $('#prev_page_btn').hide();
                }
                //show/hide bulk copy button
                var selectedid = 'input[name=\"product_id\"]';
                
                $('#bulkCopyButton').hide();
                var checkedall = 'input[name=\"checkall\"]';
                $(document).on('change', selectedid, function() {
                  console.log($(selectedid).is(':checked'))
                  $('#bulkCopyButton').toggle($(selectedid).is(':checked'));
                });
                
                $(document).on('change', checkedall, function() {
                  console.log($(checkedall).is(':checked'))
                  $('#bulkCopyButton').toggle($(checkedall).is(':checked'));
                });
                //add parrameters then submit form        
                $('.riks_wac_progress').hide();
                $('.riks_wac_message').hide();
                $('#bulkCopyButton').click(function(){
                    $(\"input:checkbox[name=product_id]:checked\").each(function(){
                        product_ids.push($(this).val());
                    });
                    var data ={
                        'action': 'riks_wac_bulk_copy',
                        'submitedIDs[]': product_ids,
                        'website':$(\"#selectedWebsite\").val()
                    };
                    $.ajax({
                        type:'POST',
                        url: ajaxurl,
                        data: {
                            'action': 'riks_wac_bulk_copy',
                            'submitedIDs[]': product_ids,
                            'website':$(\"#selectedWebsite\").val()
                        },
                        beforeSend: function() {    
                            //alert('Start Copying');
                            $('.riks_wac_progress').show();
                            $('#bulkCopyButton').hide();
                        },
                
                        success: function (result) {
                            $('.riks_wac_progress').hide();
                            $('.riks_wac_message').html(result);
                            $('.riks_wac_message').show();
                        },
                        
                    });
                });
          
            });
        </script>
        <div class='riks_wac_progress' id='myProgress'>
          <div><img src='".get_site_url()."/wp-content/plugins/riks_woo_connect/public/images/processing.gif"."' class='riks_wac_loading'></div>
          <div>Working on it, please wait!</div>
        </div>
        <div class='riks_wac_message'></div>
<!-- Testing-->
        
<!-- End test -->
        <div class='riks_wac_bulk_copy_button' id='bulkcopyform'><form id='bulkcopy' method='post' name='bulkcopy'><input class='riks_wac_submit_button' type='button' id='bulkCopyButton' name='bulkCopyButton' value='Copy Selected'></form></div>
        <div class='riks_wac_page_navigation'>
            <div class='riks_wac_nav_btn'></div>
            <div class='riks_wac_nav_btn'></div>
        </div>
        <table class='riks_wac_table'>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><form method='post'><input type='hidden' name='website' value='".$website."'><input type='hidden' name='page' id='prevPage' value='".$previouspage."'><input type='submit' name='listProduct' id='prev_page_btn' value='Previous Page'></form></th>
                    <th><form method='post'><input type='hidden' name='website' value='".$website."'><input type='hidden' name='page' id='nextPage' value='".$nextpage."'><input type='submit' name='listProduct' value='Next Page'></form></th>
                </tr>
                <tr class='riks_wac_tr'>
                    <th class='riks_wac_th'><input type='checkbox' name='checkall' id='checkbox'></th>
                    <th class='riks_wac_th'>Image</th>
                    <th class='riks_wac_th'>SKU</th>
                    <th class='riks_wac_th'>Name</th>
                    <th class='riks_wac_th'>Price</th>
                    <th class='riks_wac_th'>Action</th>
                </tr>
            </thead>
            <tbody>";
        foreach($products as $product){
            $new_product_badge="";
            if (empty(wc_get_product_id_by_sku($product['sku']))){
                $new_badge_img = get_site_url()."/wp-content/plugins/riks_woo_connect/public/images/badge_new_corner.png";
                $new_product_badge = " <img class='riks_wac_new_product_badge' src='".$new_badge_img."'>";
            }
            else{
                $new_prod="";
            }
            $product_main_image ="";
            if(isset($product['images'][0])){
                $product_main_image = $product['images'][0]['src'];
            }
            $html .= "<tr class='riks_wac_tr'>
                <td class = 'riks_wac_td'><input type='checkbox' name='product_id' value='".$product['id']."' id='checkbox'></td>
                <td class = 'riks_wac_td'><div class='riks_wac_product_image_holder'><img class = 'riks_wac_product_image' src='".$product_main_image."'>".$new_product_badge."</div></td>
                <td class = 'riks_wac_td'>".$product['sku']."</td>
                <td class = 'riks_wac_td'>".$product['name']."</td>
                <td class = 'riks_wac_td'>$".$product['regular_price']."</td>
                <td class = 'riks_wac_td'><form method='post'><input type='hidden' name='website' value='".$website."'><input type='hidden' name='product_id' value='".$product['id']."'><input type='submit' name='search' value='View'></form></td>
            </tr>";
            //echo $product['name']."<br>";
        }
        $html .= "</tbody></table>";
        
        return $html;
    }
    //Add new api htmls
    public function riks_wac_new_connection_html(){
        $html ="";
        $html .= "
            <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
            <script src='".get_site_url()."/wp-content/plugins/riks_woo_connect/admin/js/jquery.validate.min.js'></script>
            <script>
            $(function() {
              $(\"form[name='newAPIConnection']\").validate({
                
                rules: {
                  code: \"required\",
                  name: \"required\",
                  url: \"required\",
                  key: \"required\",
                  secret: \"required\"
                },
                messages: {
                  code: \"Please enter nickname without space\",
                  name: \"Required\",
                  url: \"Required\",
                  key: \"Required\",
                  secret: \"Required\"
                },
                submitHandler: function(form) {
                  form.submit();
                }
              });
            });
            </script>
            <div class='riks_wac_form_wraper'>
            <div class='riks_wac_text_title'>Add new connection</div>
            <form method='post' name='newAPIConnection' onsubmit='return validateForm()' >
                <div class='riks_wac_form_label'><label>Nickname *</label></div><div class='riks_wac_form_input'><input type='text' name='code' onfocus=\"this.value=''\" value='Enter name (without space)'></div>
                <div class='riks_wac_form_label'><label>Name *</label></div><div class='riks_wac_form_input'><input type='text' name='name'></div>
                <div class='riks_wac_form_label'><label>URL *</label></div><div class='riks_wac_form_input'><input type='text' name='url'></div>
                <div class='riks_wac_form_label'><label>Consumer key *</label></div><div class='riks_wac_form_input'><input type='text' name='key'></div>
                <div class='riks_wac_form_label'><label>Consumer secret *</label></div><div class='riks_wac_form_input'><input type='password' name='secret'></div>
                <div class='riks_wac_form_button'><input type='submit' name='saveConnection' value='Add'></div>
                
            </form>
        </div>";
        return $html;
    }
    //List of added APIs
    public function riks_wac_connections($connections){
        $html ="";
        $html .="<div class='riks_wac_wrapper'>
            <div class='riks_wac_text_title'>Connections</div>
            <table  class='riks_wac_table'>
                <thead>
                <tr class='riks_wac_tr'>
                    <th class='riks_wac_th'>Code</th>
                    <th class='riks_wac_th'>Name</th>
                    <th class='riks_wac_th'>URL</th>
                    <th class='riks_wac_th'>Action</th>
                </tr>
            </thead>
            <tbody>";
        foreach($connections as $connection){        
            $html .="<tr class='riks_wac_tr'>
                <td class = 'riks_wac_td'>".$connection->scode."</td>
                <td class = 'riks_wac_td'>".$connection->name."</td>
                <td class = 'riks_wac_td'>".$connection->api_url."</td>
                <td class = 'riks_wac_td'><form method='post'><input type='hidden' name = 'con_id' value='".$connection->id."'><input type='submit' name='delConnection' value='Delete'></form></td>
            </tr>";
        }    
        $html .="</tbody>
            </table>
        </div>";
        return $html;
    }
    public function riks_wac_channel_news_html($request){
        $html="";
        if( is_wp_error( $request ) ) {
        	$html .= "Error";
        	return false; 
        }
        $bodies = json_decode($request['body']);
        foreach($bodies as $body){
            $content = $body->content;
            $html .= $content->rendered;
        }
        return $html;
    }
    public function riks_wac_product_search_form($websites){
        $html="";
        $html .= "<div class='riks_wac_form_wraper'>";
            $html .= "<form method='post'>";
            $html .= "<select name=\"website\" id=\"websites\" style =\"background-color: #d9d9d9;height: 50px; width:20%\">";
            $html .= "<option value=\"\">Copy to ...</option>";
                foreach($websites as $website){
                    $site_index = array_search( $website['name'], array_column( $websites, 'name' ) );
                    $site_id = array_keys($websites)[$site_index];
                $html .= "<option value=\"".$site_id."\">".$website['name']."</option>";
            }
            $html .= "</select>";
                $html .= "<input type=\"text\" name=\"search_term\" onfocus=\"this.value=''\"  size = \"80\" value=\"Enter product id or sku\" style =\"background-color: #d9d9d9;height: 50px; width:60%\">";
                $html .= "<input type=\"submit\" name=\"search\" class=\"button\" value=\"Go\" style =\"background-color: #d9d9d9;height: 50px;width:10%;\" />";
            $html .= "</form>";
        $html .= "</div>";
        return $html;
    }
    private function riks_wac_check_existed_product($sku){
        if ($sku == wc_get_product_id_by_sku( $sku )){
            return true;
        }
        else
            return false;
    }
}
?>