<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Submits list controller class.
 *
 * @since  1.6
 */
class ProfilesControllerSubmits extends ProfilesController
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional
     * @param   array   $config  Configuration array for model. Optional
     *
     * @return object    The model
     *
     * @since    1.6
     */
    public function &getModel($name = 'Submits', $prefix = 'ProfilesModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }
}
