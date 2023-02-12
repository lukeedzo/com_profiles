<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

use \Joomla\CMS\Language\Text;

/**
 * Class JFormFieldSubmit
 *
 * @since  1.6
 */
class JFormFieldSubmit extends \Joomla\CMS\Form\FormField

{
    protected $type = 'submit';

    protected $value;

    protected $for;

    /**
     * Get a form field markup for the input
     *
     * @return string
     */
    public function getInput()
    {
        $this->value = $this->getAttribute('value');

        return '<button id="' . $this->id . '"'
        . ' name="submit_' . $this->for . '"'
        . ' value="' . $this->value . '"'
        . ' title="' . Text::_('JSEARCH_FILTER_SUBMIT') . '"'
        . ' class="btn" style="margin-top: -10px;">'
        . Text::_('JSEARCH_FILTER_SUBMIT')
            . ' </button>';
    }
}
