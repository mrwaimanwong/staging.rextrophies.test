<?php

function vpc_cta_create_text_option($option,$tooltip,$price,$config_to_load,$multi_views,$component_behaviour,$component,$config){
  $first_font=get_vpc_cta_first_font();
  vpc_cta_create_text_field($option,$tooltip,$price,$first_font,$config_to_load,$multi_views,$component_behaviour,$component,$config);
}

function get_vpc_cta_first_font(){
  $fonts=get_option('vpc-cta-fonts');
  if(isset($fonts) && is_array($fonts)){
    foreach($fonts as $font)
    return $font[0];
  }
  else
  return "";
}

function get_vpc_cta_first_color(){
  $colors=get_option('vpc-cta-colors');
  if (isset($colors) && !empty($colors)) {
    foreach ($colors as $color) {
      return $color[1];
    }
  }
  else
  return "";
}

function get_text_views_to_show($option)
{
  $to_show_views = array();
  if (isset($option) && !empty($option)) {
    foreach ($option as $opt_key => $opt_value) {
      if (strstr($opt_key,'multi_views_')) {
        foreach ($opt_value as $key => $value) {
          if (!in_array($value['text_view_field'], $to_show_views)) {
            array_push($to_show_views, $value['text_view_field']);
          }
        }
      }
    }
  }
  return $to_show_views;
}

function vpc_cta_get_datas($option,$config_to_load,$price,$tooltip,$value_view,$key_view)
{
  $class=$text_value="";
  $sanitized_name = sanitize_title($option["name"]);
  $first_color=get_vpc_cta_first_color();
  $opt_name=get_proper_value($option, "name","");
  $fonts=get_option('vpc-cta-fonts');
  $opt_max_char=get_proper_value($option, "max_char",10);
  $new_tooltip = $tooltip;
  if (isset($value_view) && !empty($value_view) && isset($key_view) && !empty($key_view)) {
    $sanitized_name = sanitize_title($sanitized_name.'-'.$value_view);
    $field_name = strtoupper($value_view);
    $opt_name = $opt_name.' '.$field_name;

    $multi_views_maxi_char=get_proper_value($option, "multi_views_maxi_char",array());
    if (is_array($multi_views_maxi_char)) {
      foreach ($multi_views_maxi_char as $key => $value) {
        if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
          $opt_max_char = $value['text_max_size'];
        }
      }
    }

    if (is_array($tooltip)) {
      $new_tooltip = $tooltip[$value_view];
    }
  }
  if(isset($config_to_load[$opt_name]))
    $text_value=$config_to_load[$opt_name];
  $hidden_field_name=$opt_name.' properties';
  $font="";
  if(isset($config_to_load[$hidden_field_name])){
    $properties=explode('<br>',$config_to_load[$hidden_field_name]);
    $font_properties=explode(':',$properties[0]);
    $font=trim($font_properties[1]);
    $color_properties=explode(':',$properties[1]);
    $first_color=trim($color_properties[1]);
  }

 $multi_views_text_prices=get_proper_value($option, "multi_views_text_prices",array());
  if (is_array($multi_views_text_prices)) {
    foreach ($multi_views_text_prices as $key => $value) {
      if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
        $price = (int)$value['price'];
      }
    }
  }

  $datas = array(
    'class' => $class,
    'text_value' => $text_value,
    'sanitized_name' => $sanitized_name,
    'opt_name' => $opt_name,
    'first_color' => $first_color,
    'fonts' => $fonts,
    'opt_max_char' => $opt_max_char,
    'font' => $font,
    'price' => $price,
    'tooltip' => $new_tooltip
  );
  return $datas;
}

function vpc_cta_get_content($option,$monogram,$tooltip,$opt_name,$sanitized_name,$first_color,$fonts,$font,$class,$text_value,$opt_max_char,$price,$component,$key_view,$config)
{
  $show_fonts = get_proper_value($config, "show-fonts");
  $show_colors = get_proper_value($config, "show-colors");
  $component_id = "component_" . sanitize_title(str_replace(' ', '', $component["cname"]));
   $component_id = get_proper_value($component, "component_id", $component_id);
  ?>
  <script>
  vpc.show_fonts=<?php echo json_encode($show_fonts);?>;
  vpc.show_colors=<?php echo json_encode($show_colors);?>;
  </script>
  <?php
  if($monogram)
  $class="monogram_text";
  ?>
  <div class="vpc-single-option-wrap textfield" data-oid="<?php echo $option['option_id']; ?>" data-cid="<?php echo $component_id; ?>" >
    <label>
        <?php
        $tooltip=apply_filters("vpc_option_text_tooltip",$tooltip,$price,$option,$component);
        echo " $tooltip  ";?>
    </label>
    <input type="hidden" name="<?php echo $opt_name;?> properties" value="<?php echo __("font-family: ","vpc-cta").$font; ?> <br> <?php echo __("color: ","vpc-cta").$first_color; ?>" id="<?php echo $sanitized_name;?>-properties"/>
    <div class="vpc-textfield">
      <div class="vpc-textfield-color">
        <span class="vpc-textfield-label"><?php _e("Color","vpc-cta"); ?></span>
        <span id="<?php echo $sanitized_name;?>-color-selector"  data-field="<?php echo $sanitized_name;?>-field" style="background-color:<?php echo $first_color; ?>" data-color="<?php echo $first_color; ?>"></span>
        <span class="color-code">
          <?php echo $first_color; ?></span>
        </div>
        <?php
        if(!$monogram){
          ?>
          <div class="vpc-textfield-font">
            <span class="vpc-textfield-label">Font</span>
            <?php
            ?>
            <select id="<?php echo $sanitized_name;?>-font-selector" data-field="<?php echo $sanitized_name;?>-field" class="font-selector text-element-border">
              <?php
              if(isset($fonts) && is_array($fonts)){
                foreach ($fonts as $font_value) {
                  $font_label = $font_value[0];
                  $selected = '';
                  if ($font_label === $font) {
                    $selected = 'selected';
                  }
                  echo '<option value="'.$font_label.'" style="font-family: '.$font_label.',sans-serif" '.$selected.'>'.$font_label.'</option>';
                }
              }
              ?>
            </select>
          </div>
          <?php
        }
        ?>
      </div>
      <div class="textfield-box">
        <input id="<?php  echo $sanitized_name;?>-field"  name="<?php echo $opt_name;?>" class="<?php echo $class; ?>" type="text"  value="<?php echo $text_value;?>" maxlength="<?php echo $opt_max_char;?>"  data-price='<?php echo $price;?>'/>
      </div>
      <?php
      $field_datas = get_text_settings($option,$sanitized_name,$opt_name,$font,$price,$first_color,$key_view);
      ?>
      <script>
      vpc.text_settings["<?php echo $sanitized_name.'-field';?>"]='<?php echo json_encode($field_datas);?>';
      </script>
    </div>
    <?php
  }

  function get_text_settings($option,$sanitized_name,$opt_name,$font,$price,$first_color,$key_view)
  {
    $text_top = get_proper_value($option, "text-top");
    $text_left = get_proper_value($option, "text-left");
    $angle = get_proper_value($option, "angle");
    $font_size = get_proper_value($option, "size");

    if (isset($key_view) && $key_view !== '') {
      $multi_views_text_font_size=get_proper_value($option, "multi_views_text_font_size",array());
      if (is_array($multi_views_text_font_size)) {
        foreach ($multi_views_text_font_size as $key => $value) {
          if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
            $font_size = $value['font_size'];
          }
        }
      }

      $multi_views_text_top=get_proper_value($option, "multi_views_text_top",array());
      if (is_array($multi_views_text_top)) {
        foreach ($multi_views_text_top as $key => $value) {
          if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
            $text_top = $value['top'];
          }
        }
      }

      $multi_views_text_left=get_proper_value($option, "multi_views_text_left",array());
      if (is_array($multi_views_text_left)) {
        foreach ($multi_views_text_left as $key => $value) {
          if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
            $text_left = $value['left'];
          }
        }
      }

      $multi_views_text_rotation=get_proper_value($option, "multi_views_text_rotation",array());
      if (is_array($multi_views_text_rotation)) {
        foreach ($multi_views_text_rotation as $key => $value) {
          if (isset($value['text_view_field']) && $value['text_view_field'] === $key_view) {
            $angle = $value['rotation'];
          }
        }
      }
    }
    $field_selector=$sanitized_name."-field";
    $field_datas=array(
      'container'=>$sanitized_name."-container",
      'opt_name'=>$opt_name,
      'top' => !empty($text_top)?$text_top:0,
      'left' => !empty($text_left)?$text_left:0,
      'angle' =>!empty($angle)?$angle:0,
      'size'=> !empty($font_size)?$font_size:15,
      'font'=>$font,
      'price'=>$price,
      'default_color'=>$first_color,
      'default_font'=>$font,
      'option_id'=>$sanitized_name,
      'hidden_field_id'=>$sanitized_name.'-properties',
      'color_selector_id'=>$sanitized_name.'-color-selector',
      'font_selector_id'=>$sanitized_name.'-font-selector',
      'palettes'=>get_vpc_cta_colors_palette($field_selector),
    );
    return $field_datas;
  }

  function vpc_cta_create_text_field($option,$tooltip,$price,$font,$config_to_load,$multi_views,$component_behaviour,$component,$config,$monogram=false){
    if ($multi_views && $component_behaviour === 'multi_views_text') {
      $views = get_option('vpc-mva-views');
      $to_show_views = get_text_views_to_show($option);
      if (isset($to_show_views) && !empty($to_show_views)) {
        foreach ($to_show_views as $value) {
          if (isset($views) && !empty($views) && isset($views[$value])) {
            $price = 0;
            $datas = vpc_cta_get_datas($option,$config_to_load,$price,$tooltip,$views[$value],$value);
            vpc_cta_get_content($option,$monogram,$datas['tooltip'],$datas['opt_name'],$datas['sanitized_name'],$datas['first_color'],$datas['fonts'],$datas['font'],$datas['class'],$datas['text_value'],$datas['opt_max_char'],$datas['price'],$component,$value,$config);
          }
        }
      }
    }else {
      if (!$multi_views && $component_behaviour === 'text') {
        $datas = vpc_cta_get_datas($option,$config_to_load,$price,$tooltip,"","");
        vpc_cta_get_content($option,$monogram,$datas['tooltip'],$datas['opt_name'],$datas['sanitized_name'],$datas['first_color'],$datas['fonts'],$datas['font'],$datas['class'],$datas['text_value'],$datas['opt_max_char'],$datas['price'],$component,'',$config);
      }
    }
  }

  function get_vpc_cta_colors_palette($selector){
    $palette="";
    $colors=get_option('vpc-cta-colors');
    if (isset($colors) && !empty($colors)) {
      foreach ($colors as $color) {
        $palette.='<div style=\"display:inline-block;margin-right: 8%;\"><strong data-name=\"'.$selector.'\" style=\"display:block;margin: 10px 0 5px 0;color:#768e9d !important; text-align:left;\">'. $color[0] .'</strong><span data-selector=\"'.$selector.'\" style=\"background-color: ' . $color[1] . '\" data-color=\"' . $color[1] . '\" data-name=\"' . $color[0] . '\" class=\"vpc-custom-color\"></span></div>';
      }
    }
    return $palette;
  }
  function get_vpc_ttf_font_style($font) {
    $font_label = $font[0];
    $font_ttf_files = $font[2];
    foreach ($font_ttf_files as $font_file) {
        $font_styles = $font_file["styles"];
        $font_file_url = wp_get_attachment_url($font_file["file_id"]);
        if (!$font_file_url)
            continue;
        foreach ($font_styles as $font_style) {
            if ($font_style == "")
                $font_style_css = "";
            elseif ($font_style == "I")
                $font_style_css = "font-style:italic;";
            elseif ($font_style == "B")
                $font_style_css = "font-weight:bold;"
                ?>
                <style>
                    @font-face {
                        font-family: "<?php echo $font_label; ?>";
                    src: url('<?php echo $font_file_url; ?>') format('truetype');
            <?php echo $font_style_css; ?>
                }
            </style>
                    <?php
                }
            }
        }
