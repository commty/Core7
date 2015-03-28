<?php
/**
 * @file
 * Enables modules and site configuration for a minimal site installation.
 */

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function minimal_form_install_configure_form_alter(&$form, $form_state) {
  // Pre-populate the site name with the server name.
  $form['site_information']['site_name']['#default_value'] = $_SERVER['SERVER_NAME'];
  $form['server_settings']['site_default_country']['#default_value'] = 'RU';
  // Use "Happensit" as the default username.
  $form['admin_account']['account']['name']['#default_value'] = 'Happensit';
  // Define a default email address if we can guess a valid one
  if (valid_email_address('mail@' . $_SERVER['HTTP_HOST'])) {
    $form['site_information']['site_mail']['#default_value'] = 'mail@' . $_SERVER['HTTP_HOST'];
    $form['admin_account']['account']['mail']['#default_value'] = 'mail@' . $_SERVER['HTTP_HOST'];
  }
}

function minimal_install_tasks_alter(&$tasks, $install_state){
  global $install_state;

  $tasks['install_select_locale']['display'] = FALSE;
  $install_state['parameters']['locale'] = 'ru';
}
