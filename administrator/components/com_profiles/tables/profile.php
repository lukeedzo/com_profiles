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

use \Joomla\CMS\Access\Access;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Table\Table as Table;

/**
 * profile Table class
 *
 * @since  1.6
 */
class ProfilesTableprofile extends Table
{

    /**
     * Constructor
     *
     * @param   JDatabase  &$db  A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__profiles_list', 'id', $db);

        $this->setColumnAlias('published', 'state');

    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  Optional array or list of parameters to ignore
     *
     * @return  null|string  null is operation was satisfactory, otherwise returns an error
     *
     * @see     JTable:bind
     * @since   1.5
     * @throws Exception
     */
    public function bind($array, $ignore = '')
    {
        $date = JFactory::getDate();
        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');
        $user = JFactory::getUser();

        if ($array['id'] == 0 && empty($array['created_by'])) {
            $array['created_by'] = $user->id;
        }

        if ($array['id'] == 0 && empty($array['modified_by'])) {
            $array['modified_by'] = $user->id;
        }

        if ($task == 'apply' || $task == 'save') {
            $array['modified_by'] = $user->id;
        }

        if (isset($array['params']) && is_array($array['params'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new JRegistry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }

        if (!$user->authorise('core.admin', 'com_profiles.profile.' . $array['id'])) {
            $actions = Access::getActionsFromFile(
                JPATH_ADMINISTRATOR . '/components/com_profiles/access.xml',
                "/access/section[@name='profile']/"
            );
            $default_actions = Access::getAssetRules('com_profiles.profile.' . $array['id'])->getData();
            $array_jaccess = array();

            foreach ($actions as $action) {
                if (key_exists($action->name, $default_actions)) {
                    $array_jaccess[$action->name] = $default_actions[$action->name];
                }
            }

            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }

        if (isset($array['rules']) && is_array($array['rules'])) {
            $this->setRules($array['rules']);
        }
        return parent::bind($array, $ignore);
    }

    /**
     * This function convert an array of JAccessRule objects into an rules array.
     *
     * @param   array  $jaccessrules  An array of JAccessRule objects.
     *
     * @return  array
     */
    private function JAccessRulestoArray($jaccessrules)
    {
        $rules = array();

        foreach ($jaccessrules as $action => $jaccess) {
            $actions = array();

            if ($jaccess) {
                foreach ($jaccess->getData() as $group => $allow) {
                    $actions[$group] = ((bool) $allow);
                }
            }

            $rules[$action] = $actions;
        }

        return $rules;
    }

    /**
     * Overloaded check function
     *
     * @return bool
     */
    public function check()
    {
        // If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0) {
            $this->ordering = self::getNextOrder();
        }

        // Support for subform field positions
        if (is_array($this->positions)) {
            $this->positions = json_encode($this->positions, JSON_UNESCAPED_UNICODE);
        }

        // Support for subform field publication_list
        if (is_array($this->publication_list)) {
            $this->publication_list = json_encode($this->publication_list, JSON_UNESCAPED_UNICODE);
        }

        // Support for subform field external_profiles
        if (is_array($this->external_profiles)) {
            $this->external_profiles = json_encode($this->external_profiles, JSON_UNESCAPED_UNICODE);
        }

        return parent::check();
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     *
     * @return string The asset name
     *
     * @see Table::_getAssetName
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_profiles.profile.' . (int) $this->$k;
    }

    /**
     * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @param   JTable   $table  Table name
     * @param   integer  $id     Id
     *
     * @see Table::_getAssetParentId
     *
     * @return mixed The id on success, false on failure.
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = Table::getInstance('Asset');

        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();

        // The item has the component as asset-parent
        $assetParent->loadByName('com_profiles');

        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }

        return $assetParentId;
    }

    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return bool
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);

        return $result;
    }

}
