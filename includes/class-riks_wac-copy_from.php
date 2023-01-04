<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class RiKS_WAC_copy_from {
    function riks_wac_get_products($site_identity,$page){
        $url = $site_identity['url'];
        $key = $site_identity['auth'];
        $ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, ''.$url.'/wp-json/wc/v3/products?page='.$page.'&per_page=20&status=publish');
    	
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
}
?>