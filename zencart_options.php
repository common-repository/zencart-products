<?php 
 /* 
  Plugin Name: Zencart featured, special and new product display
  Plugin URI: http://www.techize.co.uk/plugins
  Description: Plugin for displaying the featured products, special products, as well as new products as taken from a ZenCart database. Categories can also be displayed as a list/menu.
  Author: Jonathan Gill
  Version: 2.1.1
  Author URI: http://www.techize.co.uk/
  */  
  
  require_once (sprintf("%s/lib/zencart_db_methods.php", dirname(__FILE__)));
  
  class Techize_zencart_featured_products extends WP_Widget{
  
    public function __construct()
    {  
	  $params = array(
         'description' => 'Zen cart featured products display',
         'name'        => 'Zen Cart Featured Products'
	  );
		 
	  parent::__construct('Techize_zencart_featured_products','',$params);	 
    }
	
	public function form($instance) {
	  // the form on the widget page
	  if( $instance) {
		$fpd_product_count = esc_attr($instance['fpd_product_count']);
	  } else {
		$fpd_product_count = '1';			 
	  }
	  
	  ?>
      <p>
		<label for="<?php echo $this->get_field_id('fpd_product_count'); ?>"><?php _e('Number of Featured Products to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('fpd_product_count'); ?>" name="<?php echo $this->get_field_name('fpd_product_count'); ?>" type="text" value="<?php echo $fpd_product_count; ?>" />
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id('fpd_title'); ?>"><?php _e('Title to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('fpd_title'); ?>" name="<?php echo $this->get_field_name('fpd_title'); ?>" type="text" value="<?php echo $instance['fpd_title']; ?>" />
	  </p>
	  <?php
	}
	
	public function widget($args, $instance){
	  echo $random_featured_product_html = techize_get_random_featured_products($instance['fpd_product_count'], $instance['fpd_title']); 
	  
	}
	
}

class Techize_zencart_best_sellers extends WP_Widget{
  
    public function __construct()
    {  
	  $params = array(
         'description' => 'Zen cart Best selling products display',
         'name'        => 'Zen Cart Best Selling Products'
	  );
		 
	  parent::__construct('Techize_zencart_best_sellers','',$params);	 
    }
	
	public function form($instance) {
	  // the form on the widget page
	  if( $instance) {
		$zbs_product_count = esc_attr($instance['zbs_product_count']);
	  } else {
		$zbs_product_count = '1';			 
	  }
	  
	  ?>
      <p>
		<label for="<?php echo $this->get_field_id('zbs_product_count'); ?>"><?php _e('Number of Featured Products to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('zbs_product_count'); ?>" name="<?php echo $this->get_field_name('zbs_product_count'); ?>" type="text" value="<?php echo $zbs_product_count; ?>" />
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id('zbs_title'); ?>"><?php _e('Title to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('zbs_title'); ?>" name="<?php echo $this->get_field_name('zbs_title'); ?>" type="text" value="<?php echo $instance['zbs_title']; ?>" />
	  </p>
	  <?php
	}
	
	public function widget($args, $instance){
	  echo $random_featured_product_html = techize_get_best_sellers($instance['zbs_product_count'], $instance['zbs_title']); 
	  
	}
	
}

class Techize_zencart_new_products extends WP_Widget{
  
    public function __construct()
    {  
	  $params = array(
         'description' => 'Zen cart New products display',
         'name'        => 'Zen Cart New Products'
	  );
		 
	  parent::__construct('Techize_zencart_new_products','',$params);	 
    }
	
	public function form($instance) {
	  // the form on the widget page
	  if( $instance) {
		$znp_product_count = esc_attr($instance['znp_product_count']);
	  } else {
		$znp_product_count = '1';			 
	  }
	  
	  ?>
      <p>
		<label for="<?php echo $this->get_field_id('znp_product_count'); ?>"><?php _e('Number of New Products to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('znp_product_count'); ?>" name="<?php echo $this->get_field_name('znp_product_count'); ?>" type="text" value="<?php echo $znp_product_count; ?>" />
	  </p>
	  <p>
		<label for="<?php echo $this->get_field_id('znp_title'); ?>"><?php _e('Title to display', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('znp_title'); ?>" name="<?php echo $this->get_field_name('znp_title'); ?>" type="text" value="<?php echo $instance['znp_title']; ?>" />
	  </p>
	  <?php
	}
	
	public function widget($args, $instance){
	  echo techize_get_new_products($instance['znp_product_count'], $instance['znp_title']); 
	}
	
}



class Techize_zencart_options {
	
	public function __construct() {
	  add_action('admin_init', array(&$this, 'admin_init')); 
      add_action('admin_menu', array(&$this, 'add_menu_page')); 
	}
	public function add_menu_page() {
	  add_options_page('Zen Cart Options','Zen Cart Options', 'manage_options', 'techize_zencart_products', array(&$this,'plugin_options_page'));
	}
	public function plugin_options_page() {
	  include(sprintf("%s/views/zencart_products_admin.php", dirname(__FILE__))); 
	}
	public function admin_init () {
	  // admin init section
	}
	
	public function wp_zencart_products_display() {
	  parent::WP_Widget(false, $name = __('ZenCart Featured Products', 'wp_widget_plugin') );
	}
		
	function update($new_instance, $old_instance){
      $instance = $old_instance;
	  // Fields
	  $instance['product_count'] = strip_tags($new_instance['product_count']);
	  return $instance;
	}
	
  }

  $techize_zencart_options = new Techize_zencart_options();
  add_action('widgets_init', 'techize_register_zencart_widgets');
  
  function techize_register_zencart_widgets() {
    register_widget("Techize_zencart_featured_products");
	register_widget("Techize_zencart_best_sellers");
	register_widget("Techize_zencart_new_products");
  }  
?>