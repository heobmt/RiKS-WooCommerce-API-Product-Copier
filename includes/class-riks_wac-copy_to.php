<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class RiKS_WAC_copy_to {
    public function riks_wac_local_product_by_id_sku($term){
        $product = wc_get_product( $term );
        if (!empty($product)){
            return $product;
        }
        else{
            $pro_id = wc_get_product_id_by_sku($term);
            if (!empty($term)){
                $product = wc_get_product($pro_id);
            }
        }
        return $product;
    }
    function riks_wac_get_remote_categories($site_identity,$page){
        $url = $site_identity['url'];
        $key = $site_identity['auth'];
        $ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, ''.$url.'/wp-json/wc/v3/products/categories?per_page=100&page='.$page.'');
    	
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    	$headers = array();
    	$headers[] = 'Authorization: Basic '.$key.'';
    	$headers[] = 'Content-Type: application/json';
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	$result = curl_exec($ch);
    	//$total_page = $result.headers.get('x-wp-total');
    	curl_close($ch);
    	$categories = json_decode($result, true);
    	return $categories;
    }
    function riks_wac_put_remote_product($site_identity,$product_data){
        $url = $site_identity['url'];
        $key = $site_identity['auth'];
        
        $ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, ''.$url.'/wp-json/wc/v3/products');
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    	curl_setopt($ch, CURLOPT_POSTFIELDS, ''.$product_data.'');
    	$headers = array();
    	$headers[] = 'Authorization: Basic '.$key.'';
    	$headers[] = 'Content-Type: application/json';
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	return $result;
    }
}

?>