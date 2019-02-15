<?php
namespace App\Classes;

  // TODO: set default field values

class AppOption
{

  /**
   * Change this name to override the title of the theme for vanity purposes.
   */
  public $themeDisplayName = 'Theme';

  /**
   * proxy for easy access to variables.
   * @var Object
   */
  protected $options;

  /**
   * default inline style for text fields
   * @var string
   */
  protected $fullWidthStyle = 'style="width: 100%; max-width:700px"';

  /**
   * AppOption constructor.
   */
  function __construct()
  {
    add_action('admin_menu', [$this, 'createAdminMenu']);
    add_action('admin_init', [$this, 'initSettings']);
    $this->options = get_option('vm_app_options');

  }

  /**
   * Creates the sections and fields based on structure of array in return statement.
   * each top level item will be a section. Each field beneath it will be the key of the setting in json format.
   *
   * field requires the following values
   *  'type'=> (text | textarea | wysiwyg | select | checkbox | radio)
   *  'description' => the string value the user will see labeling the field
   *  **NOTE** type with value of radio or select requires array 'options' => [assoc | numeric]
   * @return array
   */
  function getFields()
  {
    return [
      'global' => [
        'footerText' => ['type' => 'textarea', 'description' => 'Footer Text'],
        'copyrightText' => ['type' => 'textarea', 'description' => 'Copyright Text'],
      ],

      'social' => [
        'facebook' => ['type' => 'text', 'description' => 'Facebook URL'],
        'twitter' => ['type' => 'text', 'description' => 'Twitter URL'],
        'linkedin' => ['type' => 'text', 'description' => 'LinkedIn URL'],
      ],

      'analytics' => [
        'gtm_container_id' => ['type' => 'text', 'description' => 'GTM Analytics ID'],
      ],
    ];
  }

  /**
   * determines what render method to use for admin page depending on metadata
   *
   * @param Object $field - the field object with metadata.
   */
  function beforeRenderField($field)
  {
    $field = $field;
    switch ($field['meta']['type']) {
      case 'string':
      case 'text':
        $this->renderTextField($field);
        break;

      case 'checkbox':
        $this->renderCheckboxField($field);
        break;

      case 'radio':
        $this->renderRadioField($field);
        break;

      case 'textarea':
        $this->renderTextAreaField($field);
        break;

      case 'wysiwyg':
        $this->renderWYSIWYGField($field);
        break;

      case 'select':
        $this->renderSelectField($field);
        break;

      case 'button';
      $this->renderButton($field);
    default:
      return;
  }
  }

/**
 * renders <input type="text">
 *
 * @param Object $field - the full field with metadata
 */
function renderTextField($field)
{
  $fieldKey = $field['name'];
  echo sprintf(
    '<input type="text" name="%s" value="%s" %s>',
    'vm_app_options[' . $fieldKey . ']',
    $this->options[$fieldKey],
    $this->fullWidthStyle
  );
}

/**
 * renders <input type="checkbox">
 *
 * @param Object $field - the full field with metadata
 */
function renderCheckboxField($field)
{
  $fieldKey = $field['name'];
  echo sprintf(
    '<input type="checkbox" name="%s" %s>',
    'vm_app_options[' . $fieldKey . ']',
    ($this->options[$fieldKey] === 'on') ? 'checked' : null
  );

}

/**
 * renders <input type="radio">
 *
 * @param Object $field - the full field with metadata
 */
function renderRadioField($field)
{
      // TODO: This isn't working yet. Pretty close, but idk how to save this in the database rn and I need to keep moving.
  echo "Sorry, this input type isn't supported yet. See AppOption.php:130";

  return;
  $fieldKey = $field['name'];
  foreach ($field['meta']['options'] as $radio) {
    echo sprintf(
      '<input type="radio" name="%s" value="%s">%s<br>',
      'vm_app_options[' . $fieldKey . ']',
      $radio,
      $radio
    );
  }

}

/**
 * renders <textarea>
 *
 * @param Object $field - the full field with metadata
 */
function renderTextAreaField($field)
{
  $fieldKey = $field['name'];
  echo sprintf(
    '
        <textarea cols="80" rows="10" name="%s" >%s</textarea>',
    'vm_app_options[' . $fieldKey . ']',
    $this->options[$fieldKey]
  );

}

/**
 * renders tinyMCE input field
 *
 * @param Object $field - the full field with metadata
 */
function renderWYSIWYGField($field)
{
  $fieldKey = $field['name'];
  $content = $this->options[$fieldKey];
  $id = 'vm_app_options[' . $fieldKey . ']';

  wp_editor($content, $fieldKey, [
    'textarea_name' => $id,
  ]);

}

/**
 * renders <select>
 *
 * @param Object $field - the full field with metadata
 */
function renderSelectField($field)
{
  $fieldKey = $field['name'];
  $options = $field['meta']['options'];
  $selectedValue = $this->options[$fieldKey];

  echo '<select name="vm_app_options[' . $fieldKey . ']">';

  if (self::isAssoc($options)) {
    foreach ($options as $name => $value) {
      $selected = ($selectedValue === $value) ? 'selected' : null;
      echo '<option value="' . $value . '" ' . $selected . '>' . $name . '</option>';
    }
  } else if (is_array($options)) {
    foreach ($options as $option) {
      $selected = ($selectedValue === $option) ? 'selected' : null;
      echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
    }
  }

  echo '</select>';
}

/**
 * renders <button> with ID of field's_key name. Create an ajax function in Javascript for the action.
 *
 * @param Object $field - the full field with metadata
 */
function renderButton($field)
{
  $text = (isset($field['meta']['text'])) ? $field['meta']['text'] : 'Go';
  $callback = (isset($field['meta']['callback'])) ? $field['meta']['callback'] : false;

  echo '<button class="button button-primary" id="' . $field['name'] . '" data-action="' . $callback . '">' . $text . '</button>';
}

/**
 * determines if an array is numeric of associative
 *
 * @param Array $arr
 *
 * @return bool
 */
static function isAssoc($arr)
{
  if ([] === $arr) {
    return false;
  }

  return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * registers the menu in the wordpress architecture.
 */
function createAdminMenu()
{

  add_options_page(
    $this->themeDisplayName . ' Settings',    // <title> name
    $this->themeDisplayName . ' Settings',    // Menu Name
    'manage_options',                         // Permission required
    'theme-options',                          // page-slug
    [$this, 'renderAdminPage']              // render callback
  );

}

/**
 * registers settings with wordpress and creates database object in wp_options
 */
function initSettings()
{
  register_setting('pluginPage', 'vm_app_options');        // the JSON object in database.
  $sections = $this->getFields();

  foreach ($sections as $section => $values) {
    $this->createNewSection($section);
    foreach ($values as $field => $fieldmeta) {
      $this->createNewFieldForSection($section, $field, $fieldmeta);
    }
  }
}

/**
 * responsible for creating a new section on the settings page
 *
 * @param string $section - the name of the section
 */
function createNewSection($section)
{
  add_settings_section(
    "vm_app_options_{$section}_section",   // id attribute
    ucwords($section),                    // section Title
    [$this, 'renderSection'],              // render callback
    'pluginPage'                          // page (do not modify)
  );
}

/**
 * creates a new field in the admin area and serialized field in the db.
 *
 * @param String $section - The String Name of the Section
 * @param String $field - The String name of the field
 * @param Object $fieldmeta - The object containing metadata about the field
 */
function createNewFieldForSection($section, $field, $fieldmeta)
{

  add_settings_field(
    "vm_app_options_{$field}",                            // field ID
    $fieldmeta['description'],                          // field label
    [$this, 'beforeRenderField'],                       // render function
    'pluginPage',                                         // page
    "vm_app_options_{$section}_section",                  // section id,
    ['name' => $field, 'meta' => $fieldmeta]
  );
}

/**
 * responsible for building the admin page for the AppOptions under the slug registered in createAdminMenu
 *
 */
function renderAdminPage()
{
  echo '<form action="options.php" method="post">
     <h1>' . $this->themeDisplayName . ' Settings</h1>';
  settings_fields('pluginPage');
  do_settings_sections('pluginPage');
  submit_button();
  echo '</form>';
}

/**
 * required, but not doing anything right now.
 *
 * @param $args
 */
function renderSection($args)
{
  echo '';
}

public static function getInstance()
{
  return get_option('vm_app_options');
}
}
