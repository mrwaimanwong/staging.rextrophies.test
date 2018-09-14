<?php
/**
* The public-facing functionality of the plugin.
*
* @link       https://www.orionorigin.com/
* @since      1.0.0
*
* @package    Vpc_Cta
* @subpackage Vpc_Cta/public
*/

/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Vpc_Cta
* @subpackage Vpc_Cta/public
* @author     Orion <help@orionorigin.com>
*/
class Vpc_Cta_Public {

  /**
  * The ID of this plugin.
  *
  * @since    1.0.0
  * @access   private
  * @var      string    $plugin_name    The ID of this plugin.
  */
  private $plugin_name;

  /**
  * The version of this plugin.
  *
  * @since    1.0.0
  * @access   private
  * @var      string    $version    The current version of this plugin.
  */
  private $version;

  /**
  * Initialize the class and set its properties.
  *
  * @since    1.0.0
  * @param      string    $plugin_name       The name of the plugin.
  * @param      string    $version    The version of this plugin.
  */
  public function __construct($plugin_name, $version) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->views = get_option('vpc-mva-views');
    $this->active_views = array();
  }

  /**
  * Register the stylesheets for the public-facing side of the site.
  *
  * @since    1.0.0
  */
  public function enqueue_styles() {

    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/vpc-cta-public.css', array(), $this->version, 'all');
    wp_enqueue_style('qtip_min_css', plugin_dir_url(__FILE__) . 'css/jquery.qtip.min.css', array(), $this->version, 'all');

    $this->register_fonts();
  }

  /**
  * Register the JavaScript for the public-facing side of the site.
  *
  * @since    1.0.0
  */
  public function enqueue_scripts() {

    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/vpc-cta-public.js', array('jquery'), $this->version, false);
    wp_enqueue_script("vpc-cta-qtip", plugin_dir_url(__FILE__) . 'js/jquery.qtip.min.js', array('jquery'), VPC_VERSION, false);
    wp_enqueue_script("vpc-cta-textfill", plugin_dir_url(__FILE__) . 'js/jquery.textfill.js', array('jquery'), VPC_VERSION, false);

  }

  function vpc_cta_set_tooltip($option,$tooltip,$price,$key_view,$value_view)
  {
    global $vpc_settings,$WOOCS;
    $price_tooltip = get_proper_value($vpc_settings, "view-price");
    $name_tooltip = get_proper_value($vpc_settings, "view-name");
    $formated_price = wc_price($price);

    if (isset($value_view) && !empty($value_view) && isset($key_view) && !empty($key_view)) {
      if ($name_tooltip == "Yes"){
        $tooltip = $option["name"].' '.strtoupper($value_view);
      }
      if(isset($option['multi_views_text_prices']) && is_array($option['multi_views_text_prices']) && !empty($option['multi_views_text_prices'])){
        $formated_price = wc_price(0);
        foreach ($option['multi_views_text_prices'] as $price_key => $price_value) {
          if ((int)$price_value['text_view_field'] === (int)$key_view) {
            $price = (int)$price_value['price'];
            if ($WOOCS) {
              $currencies = $WOOCS->get_currencies();
              $price = $price * $currencies[$WOOCS->current_currency]['rate'];
            }
            $formated_price = wc_price($price);
          }
        }
      }
      if ($price_tooltip == "Yes") {
        if (strpos($formated_price, '-') || strpos($formated_price, '+'))
        $tooltip .= " $formated_price";
        else
        $tooltip.= " +$formated_price";
      }
      if (!empty($option["desc"]))
      $tooltip .= " (" . $option["desc"] . ")";
    }else {
      if ($name_tooltip == "Yes")
      $tooltip = $option["name"];

      if ($price_tooltip == "Yes") {
        if (strpos($formated_price, '-') || strpos($formated_price, '+'))
        $tooltip .= " $formated_price";
        else
        $tooltip .= " +$formated_price";
      }
      if (!empty($option["desc"]))
      $tooltip .= " (" . $option["desc"] . ")";
    }
    return $tooltip;
  }

  function vpc_cta_behaviour_text($option, $o_image, $price, $option_id, $component, $skin_name, $config_to_load, $config) {
    $tooltip = '';
    $multi_views = false;
    if (isset($config) && !empty($config)) {
      if (isset($config['multi-views']) && $config['multi-views'] === "Yes") {
        $multi_views = true;
      }
    }
    if ($multi_views && isset($component['behaviour']) && $component['behaviour'] === 'multi_views_text') {
      $views = get_option('vpc-mva-views');
      $to_show_views = get_text_views_to_show($option);
      if (isset($to_show_views) && !empty($to_show_views)) {
        foreach ($to_show_views as $value) {
          if (isset($views) && !empty($views) && isset($views[$value])) {
            $tooltip = $this->vpc_cta_set_tooltip($option,$tooltip,$price,$value,$views[$value]);
          }
        }
      }
    }else {
      if (!$multi_views && isset($component['behaviour']) && $component['behaviour'] === 'text') {
        $tooltip = $this->vpc_cta_set_tooltip($option,$tooltip,$price,"","");
      }
    }
    $tooltip =  apply_filters("vpc_options_text_title",$tooltip,$price,$option,$component,$config);
    vpc_cta_create_text_option($option, $tooltip, $price,$config_to_load,$multi_views,$component['behaviour'],$component,$config);
  }

  function add_vpc_cta_data($datas) {
    $datas['text_settings'] = array();
    return $datas;
  }

  function get_vpc_cta_preview($preview_html, $prod_id, $config_id) {
    $config = $this->get_vpc_cta_config_data($prod_id);
    if (isset($config['multi-views']) && $config['multi-views'] == "No" || !isset($config['multi-views'])) {
      if (class_exists('Vpc_Upload_Public')) {
        $preview_html = '<div class="vpc-global-preview">'
        . '<div id="upload_panel" class=""></div>'
        . '<div id="text_panel" class=""></div>'
        . '<div id="vpc-preview"></div>'
        . '</div>';
      } else {
        $preview_html = '<div class="vpc-global-preview">'
        . '<div id="text_panel" class=""></div>'
        . '<div id="vpc-preview"></div>'
        . '</div>';
      }
    }
    return $preview_html;
  }


  private function get_vpc_cta_config_data($prod_id) {
    $ids = get_product_root_and_variations_ids($prod_id);
    $config_meta = get_post_meta($ids['product-id'], "vpc-config", true);
    $configs = get_proper_value($config_meta, $prod_id, array());
    $config_id = get_proper_value($configs, "config-id", false);
    $config = get_post_meta($config_id, 'vpc-config', true);
    return $config;
  }

  private function register_fonts() {
    $fonts = get_option("vpc-cta-fonts");
    if (empty($fonts)) {
      $fonts = $this->get_vpc_cta_default_fonts();
    }
    if (isset($fonts) && !empty($fonts)) {
      foreach ($fonts as $font) {
        $font_label = $font[0];
        $font_url = str_replace('http://', '//', $font[1]);
        if ($font_url) {
          $handler = sanitize_title($font_label) . "-css";
          wp_register_style($handler, $font_url, array(), false, 'all');
          wp_enqueue_style($handler);
        } else if (!empty($font[2]) && is_array($font[2])) {
            get_vpc_ttf_font_style($font);
        }
      }
    }
  }

  function vpc_cta_filter_recap($recap, $config, $show_icons) {
    $show_fonts = get_proper_value($config, "show-fonts","Yes");
    $show_colors = get_proper_value($config, "show-colors","Yes");
    $multi_views = false;
    if (isset($config) && !empty($config)) {
      if (isset($config['multi-views']) && $config['multi-views'] === "Yes") {
        $multi_views = true;
      }
    }
    $components = $config['components'];
    if (isset($components) && !empty($components)) {
      foreach ($components as $component_key => $component) {
        if (!$multi_views && isset($component['behaviour']) && $component['behaviour'] == 'text') {
          foreach($component['options'] as $option){
            $name = $option['name'];
            if (isset($recap[$name]) && $recap[$name]!='') {
              $name .=' properties';
              if (isset($recap[$name]) && !empty($recap[$name])) {
                $recap[$name] = explode('#',$recap[$name]);
                $color = "<div style='background-color: #".$recap[$name][1].";height: 22px;width: 25px;display: inline-block;margin-left: 5px;'></div>";
                $save_data = $recap[$name][0].$color;
                $explode_data = explode('<br>',$save_data);
                if (isset($show_fonts) && isset($show_colors) && $show_fonts === 'Yes' && $show_colors === 'Yes') {
                  $recap[$name] = $save_data;
                }elseif (isset($show_fonts) && isset($show_colors) && $show_fonts === 'Yes' && $show_colors !== 'Yes') {
                  $recap[$name] = $explode_data[0];
                }elseif (isset($show_fonts) && isset($show_colors) && $show_colors === 'Yes' && $show_fonts !== 'Yes' ) {
                  $recap[$name] = $explode_data[1];
                }else {
                  unset($recap[$name]);
                }
              }
            }else {
              unset($recap[$name]);
              $key_properties = $name . ' properties';
              if (isset($recap[$key_properties]))
              unset($recap[$key_properties]);
            }
          }
        }else {
          if ($multi_views && isset($component['behaviour']) && $component['behaviour'] == 'multi_views_text') {
            foreach($component['options'] as $option){
              $views = get_option('vpc-mva-views');
              $to_show_views = get_text_views_to_show($option);
              if (isset($to_show_views) && !empty($to_show_views)) {
                foreach ($to_show_views as $value) {
                  if (isset($views) && !empty($views) && isset($views[$value])) {
                    $field_name = strtoupper($views[$value]);
                    $name = $option['name'].' '.$field_name;
                    if (isset($recap[$name]) && $recap[$name]!='') {
                      $name .=' properties';
                      if (isset($recap[$name]) && !empty($recap[$name])) {
                        $recap[$name] = explode('#',$recap[$name]);
                        $color = "<div style='background-color: #".$recap[$name][1].";height: 15px;width: 50%;display: inline-block;margin-left: 5px;'></div>";
                        $save_data = $recap[$name][0].$color;
                        $explode_data = explode('<br>',$save_data);
                        if (isset($show_fonts) && isset($show_colors) && $show_fonts !== 'No' && $show_colors !== 'No') {
                          $recap[$name] = $save_data;
                        }elseif (isset($show_fonts) && isset($show_colors) && $show_fonts !== 'No' && $show_colors === 'No') {
                          $recap[$name] = $explode_data[0];
                        }elseif (isset($show_fonts) && isset($show_colors) && $show_colors !== 'No' && $show_fonts === 'No' ) {
                          $recap[$name] = $explode_data[1];
                        }else {
                          unset($recap[$name]);
                        }
                      }
                    }else {
                      unset($recap[$name]);
                      $key_properties = $name . ' properties';
                      if (isset($recap[$key_properties]))
                      unset($recap[$key_properties]);
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    return $recap;
  }

  public function get_vpc_cta_default_fonts() {
    $default = array(
      array("Shadows Into Light", "http://fonts.googleapis.com/css?family=Shadows+Into+Light"),
      array("Droid Sans", "http://fonts.googleapis.com/css?family=Droid+Sans:400,700"),
      array("Abril Fatface", "http://fonts.googleapis.com/css?family=Abril+Fatface"),
      array("Arvo", "http://fonts.googleapis.com/css?family=Arvo:400,700,400italic,700italic"),
      array("Lato", "http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic"),
      array("Just Another Hand", "http://fonts.googleapis.com/css?family=Just+Another+Hand")
    );

    return $default;
  }

  function vpc_cta_get_total($total_price,$o_name,$multi_views,$component)
  {
    if (isset($component) && isset($component['options']) && !empty($component['options'])) {
      foreach($component['options'] as $option){
        if($multi_views && isset($component['behaviour']) && $component['behaviour'] == 'multi_views_text'){
          $views = get_option('vpc-mva-views');
          $to_show_views = get_text_views_to_show($option);
          if (isset($to_show_views) && !empty($to_show_views)) {
            foreach ($to_show_views as $value) {
              if (isset($views) && !empty($views) && isset($views[$value])) {
                $opt_name=get_proper_value($option, "name","");
                $field_name = strtoupper($views[$value]);
                $name = $opt_name.' '.$field_name;

                if (isset($o_name) && is_array($o_name) && !empty($o_name)) {
                  if(isset($o_name[$name]) && $o_name[$name] !=''){
                    if (isset($option['multi_views_text_prices']) && !empty($option['multi_views_text_prices'])) {
                      foreach ($option['multi_views_text_prices'] as $price_key => $price_value) {
                        if ((int)$price_value['text_view_field'] === (int)$value) {
                          if(!empty($price_value['price']))
                          $total_price += (int)$price_value['price'];
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }else {
          if (!$multi_views && isset($component['behaviour']) && $component['behaviour'] == 'text') {
            if (isset($o_name) && is_array($o_name) && !empty($o_name)) {
              if(isset($o_name[$option['name']]) && $o_name[$option['name']] !=''){
                if(!empty($option['price']))
                $total_price += $option['price'];
              }
            }
          }
        }
      }
    }
    return $total_price;
  }

  function  vpc_cta_config_price($total_price, $product_id, $config, $cart_item){
    $o_name=array();
    if(isset($cart_item["visual-product-configuration"]) && is_array($cart_item["visual-product-configuration"])){
      foreach($cart_item["visual-product-configuration"] as $name => $val){
        $o_name[$name] = $val;
      }
    }
    $multi_views = false;
    $original_config = get_product_config($product_id);
    $config_settings=$original_config->settings;
    if (isset($config_settings) && !empty($config_settings)) {
      if (isset($config_settings['multi-views']) && $config_settings['multi-views'] === "Yes") {
        $multi_views = true;
      }
      if (isset($config_settings['components']) && !empty($config_settings['components'])) {
        $components = $config_settings['components'];
      }
    }
    if (isset($components) && !empty($components)) {
      foreach($components as $component)
      {
        $total_price = $this->vpc_cta_get_total($total_price,$o_name,$multi_views,$component);
      }
    }
    return $total_price;
  }
}
