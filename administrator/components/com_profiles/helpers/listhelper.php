<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Language\Text;
use \Joomla\Utilities\ArrayHelper;

/**
 * Profiles Listhelper.
 *
 * @since  1.6
 */
abstract class JHtmlListhelper
{
    public static function toggle($value = 0, $view, $field, $i)
    {
        $states = array(
            0 => array('icon-remove', Text::_('Toggle'), 'inactive btn-danger'),
            1 => array('icon-checkmark', Text::_('Toggle'), 'active btn-success'),
        );

        $state = ArrayHelper::getValue($states, (int) $value, $states[0]);
        $text = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
        $html = '<a href="#" class="btn btn-micro ' . $state[2] . '"';
        $html .= 'onclick="return toggleField(\'cb' . $i . '\',\'' . $view . '.toggle\',\'' . $field . '\')" title="' . Text::_($state[1]) . '">' . $text . '</a>';

        return $html;
    }
}
