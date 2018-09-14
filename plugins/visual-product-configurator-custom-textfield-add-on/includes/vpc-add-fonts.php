<?php
if (!defined('ABSPATH'))
exit; // Exit if accessed directly

function woocommerce_vpc_cta_add_fonts() {
  if (isset($_GET['error'])) {
    echo $_GET['error'];
  }
  // Action to perform: add, edit, delete or none
  $action = '';
  if (!empty($_POST['add_new_font'])) {
    $action = 'add';
  } elseif (!empty($_POST['save_font']) && !empty($_GET['edit'])) {
    $action = 'edit';
  } elseif (!empty($_GET['delete'])) {
    $action = 'delete';
  }

  // Add or edit an attribute
  if ('add' === $action || 'edit' === $action) {
      // Security check
      if ('add' === $action) {
          check_admin_referer('woocommerce-add-new_font');
      }
      if ('edit' === $action) {
          $font_key = absint($_GET['edit']);
          check_admin_referer('woocommerce-save-font_' . $font_key);
      }
      // Grab the submitted data
      $font_label = ( isset($_POST['font_label']) ) ? (string) stripslashes($_POST['font_label']) : '';
      $font_url = ( isset($_POST['font_url']) ) ? (string) stripslashes($_POST['font_url']) : '';
      $font_file = ( isset($_POST['font_file']) ) ? (array) $_POST['font_file'] : '';
//                $font_family=( isset( $_POST['font_family'] ) )   ? (string) stripslashes( $_POST['font_family'] ) : '';
      if ('add' === $action) {
          if ($font_label) {
              $fonts = get_option('vpc-cta-fonts');
              if (empty($fonts)) {
                  $i = 1;
                  $fonts[$i] = array($font_label, $font_url, $font_file);
              } else {
                  $font_labels = array_map(create_function('$o', 'return $o[0];'), $fonts);
//                            var_dump($font_labels);
                  if (in_array($font_label, $font_labels))
                      $error = '<div class=error>This font exist !</div>';
                  else
                      $fonts[] = array($font_label, $font_url, $font_file);
              }
              update_option('vpc-cta-fonts', $fonts);
              $action_completed = true;
          }
          else {
              $error = '<div class=error>Missing font name.</div>';
              $action_completed = true;
          }
      }
      // Edit existing attribute
      if ('edit' === $action) {
          $fonts = get_option('vpc-cta-fonts');
          $edit = $_GET['edit'];
          $fonts[$edit] = array($font_label, $font_url, $font_file);
          update_option('vpc-cta-fonts', $fonts);
          $action_completed = true;
      }
  }

  // Delete an attribute
  if ('delete' === $action) {
    // Security check
    $font_id = absint($_GET['delete']);
    $fonts = get_option('vpc-cta-fonts');
    unset($fonts[$font_id]);
    update_option('vpc-cta-fonts', $fonts);
  }

  // If an attribute was added, edited or deleted: clear cache and redirect
  if (!empty($action_completed)) {
    //delete_transient( 'wc_attribute_taxonomies' );
    if (!empty($error))
    wp_safe_redirect(get_admin_url() . 'admin.php?page=vpc_cta_add_fonts&error=' . urlencode($error));
    else {
      wp_safe_redirect(get_admin_url() . 'admin.php?page=vpc_cta_add_fonts');
    }
    exit;
  }
  // Show
  // admin interface
  if (!empty($_GET['edit']))
  woocommerce_edit_font();
  else
  woocommerce_add_font();
}

function woocommerce_edit_font() {
  $edit = absint($_GET['edit']);
  $fonts = get_option('vpc-cta-fonts');
  $font_label = $fonts[$edit][0];
  $font_url = $fonts[$edit][1];
  $font_file = ( isset($fonts[$edit][2]) ) ? (array) ($fonts[$edit][2] ) : '';
  wp_enqueue_media();
  //    $font_family=$fonts[$edit][2];
  ?>
  <div class="wrap woocommerce">
      <div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
      <h2><?php _e('Edit Font', 'vpc-cta') ?></h2>
      <form action="admin.php?page=vpc_cta_add_fonts&amp;edit=<?php echo absint($edit); ?>&amp;noheader=true" method="post">
          <?php font_select2($font_label);?>
          <table class="form-table">
              <tbody>
                  <tr class="form-field form-required">
                      <th scope="row" valign="top">
                          <label for="font_label"><?php _e('Name', 'vpc-cta'); ?></label>
                      </th>
                      <td>
                          <input name="font_label" id="font_label" class="font_auto_name" type="text" value="<?php echo esc_attr($font_label); ?>" />
                          <p class="description"><?php _e('Name for the attribute (shown on the front-end).', 'vpc-cta'); ?></p>
                      </td>
                  </tr>
                  <!-- <tr class="form-field">
                  </tr> -->
                  <tr class="form-field">
                      <th scope="row" valign="top">
                          <label for="font_label"><?php _e('URL', 'vpc-cta'); ?></label>
                      </th>
                      <td>
                          <input name="font_url" id="font_label" class="font_auto_url" type="text" value="<?php echo esc_attr($font_url); ?>" />
                          <p class="description"><?php _e('Google font URL. Leave this field empty if the font is already loaded by the theme.', 'vpc-cta'); ?></p>
                      </td>
                  </tr>
                  <tr class="form-field">
                      <th scope="row" valign="top">
                          <label for="font_file"><?php _e('TTF font file', 'vpc-cta'); ?></label>
                      </th>
                      <td>
                          <div>
                              <textarea id='vpc-cta-font-tpl' style='display: none;'>
  <?php echo get_font_tpl(); ?>
                              </textarea>
                              <div><a class='o-add-font-file button'><?php _e('Add font file', 'vpc-cta'); ?></a><br>
                                  <span style="color:red;"><?php _e('Make sure you select at least one style per file.', 'vpc-cta'); ?></span>
                              </div>
                              <table class="font_style_table">
                                  <thead>
                                  <th>Style</th>
                                  <th>File</th>
                                  <th>Action</th>
                                  </thead>
                                  <tbody>
  <?php
  echo get_font_tpl($font_file);
  ?>
                                  </tbody>
                              </table>
                          </div>
                          <p class="description"><?php _e('TrueType font file (can be used if the url is not provided and while generating the output vector.', 'vpc-cta'); ?></p>
                      </td>
                  </tr>
              </tbody>
          </table>
          <p class="submit"><input type="submit" name="save_font" id="submit" class="button-primary" value="<?php _e('Update', 'vpc-cta'); ?>"></p>
  <?php wp_nonce_field('woocommerce-save-font_' . $edit); ?>
      </form>
  </div>
<?php
}

function woocommerce_add_font() {
  wp_enqueue_media();
  ?>
  <div class="wrap woocommerce">
      <div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
      <h2><?php _e('Add Fonts', 'vpc-cta') ?></h2>
      <br class="clear" />
      <div id="col-container">
          <div id="col-right">
              <div class="col-wrap">
                  <table class="widefat fixed" style="width:100%">
                      <thead>
                          <tr>
                              <th scope="col"><?php _e('Name', 'vpc-cta') ?></th>
                              <th scope="col"><?php _e('Url', 'vpc-cta') ?></th>
                              <th scope="col"><?php _e('Style name', 'vpc-cta') ?></th>
                              <th scope="col"><?php _e('TTF font file', 'vpc-cta') ?></th>
                          </tr>
                      </thead>
                      <tbody>
  <?php
  $fonts = get_option('vpc-cta-fonts');
  if ($fonts) :
      foreach ($fonts as $key => $font_arr) :
          $font = $font_arr[0];
          $font_url = $font_arr[1];
          if (!isset($font_arr[2]))
              $font_arr[2] = array();
          ?><tr>

                                      <td><a href="<?php echo esc_url(add_query_arg('edit', $key, 'admin.php?page=vpc_cta_add_fonts')); ?>"><?php echo esc_html($font); ?></a>

                                          <div class="row-actions"><span class="edit"><a href="<?php echo esc_url(add_query_arg('edit', $key, 'admin.php?page=vpc_cta_add_fonts')); ?>"><?php _e('Edit', 'vpc-cta'); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url(wp_nonce_url(add_query_arg('delete', $key, 'admin.php?page=vpc_cta_add_fonts'), 'woocommerce-delete-attribute_' . $key)); ?>"><?php _e('Delete', 'vpc-cta'); ?></a></span></div>
                                      </td>
                                      <td><?php echo esc_html($font_url); ?> </td>
                                      <td>
          <?php
          if (is_array($font_arr[2])):
              foreach ($font_arr[2] as $key => $fonts_styles) :
                  $i = 1;
                  foreach ($fonts_styles['styles'] as $style):
                      if ($i == count($fonts_styles['styles']))
                          echo fonts_array($style);
                      else
                          echo fonts_array($style) . '+';
                      $i++;
                  endforeach;
                  echo "<br>";
              endforeach;
          endif;
          ?>
                                      </td>
                                      <td>
                                          <?php
                                          if (is_array($font_arr[2])):
                                              foreach ($font_arr[2] as $key => $fonts_styles) :
                                                  $font_file_url = wp_get_attachment_url($fonts_styles['file_id']);
                                                  echo basename($font_file_url) . "<br>";
                                              endforeach;
                                          endif;
                                          ?>
                                          </ul>

                                      </td>
                                  </tr><?php
                                      endforeach;
                                  else :
                                      ?><tr><td colspan="4"><?php _e('No fonts currently exist.', 'vpc-cta') ?></td></tr><?php
                                  endif;
                                  ?>
                      </tbody>
                  </table>
              </div>
          </div>
          <div id="col-left">
              <div class="col-wrap">
                  <div class="form-wrap">
                      <h3><?php _e('Add New Font', 'vpc-cta') ?></h3>
                      <form action="admin.php?page=vpc_cta_add_fonts&amp;noheader=true" method="post">
                          <?php font_select2();?>
                          <div class="form-field">
                              <label for="font_label"><?php _e('Name', 'vpc-cta'); ?></label>
                              <input name="font_label" class="font_auto_name" id="font_label" type="text" value="" />
                              <p class="description"><?php _e('Name for the font (shown on the front-end).', 'vpc-cta'); ?></p>
                          </div>
                          <div class="form-field">
                              <label for="font_url"><?php _e('URL', 'vpc-cta'); ?></label>
                              <input name="font_url" id="font_label" class="font_auto_url" type="text" value="" />
                              <p class="description"><?php _e('Google font URL. Leave this field empty if the font is already loaded by the theme.', 'vpc-cta'); ?></p>
                          </div>
                          <div class="form-field">
                              <textarea id='vpc-cta-font-tpl' style='display: none;'>
  <?php echo get_font_tpl(); ?>
                              </textarea>
                              <label for="font_file"><?php _e('TTF font file', 'vpc-cta'); ?></label>
                              <div>
                                  <div>
                                      <a class='o-add-font-file button'><?php _e('Add font file', 'vpc-cta'); ?></a><br>
                                      <span style="color:red;"><?php _e('Make sure you select at least one style per file.', 'vpc-cta'); ?></span>
                                  </div>
                                  <table class="font_style_table">
                                      <thead>
                                      <th>Style</th>
                                      <th>File</th>
                                      <th>Action</th>
                                      </thead>
                                      <tbody>

                                      </tbody>
                                  </table>

                              </div>
                              <p class="description"><?php _e('TrueType font (can be used if the url is not provided and while generating the output vector.', 'vpc-cta'); ?></p>
                          </div>
                          <p class="submit"><input type="submit" name="add_new_font" id="submit" class="button" value="<?php _e('Add Font', 'vpc-cta'); ?>"></p>
  <?php wp_nonce_field('woocommerce-add-new_font'); ?>
                      </form>
                  </div>
              </div>
          </div>
      </div>
      <script type="text/javascript">
          jQuery('a.delete').click(function () {
              var answer = confirm("<?php _e('Are you sure you want to delete this font?', 'vpc-cta'); ?>");
              if (answer)
                  return true;
              return false;
          });
      </script>
  </div>
  <?php
}

function get_font_tpl($font_array = false) {
  if ($font_array) {
      $tpl = "";
      foreach ($font_array as $key => $fonts_styles) :
          if (!empty($fonts_styles)):
              if (isset($fonts_styles['file_id'])):
                  $file_id = $fonts_styles['file_id'];
                  $font_file_url = wp_get_attachment_url($file_id);
                  $file_name = basename($font_file_url);
              else:
                  $file_name = '';
              endif;
              $tpl.="<tr>
                      <td>
                          <ul class='radio'>";

              if (isset($fonts_styles['styles']) && in_array('', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='' checked='checked'/>Regular</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='' />Regular</li>";
              if (isset($fonts_styles['styles']) && in_array('B', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='B' checked='checked'/>Bold</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='B' />Bold</li>";
              if (isset($fonts_styles['styles']) && in_array('U', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='U' checked='checked'/>Underline</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='U' />Underline</li>";
              if (isset($fonts_styles['styles']) && in_array('D', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='D' checked='checked'/>Line Through</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='D' />Line Through</li>";
              if (isset($fonts_styles['styles']) && in_array('I', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='I' checked='checked'/>Italic</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='I' />Italic</li>";
              if (isset($fonts_styles['styles']) && in_array('O', $fonts_styles['styles']))
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='O' checked='checked'/>Overline</li>";
              else
                  $tpl.="<li><input type='checkbox' name='font_file[$key][styles][]' value='O' />Overline</li>";
              $tpl.=" </ul>
                         </td>
                         <td>
                             <div class='media-name'>$file_name</div>
                         </td>
                         <td id='file_data_$key'>
                             <button class='button o-remove-font-file' data-selector='file_container_$key'>" . __("Remove font", "vpc-cta") . "</button>
                             <input type='hidden' id='font_file' name='font_file[$key][file_id]' value='$file_id'>
                      </td>
                  </tr>";
          endif;
      endforeach;
  }
  else {
      $tpl = "<tr>
               <td>
                        <ul class='radio'>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='' />Regular</li>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='B' />Bold</li>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='U' />Underline</li>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='D' />Line Through</li>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='I' />Italic</li>
                          <li><input type='checkbox' name='font_file[{index}][styles][]' value='O' />Overline</li>
                        </ul>
                  </td>
                  <td>
                      <div class='media-name'>
                      </div>
                  </td>
                  <td id='file_data_{index}'>
                      <button class='button o-remove-font-file' data-selector='file_container_{index}'>" . __("Remove font", "vpc-cta") . "</button>
                      <input type='hidden' id='font_file' name='font_file[{index}][file_id]' value=''>
               </td>
           </tr>";
  }
  return $tpl;
}

function fonts_array($value) {
  $fonts_array = array(
    '' => 'Regular',
    'B' => 'Bold',
    'I' => 'Italic',
    'U' => 'Underline',
    'D' => 'Line Through',
    'O' => 'Overline'
  );
  return $fonts_array[$value];
}

function font_select2($selected_font=false) {
  $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBwjhzcfEEHD0cL0S90wDyvoKHLGJdwWvY';
  $test_url = @fopen($url, 'r');

  if ($test_url) {
      $url = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBwjhzcfEEHD0cL0S90wDyvoKHLGJdwWvY');
      $url_decode = json_decode($url, true);
      fclose($test_url);
  } else {
      $url = file_get_contents(plugin_dir_path(dirname(__FILE__)) . 'admin/js/google-fonts.json');
      $url_decode = json_decode($url, true);
  }
  ?>
  <select id="font">
  <?php
  echo '<option>' . __('Pick a google font', 'vpc-cta') . ' </option>';
  foreach ($url_decode['items'] as $font) {
      if (isset($font['family']) AND isset($font['files']) AND isset($font['files']['regular'])) {
          $selected="";
          if($selected_font==$font['family'])
              $selected="selected";
          echo '<option value="' . 'http://fonts.googleapis.com/css?family=' . urlencode($font['family']) . '" '.$selected.'>' . $font['family'] . '</option> ';
      }
  }
  ?>
  </select>
      <?php
      return $url_decode['items'];
}
