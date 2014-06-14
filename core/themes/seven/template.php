<?php

/**
 * Implements hook_element_info_alter().
 */
function seven_element_info_alter(&$elements) {
  foreach ($elements as &$element) {
    // Process all elements.
    $element['#process'][] = '_seven_process_element';
  }
}

/**
 * Process all elements.
 */
function _seven_process_element(&$element, &$form_state) {
  // Process form actions.
  if (!empty($element['#type']) && $element['#type'] === 'actions') {
    foreach (element_children($element) as $child) {
      _bootstrap_iconize_button($element[$child]);
    }
  }
  return $element;
}


/**
 * Override or insert variables into the maintenance page template.
 */
function seven_preprocess_maintenance_page(&$vars) {
  // While markup for normal pages is split into page.tpl.php and html.tpl.php,
    // the markup for the maintenance page is all in the single
    // maintenance-page.tpl.php template. So, to have what's done in
    // seven_preprocess_html() also happen on the maintenance page, it has to be
    // called here.
  seven_preprocess_html($vars);

    // Add bootstrap framework
    drupal_add_css(path_to_theme(). '/flat/bootstrap/css/bootstrap.css', array('group'=> CSS_THEME, 'preprocess' => FALSE));
    drupal_add_css(path_to_theme(). '/flat/css/flat-ui.css', array('group'=> CSS_THEME, 'preprocess' => FALSE));
    drupal_add_js(path_to_theme(). '/flat/js/flatui-radio.js', array('type' => 'file'));
    drupal_add_js(path_to_theme(). '/flat/js/app.js', array('type' => 'file'));
}

/**
 * Override or insert variables into the html template.
 */
function seven_preprocess_html(&$vars) {
  // Add conditional CSS for IE8 and below.
  drupal_add_css(path_to_theme() . '/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 8', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE7 and below.
  drupal_add_css(path_to_theme() . '/ie7.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));
  // Add conditional CSS for IE6.
  drupal_add_css(path_to_theme() . '/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 6', '!IE' => FALSE), 'weight' => 999, 'preprocess' => FALSE));

  drupal_add_js(path_to_theme(). '/flat/js/bootstrap.min.js', array('type' => 'file'));
}

/**
 * Override or insert variables into the page template.
 */
function seven_preprocess_page(&$vars) {
  $vars['primary_local_tasks'] = $vars['tabs'];
  unset($vars['primary_local_tasks']['#secondary']);
  $vars['secondary_local_tasks'] = array(
    '#theme' => 'menu_local_tasks',
    '#secondary' => $vars['tabs']['#secondary'],
  );
}

/**
 * Display the list of available node types for node creation.
 */
function seven_node_add_list($variables) {
  $content = $variables['content'];
  $output = '';
  if ($content) {
    $output = '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="clearfix">';
      $output .= '<span class="label">' . l($item['title'], $item['href'], $item['localized_options']) . '</span>';
      $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  else {
    $output = '<p>' . t('You have not created any content types yet. Go to the <a href="@create-content">content type creation page</a> to add a new content type.', array('@create-content' => url('admin/structure/types/add'))) . '</p>';
  }
  return $output;
}

/**
 * Overrides theme_admin_block_content().
 *
 * Use unordered list markup in both compact and extended mode.
 */
function seven_admin_block_content($variables) {
  $content = $variables['content'];
  $output = '';
  if (!empty($content)) {
    $output = system_admin_compact_mode() ? '<ul class="admin-list compact">' : '<ul class="admin-list">';
    foreach ($content as $item) {
      $output .= '<li class="leaf">';
      $output .= l($item['title'], $item['href'], $item['localized_options']);
      if (isset($item['description']) && !system_admin_compact_mode()) {
        $output .= '<div class="description">' . filter_xss_admin($item['description']) . '</div>';
      }
      $output .= '</li>';
    }
    $output .= '</ul>';
  }
  return $output;
}

/**
 * Override of theme_tablesort_indicator().
 *
 * Use our own image versions, so they show up as black and not gray on gray.
 */
function seven_tablesort_indicator($variables) {
  $style = $variables['style'];
  $theme_path = drupal_get_path('theme', 'seven');
  if ($style == 'asc') {
    return theme('image', array('path' => $theme_path . '/images/arrow-asc.png', 'alt' => t('sort ascending'), 'width' => 13, 'height' => 13, 'title' => t('sort ascending')));
  }
  else {
    return theme('image', array('path' => $theme_path . '/images/arrow-desc.png', 'alt' => t('sort descending'), 'width' => 13, 'height' => 13, 'title' => t('sort descending')));
  }
}

/**
 * Implements hook_css_alter().
 */
function seven_css_alter(&$css) {
  // Use Seven's vertical tabs style instead of the default one.
  if (isset($css['misc/vertical-tabs.css'])) {
    $css['misc/vertical-tabs.css']['data'] = drupal_get_path('theme', 'seven') . '/vertical-tabs.css';
    $css['misc/vertical-tabs.css']['type'] = 'file';
  }
  if (isset($css['misc/vertical-tabs-rtl.css'])) {
    $css['misc/vertical-tabs-rtl.css']['data'] = drupal_get_path('theme', 'seven') . '/vertical-tabs-rtl.css';
    $css['misc/vertical-tabs-rtl.css']['type'] = 'file';
  }
  // Use Seven's jQuery UI theme style instead of the default one.
  if (isset($css['misc/ui/jquery.ui.theme.css'])) {
    $css['misc/ui/jquery.ui.theme.css']['data'] = drupal_get_path('theme', 'seven') . '/jquery.ui.theme.css';
    $css['misc/ui/jquery.ui.theme.css']['type'] = 'file';
  }
}


/**
 * Overrides status_messages();
 */
function seven_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
    'info' => t('Informative message'),
  );

  // Map Drupal message types to their corresponding Bootstrap classes.
  // @see http://twitter.github.com/bootstrap/components.html#alerts
  $status_class = array(
    'status' => 'success',
    'error' => 'danger',
    'warning' => 'warning',
    // Not supported, but in theory a module could send any type of message.
    // @see drupal_set_message()
    // @see theme_status_messages()
    'info' => 'info',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $class = (isset($status_class[$type])) ? ' alert-' . $status_class[$type] : '';
    $output .= "<div class=\"alert alert-block$class\">\n";
    $output .= "  <a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a>\n";

    if (!empty($status_heading[$type])) {
      $output .= '<h4 class="element-invisible">' . $status_heading[$type] . "</h4>\n";
    }

    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }

    $output .= "</div>\n";
  }
  return $output;
}


/**
 * Implement hook_form_alter();
 */
function seven_form_alter(&$form, &$form_state, $form_id) {
  switch ($form_id) {
    case 'install_configure_form':
      //$form['#prefix'] = '<i class="glyphicon-play-circle dont_show"></i>';
      //$form['actions']['submit']['#value'] = 'Далее';
      break;
  }
}


/**
 * OVERIDE BUTTONS
 */


/**
 * Overrides theme_button().
 */
function seven_button($variables) {
  // This line break adds inherent margin between multiple buttons.
  return '<button' . drupal_attributes($variables['element']['#attributes']) . '>' . $variables['element']['#value'] . "</button>\n";
}

/**
 * Implements hook_preprocess_button().
 */
function seven_preprocess_button(&$vars) {
  $element = &$vars['element'];

  // Set the element's attributes.
  element_set_attributes($element, array('id', 'name', 'value', 'type'));

  // Add the base Bootstrap button class.
  $element['#attributes']['class'][] = 'btn';
  // Colorize button.
  _bootstrap_colorize_button($element);

  // Add in the button type class.
  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];

  // Ensure that all classes are unique, no need for duplicates.
  $element['#attributes']['class'] = array_unique($element['#attributes']['class']);
}

/**
 * Iconize buttons based on the text value.
 *
 */
function _bootstrap_iconize_button(&$element) {
  if (_bootstrap_is_button($element) && ($icon = _bootstrap_iconize_text($element['#value']))) {
    $element['#value'] = $icon . ' ' . $element['#value'];
  }
}

/**
 * Helper function for determining whether an element is a button.
 *
 * @param array $element
 *   A renderable element.
 *
 * @return bool
 *   TRUE or FALSE.
 */
function _bootstrap_is_button($element) {
  return
    !empty($element['#type']) &&
    !empty($element['#value']) && (
      $element['#type'] === 'button' ||
      $element['#type'] === 'submit' ||
      $element['#type'] === 'image_button'
    );
}

/**
 * Helper function for associating Bootstrap icons with text.
 *
 * @param string $text
 *   Text string to search against.
 *
 * @return string
 *   The Bootstrap icon associated with text matching or FALSE if no match.
 */
function _bootstrap_iconize_text($text) {
  // Text values containing these specific strings, which are matched first.
  $specific_strings = array();
  // Text values containing these generic strings, which are matches last.
  $generic_strings = array(
    'cog' => array(
      t('Manage'),
      t('Configure'),
    ),
    'download' => array(
      t('Download'),
    ),
    'export' => array(
      t('Export'),
    ),
    'import' => array(
      t('Import'),
    ),
    'import' => array(
      t('Update'),
    ),
    'ok' => array(
      t('Save'),
      t('Update'),
      'Установить',
    ),
    'eye-open' => array(
      t('Preview'),
    ),
    'pencil' => array(
      t('Edit'),
    ),
    'plus' => array(
      t('Add'),
      t('Write'),
    ),
    'remove' => array(
      t('Cancel'),
    ),
    'trash' => array(
      t('Delete'),
      t('Remove'),
    ),
    'upload' => array(
      t('Upload'),
    ),
  );
  // Specific matching first.
  foreach ($specific_strings as $icon => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return _bootstrap_icon($icon);
      }
    }
  }
  // Generic matching last.
  foreach ($generic_strings as $icon => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return _bootstrap_icon($icon);
      }
    }
  }
  return FALSE;
}

/**
 * Helper function for associating Bootstrap classes on text.
 *
 * @param string $text
 *   Text string to search against.
 *
 * @return string
 *   The Bootstrap class associated with text matching or FALSE if no match.
 */
function _bootstrap_colorize_text($text) {
  // Text values containing these specific strings, which are matched first.
  $specific_strings = array(
    'primary' => array(
      t('Download feature'),
    ),
    'success' => array(
      t('Add effect'),
      t('Add and configure'),
    ),
    'info' => array(
      t('Save and add'),
      t('Add another item'),
      t('Update style'),
    ),
  );
  // Text values containing these generic strings, which are matches last.
  $generic_strings = array(
    'primary' => array(
      t('Confirm'),
      t('Submit'),
      t('Search'),
      t('Log in'),
      ('Восстановить'),
      ('Регистрация'),
      ('Отправить'),

    ),
    'success' => array(
      t('Add'),
      t('Create'),
      t('Save'),
      t('Write'),
      t('Refine'),
      t('Filter'),

    ),
    'warning' => array(
      t('Export'),
      t('Import'),
      t('Restore'),
      t('Rebuild'),
    ),
    'info' => array(
      t('Apply'),
      ('Найти'),
      t('Update'),
      t('Upload'),
      t('Undo'),
      t('Start'),
      'Далее',
      'Установить',
    ),
    'danger' => array(
      t('Delete'),
      t('Remove'),
      t('Reset'),

    ),
  );
  // Specific matching first.
  foreach ($specific_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  // Generic matching last.
  foreach ($generic_strings as $class => $strings) {
    foreach ($strings as $string) {
      if (strpos(drupal_strtolower($text), drupal_strtolower($string)) !== FALSE) {
        return $class;
      }
    }
  }
  return FALSE;
}


/**
 * Theme a Bootstrap Glyphicon.
 *
 * @param string $name
 *   The icon name, minus the "glyphicon-" prefix.
 *
 * @return string
 *   The icon HTML markup.
 */
function _bootstrap_icon($name) {
  $output = '';
  // Attempt to use the Icon API module, if enabled and it generates output.
  if (module_exists('icon')) {
    $output = theme('icon', array('bundle' => 'bootstrap', 'icon' => 'glyphicon-' . $name));
  }
  if (empty($output)) {
    // Mimic the Icon API markup.
    $attributes = array(
      'class' => array('icon', 'glyphicon', 'glyphicon-' . $name),
      'aria-hidden' => 'true',
    );
    $output = '<i' . drupal_attributes($attributes) . '></i>';
  }
  return $output;
}

/**
 * Helper function for adding colors to button elements.
 *
 * @param array $element
 *   The form element, passed by reference.
 */
function _bootstrap_colorize_button(&$element) {
  if (_bootstrap_is_button($element)) {
    // Do not add the class if one is already present in the array.
    $button_classes = array(
      'btn-default',
      'btn-primary',
      'btn-success',
      'btn-info',
      'btn-warning',
      'btn-danger',
      'btn-link',
    );
    $class_intersection = array_intersect($button_classes, $element['#attributes']['class']);
    if (empty($class_intersection)) {
      // Get the matched class.
      $class = _bootstrap_colorize_text($element['#value']);
      // If no particular class matched, use the default style.
      if (!$class) {
        $class = 'default';
      }
      $element['#attributes']['class'][] = 'btn-' . $class;
    }
  }
}
