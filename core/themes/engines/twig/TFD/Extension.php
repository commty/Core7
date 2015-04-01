<?php

/* register the drupal specific tags and filters within a
* proper declared twig extension
*/

class TFD_Extension extends Twig_Extension {

  public function getGlobals() {
    return array(
      'base_path' => base_path(),

    );
  }

  public function getOperators() {
    return array(
      array(), // There are no UNARY operators
      array( // Just map || and && for convience to developers
        '||' => array(
          'precedence' => 10,
          'class' => 'Twig_Node_Expression_Binary_Or',
          'associativity' => Twig_ExpressionParser::OPERATOR_LEFT
        ),
        '&&' => array(
          'precedence' => 15,
          'class' => 'Twig_Node_Expression_Binary_And',
          'associativity' => Twig_ExpressionParser::OPERATOR_LEFT
        )
      )
    );
  }

  /**
   * registers the drupal specific filters
   * @implements hook_twig_filter
   * please note that we use the key for convience, it allows developers of
   * modules to test if their wanted filter is already registered.
   *
   * The key is never used
   */
  public function getFilters() {
    $filters = array();
    $filters['strreplace'] = new Twig_SimpleFilter('strreplace', 'tfd_str_replace');

    $filters['dump'] = new Twig_SimpleFilter('dump', 'tfd_dump', array('needs_environment' => TRUE));
    $filters['defaults'] = new Twig_SimpleFilter('defaults', 'tfd_defaults_filter');
    $filters['size'] = new Twig_SimpleFilter('size', 'format_size');
    $filters['interval'] = new Twig_SimpleFilter('interval', 'tfd_interval');
    $filters['plural'] = new Twig_SimpleFilter('plural', 'format_plural');
    $filters['url'] = new Twig_SimpleFilter('url', 'tfd_url');
    $filters['t'] = new Twig_SimpleFilter('t', 't');
    $filters['ucfirst'] = new Twig_SimpleFilter('ucfirst', 'ucfirst');
    $filters['wrap'] = new Twig_SimpleFilter('wrap', 'tfd_wrap_text');
    $filters['image_url'] = new Twig_SimpleFilter('image_url', 'tfd_image_url');
    $filters['image_size'] = new Twig_SimpleFilter('image_size', 'tfd_image_size');
    $filters['truncate'] = new Twig_SimpleFilter('truncate', 'tfd_truncate_text');
    $filters['striphashes'] = new Twig_SimpleFilter('striphashes', 'tfd_striphashes');
    $filters = array_merge($filters, module_invoke_all('twig_filter', $filters, $this));
    return array_values($filters);

  }


  /**
   * registers the drupal specific functions
   *
   * @implements hook_twig_function
   * please note that we use the key for convience, it allows developers of
   * modules to test if their wanted filter is already registered.
   *
   * The key is never used!
   */
  public function getFunctions() {
    $functions = array();
    $functions['theme_get_setting'] = new Twig_SimpleFunction('theme_get_setting', 'theme_get_setting');
    $functions['module_exists'] = new Twig_SimpleFunction('module_exists', 'module_exists');
    $functions['dump'] = new Twig_SimpleFunction('dump', 'tfd_dump', array('needs_environment' => TRUE));
    $functions['render'] = new Twig_SimpleFunction('render', 'tfd_render');
    $functions['url'] = new Twig_SimpleFunction('url', 'tfd_url');
    $functions['classname'] = new Twig_SimpleFunction('classname', 'get_class');
    $functions['variable_get'] = new Twig_SimpleFunction('variable_get', 'variable_get');
    $functions['array_search'] = new Twig_SimpleFunction('array_search', 'array_search');
    $functions['machine_name'] = new Twig_SimpleFunction('machine_name', 'tfd_machine_name');
    $functions['viewblock'] = new Twig_SimpleFunction('viewblock', 'tfd_view_block');
    $functions['user_is_logged_in'] = new Twig_SimpleFunction('user_is_logged_in', 'tfd_user_is_logged_in');
    $functions['base_url'] = new Twig_SimpleFunction('base_url', 'tfd_base_url');
    $functions['get_form'] = new Twig_SimpleFunction('get_form', 'tfd_get_form');


    $functions = array_merge($functions, module_invoke_all('twig_function', $functions, $this));
    return array_values($functions);
  }


  /**
   * registers the drupal specific tests
   *
   * @implements hook_twig_test
   * please note that we use the key for convience, it allows developers of
   * modules to test if their wanted filter is already registered.
   *
   * The key is never used!
   */
  public function getTests() {
    $tests = array();
    $tests['property'] = new Twig_SimpleTest('property', 'tfd_test_property');
    $tests['number'] = new Twig_SimpleTest('number', 'tfd_test_number');
    $tests = array_merge($tests, module_invoke_all('twig_test', $tests, $this));
    return array_values($tests);
  }

  public function getName() {
    return 'twig_for_drupal';
  }
}

/* ------------------------------------------------------------------------------------------------
/* the above declared filter implementations
 ------------------------------------------------------------------------------------------------*/

/**
 * Wrapper around the default drupal render function.
 * This function is a bit smarter, as twig passes a NULL if the item you want to
 * be rendered is not found in the $context (aka template variables!)
 *
 * @param $var array item from the render array of doom item you wish to be rendered.
 * @return string
 */
function tfd_render($var) {
  if (isset($var) && !is_null($var)) {
    if (is_scalar($var)) {
      return $var;
    }
    elseif (is_array($var)) {
      return render($var);
    }
  }
}

function tfd_self($enviroment) {
  /** @var $enviroment TFD_Environment */
  echo "Dump in " . __FILE__ . " @ line " . __LINE__ . "<pre>";
  print_r($enviroment);
  echo "<pre>";
  die();

}

/**
 * Wrapper around the default drupal hide function.
 *
 * This function is a bit smarter, as twig passes a NULL if the item you want to
 * be hiden is not found in the $context (aka template variables!)
 *
 * @param $var array item from the render array of doom item you wish to hide.
 * @return mixed
 */
function tfd_hide(&$var) {
  if (!is_null($var) && !is_scalar($var) && count($var) > 0) {
    hide($var);
  }
}

/**
 * Returns only the keys of an array that do not start with #
 * AKA, clean the drupal render_array() a little
 *
 * @param $element
 */
function tfd_striphashes($array) {
  $output = array();
  foreach ($array as $key => $value) {
    if ($key[0] !== '#') {
      $output[$key] = $value;
    }
  }
  return $output;
}

/**
 * Twig filter for str_replace, switches needle and arguments to provide sensible
 * filter arguments order
 *
 * {{ haystack|replace("needle", "replacement") }}
 *
 * @param  $haystack
 * @param  $needle
 * @param  $repl
 * @return mixed
 */
function tfd_str_replace($haystack, $needle, $repl) {
  $haystack = tfd_render($haystack);
  return str_ireplace($needle, $repl, $haystack);
}


function tfd_defaults_filter($value, $defaults = NULL) {
  $args = func_get_args();
  $args = array_filter($args);
  if (count($args)) {
    return array_shift($args);
  }
  else {
    return NULL;
  }
}

/**
 * Wraps the given text with a HTML tag
 * @param $value
 * @param $tag
 * @return string
 */
function tfd_wrap_text($value, $tag) {
  $value = tfd_render($value);
  if (!empty($value)) {
    return sprintf('<%s>%s</%s>', $tag, trim($value), $tag);
  }
}

function tfd_dump($env, $var = NULL, $function = NULL) {
  static $functions = array(
    'dpr' => NULL,
    'dpm' => NULL,
    'kpr' => NULL,
    'print_r' => 'p',
    'var_dump' => 'v'
  );
  if (empty($function)) {
    $functionCalls = array_keys($functions);
    if (!module_exists('devel')) {
      $function = end($functionCalls);
    }
    else {
      $function = reset($functionCalls);
    }
  }
  if (array_key_exists($function, $functions) && is_callable($function)) {
    call_user_func($function, $var);
  }
  else {
    $found = FALSE;
    foreach ($functions as $name => $alias) {
      if (in_array($function, (array) $alias)) {
        $found = TRUE;
        call_user_func($name, $var);
        break;
      }
    }
    if (!$found) {
      throw new InvalidArgumentException("Invalid mode '$function' for TFD_dump()");
    }
  }
}


function tfd_image_url($filepath, $preset = NULL) {
  if (is_array($filepath)) {
    $filepath = $filepath['filepath'];
  }

  if ($preset) {
    return image_style_url($preset, $filepath);
  }
  else {
    return $filepath;
  }
}


function tfd_image_size($filepath, $preset, $asHtml = TRUE) {
  if (is_array($filepath)) {
    $filepath = $filepath['filepath'];
  }
  $info = image_get_info(image_style_url($preset, $filepath));
  $attr = array(
    'width' => (string) $info['width'],
    'height' => (string) $info['height']
  );
  if ($asHtml) {
    return drupal_attributes($attr);
  }
  else {
    return $attr;
  }
}


function tfd_url($item, $options = array()) {
  if (is_numeric($item)) {
    $ret = url('node/' . $item, (array) $options);
  }
  else {
    $ret = url($item, (array) $options);
  }
  return check_url($ret);
}


function tfd_test_property($element, $propertyName, $value = TRUE) {
  return array_key_exists("#{$propertyName}", $element) && $element["#{$propertyName}"] == $value;
}

function tfd_test_number($element) {
  if (is_scalar($element) && is_numeric($element) && is_integer($element)) {
    return TRUE;
  }
  return FALSE;
}

/**
 *
 * @param $value
 * @param int $length
 * @param bool $elipse
 * @param bool $words
 * @return string
 */
function tfd_truncate_text($value, $length = 300, $elipse = TRUE, $words = TRUE) {
  $value = tfd_render($value);
  if (drupal_strlen($value) > $length) {
    $value = drupal_substr($value, 0, $length);
    if ($words) {
      $regex = "(.*)\b.+";
      if (function_exists('mb_ereg')) {
        mb_regex_encoding('UTF-8');
        $found = mb_ereg($regex, $value, $matches);
      }
      else {
        $found = preg_match("/$regex/us", $value, $matches);
      }
      if ($found) {
        $value = $matches[1];
      }
    }
    // Remove scraps of HTML entities from the end of a strings
    $value = rtrim(preg_replace('/(?:<(?!.+>)|&(?!.+;)).*$/us', '', $value));
    if ($elipse) {
      $value .= ' ' . t('...');
    }
  }
  return $value;
}

/**
 * Convience wrapper around the drupal format_interval method
 * *
 * Instead of receiving the calculated difference in seconds
 * you can just give it a date and it calculates the difference
 * for you.
 *
 * @see format_interval();
 *
 * @param $date  String containing the date, or unix timestamp
 * @param int $granularity
 */
function tfd_interval($date, $granularity = 2, $display_ago = TRUE,$langcode = null) {


  $now = time();
  if (preg_match('/[^\d]/', $date)) {
    $then = strtotime($date);
  }
  else {
    $then = $date;
  }
  $interval = $now - $then;
  if ($interval > 0) {
    return $display_ago ? t('!time ago', array('!time' => format_interval($interval, $granularity,$langcode))) :
      t('!time', array('!time' => format_interval($interval, $granularity,$langcode)));
  }
  else {
    return format_interval(abs($interval), $granularity);
  }
}

function tfd_machine_name($string) {
  return preg_replace(array('/[^a-z0-9]/', '/_+/'), '_', strtolower($string));
}

/**
 * Get a block from the DB
 *
 * @param string $delta
 * @param null $module Optional name of the module this block belongs to.
 * @param boolean $render return the raw data instead of the rendered content.
 * @return bool|string
 */
function tfd_view_block($module = 'block', $delta, $render = TRUE) {
    $blockdata = block_load($module, $delta); // Немного об Антоне
    $output = _block_get_renderable_array(_block_render_blocks(array($blockdata)));
    $output = ($render) ? render($output) : $output;
    return $output;
}

function tfd_user_is_logged_in(){
    return user_is_logged_in();
}

function tfd_base_url(){
    global $base_url;

    return $base_url;
}

function tfd_get_form($form_id){
    $form = drupal_get_form($form_id);
    return render($form);
}