<?php

function wpmlsync_print_pre($a){
	echo "<pre>";
	print_r($a);
	echo "</pre>";
}

function wp_mercadolibre_sync_meli_code_array(){
	$meli_code_array = array(
			0 => array(
				'desc' => __('No data set and / or saved yet.','wp-mercadolibre-sync'),
				'html' => '',
			),
			1 => array(
				'desc' => '',
				'html' => ''
			),
			2 => array(
				'desc' => '',
				'html' => ''
			),
			3 => array(
				'desc' => '',
				'html' => ''
			),
			4 => array(
				'desc' => __('Okay, the API has been authorized. The data has been saved.','wp-mercadolibre-sync'),
				'html' => ''
			),
			5 => array(
				'desc' => __('The Token and expiration time have been refreshed. The data has been saved.','wp-mercadolibre-sync'),
				'html' => ''
			),
			6 => array(
				'desc' => __('Excellent, your API is linked correctly.','wp-mercadolibre-sync'),
				'html' => ''
			),
			7 => array(
				'desc' => __('You must authenticate your application to continue.','wp-mercadolibre-sync'),
				'html' => ''
			), 
			8 => array(
				'desc' => __('Your app has been authorized with a new code. Tokens and expiration time have been refreshed.','wp-mercadolibre-sync'),
				'html' => ''
			),
		);
	return $meli_code_array;
}

 function wp_mercadolibre_sync_settings($name=''){
		$options = get_option( 'wp_mercadolibre_sync_settings' );  
		$out = array(
			
			'appId' => $options['wp_mercadolibre_sync_appId'],
			'secretKey' => $options['wp_mercadolibre_sync_secretKey'],
			'redirectURI' => $options['wp_mercadolibre_sync_redirectURI'],
			'siteId' => $options['wp_mercadolibre_sync_siteId'],

			'access_token' => $options['wp_mercadolibre_sync_access_token'],
			'expires_in' => $options['wp_mercadolibre_sync_expires_in'],
			'refresh_token' => $options['wp_mercadolibre_sync_refresh_token'],

			'seller_id' => $options['wp_mercadolibre_sync_seller_id'],

			'auto_token' => $options['wp_mercadolibre_sync_auto_token'],

		);
		if($name){
			return $out[$name];
		}else{
			return $out;
		} 
	}

function wp_mercadolibre_sync_update_settings($args=array(), $is_public=false){
	if(!empty($args)){
			$_temp_options = get_option( 'wp_mercadolibre_sync_settings' );
			$_temp_options['wp_mercadolibre_sync_access_token'] = $args['access_token'];
			$_temp_options['wp_mercadolibre_sync_expires_in'] = time() + $args['expires_in'];
			//$_temp_options['wp_mercadolibre_sync_expires_in'] = time() + 10;
			$_temp_options['wp_mercadolibre_sync_refresh_token'] = $args['refresh_token'];
			$_temp_options['wp_mercadolibre_sync_seller_id'] = $args['seller_id'];
			update_option( 'wp_mercadolibre_sync_settings', $_temp_options ); 

			wp_mercadolibre_sync_debug('update_option::wp_mercadolibre_sync_settings '. ($is_public ? 'public' : 'admin') .'');
	}
}

function wp_mercadolibre_sync_get_seller_id($meli, $access_token){
	$params = array(
  	'access_token'=>$access_token
  ); 
	$url = '/users/me';  
	$meli_result = $meli->get($url, $params); 
	$meli_user_id = $meli_result['body']->id;
	return $meli_user_id;
}

function wp_mercadolibre_sync_get_api_status(){
	$status = get_option('wp_mercadolibre_sync_status'); 
	// $status = apply_filters('wpmlsync/api/status', $status);
	return $status;
}

function wp_mercadolibre_sync_get_api_debug($debug){ 
	$debug = apply_filters('wpmlsync/api/debug', $debug);
	return $debug;
}
function wp_mercadolibre_sync_debug($debug){
	$enable = get_option('wp_mercadolibre_sync_settings', 0); 
	if(!empty($enable['wp_mercadolibre_sync_debug'])){
		// $debug = wp_mercadolibre_sync_get_api_status($debug);
		$status = wp_mercadolibre_sync_get_api_status();
		file_put_contents(WP_CONTENT_DIR . '/wp-mercadolibre-sync-debug.txt', "[status: ".$status."] ".date('Y-m-d H:i:s', time())." msg: ".$debug."\n", FILE_APPEND);
	} 
}
 

// TESTS

function wp_mercadolibre_sync_get_item_test_fields(){
	/*
	TODO, make multidimensional like:

		$fields = array(

			array(

				'name' => '',
				'default_value' => '',
				'type' => '' // string/array

			),
	
		);

	*/
	$fields = array(
		'title',
		'price'
	);
	return $fields; 
}
function wp_mercadolibre_sync_get_item_test($get_params){
	$item = array(
		"title" => "Item De Prueba - Por Favor, No Ofertar --kc:off",
    "category_id" => "MLU177823",
    "price" => 10,
    "currency_id" => "UYU",
    "available_quantity" => 1,
    "buying_mode" => "buy_it_now",
    "listing_type_id" => "bronze",
    "condition" => "new",
    "description" => array ("plain_text" => "Item de Teste WP Mercado Libre's PHP SDK."),
    "video_id" => "RXWn6kftTHY",
    "warranty" => "12 month",
    "pictures" => array(
        array(
            "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/IPhone_7_Plus_Jet_Black.svg/440px-IPhone_7_Plus_Jet_Black.svg.png"
        ),
        array(
            "source" => "https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/IPhone7.jpg/440px-IPhone7.jpg"
        )
    ), 
  );
  $prefix = 'wp_ml_sync_test__';
  $fields = wp_mercadolibre_sync_get_item_test_fields();
  foreach ($fields as $field) {
  	$item[$field] = isset($get_params[$prefix.$field]) ? $get_params[$prefix.$field] : $item[$field];
  }
  //$item['title'] = isset($get_params[$prefix.'title']) ? $get_params[$prefix.'title'] : $item['title'];
	return $item; 
}

function wp_mercadolibre_sync_get_template($template='',$part=''){ 
	//$template = ''; 
	if( file_exists( get_template_directory() . '/wpsyncml-template/'.$template.'-'.$part.'.php' )){
		
		$template = get_template_directory() . '/wpsyncml-template/'.$template.'-'.$part.'.php';
	
	}else if(file_exists( get_template_directory() . '/wpsyncml-template/'.$template.'.php' )){
	
		$template = get_template_directory() . '/wpsyncml-template/'.$template.'.php'; 
	
	}else{

		if( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'template-parts/'.$template.'-'.$part.'.php' )){

			$template = plugin_dir_path( dirname( __FILE__ ) ) . 'template-parts/'.$template.'-'.$part.'.php'; 

		}else if( file_exists( plugin_dir_path( dirname( __FILE__ ) ) . 'template-parts/'.$template.'.php' )){

			$template = plugin_dir_path( dirname( __FILE__ ) ) . 'template-parts/'.$template.'.php'; 

		} 

	}
	 
	if($template != ''){
		return $template;
	} 
}


function wp_mercadolibre_sync_get_item_description($item, $meli){
	/*
	ej: https://api.mercadolibre.com/items/$ITEM_ID/description
	*/ 
	$description = $meli->get( '/items/'.$item->id.'/description' );
	if(empty($description)) return false;

	return $description['body']->plain_text;
}
 
function wp_mercadolibre_sync_get_item_pictures($item){ 
	$return = array();
	$pictures = $item->pictures;  
	$i = 0;
	foreach($pictures as $picture){ 
		$return['images'][] = array(
			'src' => $picture->url,
			'position' => $i
		);
		$i++;
	}
	return $return;
}
function wp_mercadolibre_sync_get_and_upload_item_pictures($item){
	$return = array();
	$pictures = $item->pictures; 
	$images = array(); // images url array of product
	foreach ($pictures as $picture){ 
		$images[] = $picture->url;
	}
	if(!empty($images)){
		function uploadMedia($image_url){
			require_once( ABSPATH . 'wp-admin/includes/image.php');
			require_once( ABSPATH . 'wp-admin/includes/file.php');
			require_once( ABSPATH . 'wp-admin/includes/media.php');
			$media = media_sideload_image($image_url,0);
			$attachments = get_posts(array(
				'post_type' => 'attachment',
				'post_status' => null,
				'post_parent' => 0,
				'orderby' => 'post_date',
				'order' => 'DESC'
			));
			return $attachments[0]->ID;
		} 
		$productImagesIDs = array();  
		foreach($images as $image){
			$mediaID = uploadMedia($image);  
			if($mediaID) $productImagesIDs[] = $mediaID; 
		}
		if($productImagesIDs){
			$return['image_id'] = $productImagesIDs[0];
			//$objProduct->set_image_id($productImagesIDs[0]); 
			if(count($productImagesIDs) > 1){
				$return['gallery_image_ids'] = $productImagesIDs;
				//$objProduct->set_gallery_image_ids($productImagesIDs);
			}
			$i = 0;
			foreach($productImagesIDs as $img){
				$src = wp_get_attachment_image_src($img, 'large');
				$src = $src[0];
				$return['images'][] = array(
					'src' => $src,
					'position' => $i
				);
				$i++;
			}

			return $return;
		}
	}
}