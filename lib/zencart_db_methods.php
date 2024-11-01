<?php
  define('IS_ADMIN_FLAG', FALSE); # define the flag so zencart functions will work properly as they check for the existence of this flag.

  require(sprintf("%s/class.base.php", dirname(__FILE__))); # pull in the zencart base class.
  require(sprintf("%s/query_factory.php", dirname(__FILE__)));
  require(sprintf("%s/functions_general.php", dirname(__FILE__))); # general zencart functions that are used.
  
  $techize_zen_db = new queryFactory();
  
  define ('DB_SERVER_USERNAME', get_option('zenfpd_dbuser'));
  define ('DB_SERVER_PASSWORD', get_option('zenfpd_dbpwd'));
  define ('DB_DATABASE', get_option('zenfpd_dbname'));
  define ('DB_SERVER', get_option('zenfpd_dbhost'));
  $zen_prefix = get_option('zenfpd_prefix');
  define ('TABLE_FEATURED', $zen_prefix . 'featured');
  define ('TABLE_PRODUCTS_DESCRIPTION', $zen_prefix . 'products_description');
  define ('TABLE_PRODUCTS', $zen_prefix . 'products');
  define ('IMAGE_WIDTH', get_option('zenfpd_image_width'));
  define ('IMAGE_HEIGHT',get_option('zenfpd_image_height'));	
  define ('STORE_URL', get_option('zenfpd_store_url'));  
  define ('IMAGE_FOLDER', get_option('zenfpd_prod_img_folder'));  
  define ('SHOW_NEW_PRODUCTS_LIMIT', get_option('zenfpd_new_product_days')); # wont work at the mo, as need to set the form up TODO
  // define ('SHOW_NEW_PRODUCTS_LIMIT',120); # TODO remove the fixed number once options page is back and working.
  
  if (!$techize_zen_db->connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, false, false)) {
    ?>
	  <div id="zencart-config-problem" class="error"><p>Zen Cart Database Configuration needs to be done or is not correct.<a href="options-general.php?page=techize_zencart_products">Click Here</a> to set up or check the database settings.</p></div>
	<?php
  }
  
function techize_get_best_sellers($product_count, $title) {
  global $techize_zen_db;
  $retval = "";
  $featured_box_counter=0;
  $best_sellers = "select distinct p.products_id, pd.products_name, p.products_image, p.products_ordered
                             from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
                             where p.products_status = '1'
                             and p.products_ordered > 0
                             and p.products_id = pd.products_id
                             order by p.products_ordered desc, pd.products_name LIMIT " . $product_count;
  $best_sellers = $techize_zen_db->Execute($best_sellers);
  
  $retval = '<div class="zenfpd_title"><h4 class="widget-header">'. $title . '</h4></div>'; 
  
  $rows = 0;
  while (!$best_sellers->EOF) {
    $retval .= '<div class="zenbest_sellers">';
	$retval .= '<a href="'. STORE_URL . '/index.php?main_page=product_info&products_id=' . $best_sellers->fields['products_id'] . '"><img src="' . IMAGE_FOLDER . $best_sellers->fields["products_image"] . '" width = "'.IMAGE_WIDTH . '" height = "' . IMAGE_HEIGHT . '"/></a><br />';
	$retval .= '<a href="'. STORE_URL. '/index.php?main_page=product_info&products_id=' . $best_sellers->fields["products_id"] . '">' . $best_sellers->fields["products_name"] . '</a>';  
    $retval .= '</div>';  
    $best_sellers->MoveNext();
  }
  return $retval;  
}  
  
function techize_get_new_products($product_count, $title) {
  global $techize_zen_db;
  
  $display_limit = zen_get_new_date_range();
  $retval = "";
  $featured_box_counter=0;
  $new_products_query = "select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, pd.products_name,
                                              p.master_categories_id
                           from (" . TABLE_PRODUCTS . " p
                           left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                           where p.products_id = pd.products_id
                           and p.products_status = 1 " . $display_limit;
  $new_products = $techize_zen_db->ExecuteRandomMulti($new_products_query, $product_count);
  
  $retval = '<div class="zenfpd_title"><h4 class="widget-header">'. $title . '</h4></div>'; 
  
  $rows = 0;
  while (!$new_products->EOF) {
    $retval .= '<div class="zenbest_sellers">';
	$retval .= '<a href="'. STORE_URL . '/index.php?main_page=product_info&products_id=' . $new_products->fields['products_id'] . '"><img src="' . IMAGE_FOLDER . $new_products->fields["products_image"] . '" width = "'.IMAGE_WIDTH . '" height = "' . IMAGE_HEIGHT . '"/></a><br />';
	$retval .= '<a href="'. STORE_URL. '/index.php?main_page=product_info&products_id=' . $new_products->fields["products_id"] . '">' . $new_products->fields["products_name"] . '</a>';  
    $retval .= '</div>';  
    $new_products->MoveNextRandom();
  }
  return $retval;  
}    
  
function techize_get_random_featured_products($product_count, $title) {
  global $techize_zen_db;
  $retval = "";
  $featured_box_counter=0;
  $random_featured_products_query = "select p.products_id, p.products_image, pd.products_name,
                                       p.master_categories_id
                           from (" . TABLE_PRODUCTS . " p
                           left join " . TABLE_FEATURED . " f on p.products_id = f.products_id
                           left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                           where p.products_id = f.products_id
                           and p.products_id = pd.products_id
                           and p.products_status = 1
                           and f.status = 1";
  $featured_product = $techize_zen_db->ExecuteRandomMulti($random_featured_products_query, $product_count);
  
  $retval = '<div class="zenfpd_title"><h4 class="widget-header">'. $title . '</h4></div>'; 
  while (!$featured_product->EOF) {
    $featured_box_counter++;
    $retval .= '<div class="zenfpd_product">'; 
    $retval .= '<a href="'. STORE_URL . '/index.php?main_page=product_info&products_id=' . $featured_product->fields["products_id"] . '"><img src="' . IMAGE_FOLDER . $featured_product->fields["products_image"] . '" width = "'.IMAGE_WIDTH . '" height = "' . IMAGE_HEIGHT . '"/></a><br />';  
    $retval .= '<a href="'. STORE_URL. '/index.php?main_page=product_info&products_id=' . $featured_product->fields["products_id"] . '">' . $featured_product->fields["products_name"] . '</a>';  
    $retval .= '</div>';  
    $featured_product->MoveNextRandom();
  }
  
  return $retval;  
}
?>