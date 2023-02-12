<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Profiles', JPATH_COMPONENT);
JLoader::register('ProfilesController', JPATH_COMPONENT . '/controller.php');

// Execute the task.
$controller = BaseController::getInstance('Profiles');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
