<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;

/**
 * Profile controller class.
 *
 * @since  1.6
 */
class ProfilesControllerProfile extends \Joomla\CMS\MVC\Controller\BaseController

{
    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @return void
     *
     * @since    1.6
     *
     * @throws Exception
     */
    public function edit()
    {
        $app = Factory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_profiles.edit.profile.id');
        $editId = $app->input->getInt('id', 0);

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_profiles.edit.profile.id', $editId);

        // Get the model.
        $model = $this->getModel('Profile', 'ProfilesModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId && $previousId !== $editId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(Route::_('index.php?option=com_profiles&view=profileform&layout=edit', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return    void
     *
     * @throws Exception
     * @since    1.6
     */
    public function publish()
    {
        // Initialise variables.
        $app = Factory::getApplication();

        // Checking if the user can remove object
        $user = Factory::getUser();

        if ($user->authorise('core.edit', 'com_profiles') || $user->authorise('core.edit.state', 'com_profiles')) {
            $model = $this->getModel('Profile', 'ProfilesModel');

            // Get the user data.
            $id = $app->input->getInt('id');
            $state = $app->input->getInt('state');

            // Attempt to save the data.
            $return = $model->publish($id, $state);

            // Check for errors.
            if ($return === false) {
                $this->setMessage(Text::sprintf('Save failed: %s', $model->getError()), 'warning');
            }

            // Clear the profile id from the session.
            $app->setUserState('com_profiles.edit.profile.id', null);

            // Flush the data from the session.
            $app->setUserState('com_profiles.edit.profile.data', null);

            // Redirect to the list screen.
            $this->setMessage(Text::_('COM_PROFILES_ITEM_SAVED_SUCCESSFULLY'));
            $menu = Factory::getApplication()->getMenu();
            $item = $menu->getActive();

            if (!$item) {
                // If there isn't any menu item active, redirect to list view
                $this->setRedirect(Route::_('index.php?option=com_profiles&view=profiles', false));
            } else {
                $this->setRedirect(Route::_('index.php?Itemid=' . $item->id, false));
            }
        } else {
            if ($user->guest) {
                throw new \Exception(Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 401);
            } else {
                throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }
        }
    }

    /**
     * Remove data
     *
     * @return void
     *
     * @throws Exception
     */
    public function remove()
    {
        // Initialise variables.
        $app = Factory::getApplication();

        // Checking if the user can remove object
        $user = Factory::getUser();

        if ($user->authorise('core.delete', 'com_profiles')) {
            $model = $this->getModel('Profile', 'ProfilesModel');

            // Get the user data.
            $id = $app->input->getInt('id', 0);

            // Attempt to save the data.
            $return = $model->delete($id);

            // Check for errors.
            if ($return === false) {
                $this->setMessage(Text::sprintf('Delete failed', $model->getError()), 'warning');
            } else {
                // Check in the profile.
                if ($return) {
                    $model->checkin($return);
                }

                $app->setUserState('com_profiles.edit.profile.id', null);
                $app->setUserState('com_profiles.edit.profile.data', null);

                $app->enqueueMessage(Text::_('COM_PROFILES_ITEM_DELETED_SUCCESSFULLY'), 'success');
                $app->redirect(Route::_('index.php?option=com_profiles&view=profiles', false));
            }

            // Redirect to the list screen.
            $menu = Factory::getApplication()->getMenu();
            $item = $menu->getActive();
            $this->setRedirect(Route::_($item->link, false));
        } else {
            if ($user->guest) {
                throw new \Exception(Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 401);
            } else {
                throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }
        }
    }
}
