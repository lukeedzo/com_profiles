<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */
defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;

jimport('joomla.form.formfield');

/**
 * Supports a value from an external table
 *
 * @since  1.6
 */
class JFormFieldForeignKey extends \Joomla\CMS\Form\FormField

{
    /**
     * The form field type.
     *
     * @var      string
     * @since    1.6
     */
    protected $type = 'foreignkey';

    private $input_type;

    private $table;

    private $key_field;

    private $value_field;

    private $option_key_field;

    private $option_value_field;

    private $condition;

    /**
     * Method to get the field input markup.
     *
     * @return   string  The field input markup.
     *
     * @since    1.6
     */
    protected function getInput()
    {
        // Type of input the field shows
        $this->input_type = $this->getAttribute('input_type');

        // Database Table
        $this->table = $this->getAttribute('table');

        // The field that the field will save on the database
        $this->key_field = (string) $this->getAttribute('key_field');

        // The column that the field shows in the input
        $this->value_field = (string) $this->getAttribute('value_field');

        // The option field that the field will save on the database
        $this->option_key_field = (string) $this->getAttribute('option_key_field');

        // The option value that the field shows in the input
        $this->option_value_field = (string) $this->getAttribute('option_value_field');

        // Flag to identify if the fk_value is multiple
        $this->value_multiple = (int) $this->getAttribute('value_multiple', 0);

        $this->required = (string) $this->getAttribute('required', 0);

        // Flag to identify if the fk_value hides the trashed items
        $this->hideTrashed = (int) $this->getAttribute('hide_trashed', 0);

        // Flag to identify if the fk_value hides the unpublished items
        $this->hideUnpublished = (int) $this->getAttribute('hide_unpublished', 0);

        // Flag to identify if the fk_value hides the published items
        $this->hidePublished = (int) $this->getAttribute('hide_published', 0);

        // Flag to identify if the fk_value hides the archived items
        $this->hideArchived = (int) $this->getAttribute('hide_archived', 0);

        // Flag to identify if the fk has default order
        $this->fk_ordering = (string) $this->getAttribute('fk_ordering');

        // The where SQL for foreignkey
        $this->condition = (string) $this->getAttribute('condition');

        // Initialize variables.
        $html = '';
        $fk_value = '';

        // Load all the field options
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Support for multiple fields on fk_values
        if ($this->value_multiple == 1) {
            // Get the fields for multiple value
            $this->value_fields = (string) $this->getAttribute('value_field_multiple');
            $this->value_fields = explode(',', $this->value_fields);
            $this->separator = (string) $this->getAttribute('separator');

            $fk_value = ' CONCAT(';

            foreach ($this->value_fields as $field) {
                $fk_value .= $db->quoteName($field) . ', \'' . $this->separator . '\', ';
            }

            $fk_value = substr($fk_value, 0, -(strlen($this->separator) + 6));
            $fk_value .= ') AS ' . $db->quoteName($this->value_field);
        } else {
            $fk_value = $db->quoteName($this->value_field);
        }

        $query
            ->select(
                array(
                    $db->quoteName($this->key_field),
                    $fk_value,
                )
            )
            ->from($this->table);

        if ($this->hideTrashed) {
            $query->where($db->quoteName('state') . ' != -2');
        }

        if ($this->hideUnpublished) {
            $query->where($db->quoteName('state') . ' != 0');
        }

        if ($this->hidePublished) {
            $query->where($db->quoteName('state') . ' != 1');
        }

        if ($this->hideArchived) {
            $query->where($db->quoteName('state') . ' != 2');
        }

        if ($this->fk_ordering) {
            $query->order($this->fk_ordering);
        }

        if ($this->condition) {
            $query->where($this->condition);
        }

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $input_options = 'class="' . $this->getAttribute('class') . '"';

        if ($this->required === "true") {
            $input_options .= 'required="required"';
        }

        // Depends of the type of input, the field will show a type or another
        switch ($this->input_type) {
            case 'list':
            default:
                $options = array();

                if (!empty($this->option_value_field) || !empty($this->option_key_field)) {
                    $options[] = HTMLHelper::_('select.option', $this->option_key_field, Text::_($this->option_value_field));
                }

                // Iterate through all the results
                foreach ($results as $result) {
                    $options[] = HTMLHelper::_('select.option', $result->{$this->key_field}, Text::_($result->{$this->value_field}));
                }

                $value = $this->value;

                // If the value is a string -> Only one result
                if (is_string($value)) {
                    $value = array($value);
                } elseif (is_object($value)) {
                    // If the value is an object, let's get its properties.
                    $value = get_object_vars($value);
                }

                // If the select is multiple
                if ($this->multiple) {
                    $input_options .= 'multiple="multiple"';
                } else {
                    array_unshift($options, HTMLHelper::_('select.option', '', ''));
                }

                $html = HTMLHelper::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value, $this->id);
                break;
        }

        return $html;
    }

    /**
     * Wrapper method for getting attributes from the form element
     *
     * @param   string  $attr_name  Attribute name
     * @param   mixed   $default    Optional value to return if attribute not found
     *
     * @return mixed The value of the attribute if it exists, null otherwise
     */
    public function getAttribute($attr_name, $default = null)
    {
        if (!empty($this->element[$attr_name])) {
            return $this->element[$attr_name];
        } else {
            return $default;
        }
    }
}
