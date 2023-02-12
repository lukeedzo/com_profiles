<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldTimecreated extends \Joomla\CMS\Form\FormField

{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'timecreated';

    /**
     * Method to get the field input markup.
     *
     * @return   string  The field input markup.
     *
     * @since    1.6
     */
    protected function getInput()
    {
        // Initialize variables.
        $html = array();

        $time_created = $this->value;

        if (!strtotime($time_created)) {
            $time_created = Factory::getDate()->toSql();
            $html[] = '<input type="hidden" name="' . $this->name . '" value="' . $time_created . '" />';
        }

        $hidden = (boolean) $this->element['hidden'];

        if ($hidden == null || !$hidden) {
            $jdate = new JDate($time_created);
            $pretty_date = $jdate->format(Text::_('DATE_FORMAT_LC2'));
            $html[] = "<div>" . $pretty_date . "</div>";
        }

        return implode($html);
    }
}
