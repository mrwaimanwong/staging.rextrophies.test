<?php

/**
* The admin-specific functionality of the plugin.
*
* @link       https://www.orionorigin.com/
* @since      1.0.0
*
* @package    Vpc_Cta
* @subpackage Vpc_Cta/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Vpc_Cta
* @subpackage Vpc_Cta/admin
* @author     Orion <help@orionorigin.com>
*/
class Vpc_Cta_Admin {

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
	* @param      string    $plugin_name       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Vpc_Cta_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Vpc_Cta_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vpc-cta-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style("vpc-cta-colorpicker-css", plugin_dir_url( __FILE__ ) . 'js/colorpicker/css/colorpicker.min.css', array(), $this->version, 'all');

	}

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Vpc_Cta_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Vpc_Cta_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vpc-cta-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script("vpc-cta-colorpicker-js", plugin_dir_url( __FILE__ ) . 'js/colorpicker/js/colorpicker.min.js', array('jquery'), $this->version, false);

	}

	function add_vpc_cta_behaviour($behaviours){
		$behaviours['text']=__('Simple Textfield','vpc-cta');
		if (class_exists( 'Vpc_Mva')) {
			$behaviours['multi_views_text']=__('Multi-views Textfield','vpc-cta');
		}
		return $behaviours;
	}

	function add_vpc_cta_submenu(){
		$parent_slug = "edit.php?post_type=vpc-config";
		add_submenu_page($parent_slug, __('Add Fonts', 'vpc-cta'), __('Add Fonts', 'vpc-cta'), 'manage_product_terms', 'vpc_cta_add_fonts', array($this, 'vpc_cta_add_fonts'));
		add_submenu_page($parent_slug, __('Add Colors', 'vpc-cta'), __('Add Colors', 'vpc-cta'), 'manage_product_terms', 'vpc_cta_add_colors', array($this, 'vpc_cta_add_colors'));

	}

	function vpc_cta_add_fonts(){
		include_once( VPC_CTA_DIR . '/includes/vpc-add-fonts.php' );
		woocommerce_vpc_cta_add_fonts();
	}

	function vpc_cta_add_colors(){
		include_once( VPC_CTA_DIR . '/includes/vpc-add-colors.php' );
		woocommerce_vpc_cta_add_colors();
	}

	function add_vpc_cta_skins_settings($skin_settings)
	{
		$skin_settings[] = array(
			'type' => 'sectionbegin',
			'id' => 'vpc-rqa-container',
		);

		$skin_settings[] = array(
			'title' => __('Show custom text font in configurator page', 'vpc-cta'),
			'name' => 'vpc-config[show-fonts]',
			'type' => 'radio',
			'options' => array("Yes" => "Yes", "No" => "No"),
			'default' => 'Yes',
			'class' => '',
			'desc' => __('Should the plugin show the custom text font on configurator page?', 'vpc-cta'),
		);
		$skin_settings[] = array(
			'title' => __('Show custom text color in configurator page', 'vpc-cta'),
			'name' => 'vpc-config[show-colors]',
			'type' => 'radio',
			'options' => array("Yes" => "Yes", "No" => "No"),
			'default' => 'Yes',
			'class' => '',
			'desc' => __('Should the plugin show the custom text color on configurator page?', 'vpc-cta'),
		);

		$skin_settings[] =array('type' => 'sectionend');
		return $skin_settings;
	}

	function vpc_cta_simple_text_options($options_fields)
	{
		$option_top=array(
			'title' => __('Text top position (%)', 'vpc-cta'),
			'name' => 'text-top',
			'type' => 'number',
			'class'=>'custom_text_top',
			'custom_attributes' => array('step' => 'any')
		);

		$option_left=array(
			'title' => __('Text left position (%)', 'vpc-cta'),
			'name' => 'text-left',
			'type' => 'number',
			'class'=>'custom_text_left',
			'custom_attributes' => array('step' => 'any')
		);

		$option_text_transform=array(
			'title' => __('Text rotation(&deg;)', 'vpc-cta'),
			'name' => 'angle',
			'type' => 'number',
			'class'=>'custom_text_rotation',
			'custom_attributes' => array('step' => 'any')
		);

		$option_font_size=array(
			'title' => __('Font size', 'vpc-cta'),
			'name' => 'size',
			'type' => 'number',
			'class'=>'custom_text_font_size',
			'custom_attributes' => array('step' => 'any')
		);
		$max_characters=array(
			'title' => __('Text size', 'vpc-cta'),
			'name' => 'max_char',
			'type' => 'number',
			'class'=>'custom_text_size',
			'custom_attributes' => array('step' => 'any')
		);
		array_push($options_fields['fields'],$option_top);
		array_push($options_fields['fields'],$option_left);
		array_push($options_fields['fields'], $option_text_transform);
		array_push($options_fields['fields'], $option_font_size);
		array_push($options_fields['fields'],$max_characters);
		return $options_fields;
	}

	function vpc_cta_multi_views_text_options($options_fields)
	{
		$views = get_option('vpc-mva-views');
		if (is_array($views)) {
			$view_field_id=array(
				'title' => __('ID', 'vpc-cta'),
				'name' => 'text_view_field_id',
				'type' => 'text',
			);

			$view_field=array(
				'title'=>__('View', 'vpc-cta'),
				'type' => 'select',
				'name' => 'text_view_field',
				'options' => $views,
			);

			$multi_views_option_top=array(
				'title' => __('Text top position', 'vpc-cta'),
				'name' => 'top',
				'type' => 'number',
				'class'=>'text_top_position',
				'custom_attributes' => array('step' => 'any')
			);

			$multi_views_option_left=array(
				'title' => __('Text left position', 'vpc-cta'),
				'name' => 'left',
				'type' => 'number',
				'class'=>'text_left_position',
				'custom_attributes' => array('step' => 'any')
			);

			$multi_views_option_text_transform=array(
				'title' => __('Rotation', 'vpc-cta'),
				'name' => 'rotation',
				'type' => 'number',
				'class'=>'text_rotation',
				'custom_attributes' => array('step' => 'any')
			);

			$multi_views_option_font_size=array(
				'title' => __('Font Size', 'vpc-cta'),
				'name' => 'font_size',
				'type' => 'number',
				'class'=>'text_font_size',
				'custom_attributes' => array('step' => 'any')
			);

			$multi_views_option_max_characters=array(
				'title' => __('Text size', 'vpc-cta'),
				'name' => 'text_max_size',
				'type' => 'number',
				'class'=>'text_size',
				'custom_attributes' => array('step' => 'any')
			);

			$multi_views_option_price=array(
				'title' => __('Price', 'vpc-cta'),
				'name' => 'price',
				'type' => 'number',
				'class'=>'text_price',
				'custom_attributes' => array('step' => 'any')
			);

			$options_fields['fields'][]=array(
				'title' => __('Text top positions (%)', 'vpc-cta'),
				'name' => 'multi_views_text_top',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_top',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_top),
				'desc' => __('text_top_positions', 'vpc-cta'),
				'row_class'=>'vpc-text_top_positions_option-row',
				'add_btn_label'=> __("Add position", "vpc-cta")
			);

			$options_fields['fields'][]=array(
				'title' => __('Text left positions (%)', 'vpc-cta'),
				'name' => 'multi_views_text_left',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_left',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_left),
				'desc' => __('text_left_positions', 'vpc-cta'),
				'row_class'=>'vpc-text_left_positions_option-row',
				'add_btn_label'=> __("Add position", "vpc-cta")
			);
			$options_fields['fields'][]=array(
				'title' => __('Text Rotations (&deg;)', 'vpc-cta'),
				'name' => 'multi_views_text_rotation',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_rotation',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_text_transform),
				'desc' => __('text_rotations', 'vpc-cta'),
				'row_class'=>'vpc-text_rotations_option-row',
				'add_btn_label'=> __("Add rotation", "vpc-cta")
			);
			$options_fields['fields'][]=array(
				'title' => __('Font sizes', 'vpc-cta'),
				'name' => 'multi_views_text_font_size',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_font_size',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_font_size),
				'desc' => __('font_sizes', 'vpc-cta'),
				'row_class'=>'vpc-font_sizes_option-row',
				'add_btn_label'=> __("Add font size", "vpc-cta")
			);

			$options_fields['fields'][]=array(
				'title' => __('Text sizes', 'vpc-cta'),
				'name' => 'multi_views_maxi_char',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_size',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_max_characters),
				'desc' => __('text_sizes', 'vpc-cta'),
				'row_class'=>'vpc-text_sizes_option-row',
				'add_btn_label'=> __("Add text size", "vpc-cta")
			);

			$options_fields['fields'][]=array(
				'title' => __('Text prices', 'vpc-cta'),
				'name' => 'multi_views_text_prices',
				'type' => 'repeatable-fields',
				'class' => 'striped custom_multi_views_text_prices',
				'fields' => array( $view_field_id,$view_field,$multi_views_option_price),
				'desc' => __('vpc_text_prices', 'vpc-cta'),
				'row_class'=>'vpc-text_prices_option-row',
				'add_btn_label'=> __("Add text price", "vpc-cta")
			);
		}
		return $options_fields;

	}

	function add_vpc_cta_text_options($options_fields){
		$options_fields = $this->vpc_cta_simple_text_options($options_fields);
		if ( class_exists( 'Vpc_Mva') ) { //if Visual Product Configurator Multiple Views Addon is activated
			$options_fields = $this->vpc_cta_multi_views_text_options($options_fields);
		}
		return $options_fields;
	}

	function vpc_cta_add_custom_mime_types($mimes) {
		$mimes['ttf'] = 'application/x-font-ttf';
		$mimes['svg'] = 'image/svg+xml';
		$mimes['icc'] = 'application/vnd.iccprofile';
		return $mimes;
	}


}
