<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Profile controller class.
 *
 * @since  1.6
 */
class ProfilesControllerProfile extends \Joomla\CMS\MVC\Controller\FormController

{
    /**
     * Constructor
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->view_list = 'profiles';
        parent::__construct();
    }
}
