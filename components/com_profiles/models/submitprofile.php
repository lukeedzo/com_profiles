<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use \Joomla\CMS\Factory;

/**
 * Profiles model.
 *
 * @since  1.6
 */
class ProfilesModelSubmitprofile extends \Joomla\CMS\MVC\Model\ItemModel

{
    protected function populateState()
    {
        $app = Factory::getApplication('com_profiles');
        $params = $app->getParams();
        $params_array = $params->toArray();
        $this->setState('params', $params);
    }

    public function getItem($id = null)
    {

    }
}
