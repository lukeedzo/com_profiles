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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * Profiles helper.
 *
 * @since  1.6
 */
class ProfilesHelper
{
    private $db;

    /**
     *  Constructor
     *
     */
    public function __construct()
    {
        $this->db = Factory::getDbo();
    }

    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  string
     *
     * @return void
     */
    public static function addSubmenu($vName = '')
    {
        JHtmlSidebar::addEntry(
            Text::_('COM_PROFILES_TITLE_PROFILES'),
            'index.php?option=com_profiles&view=profiles',
            $vName == 'profiles'
        );

    }

    /**
     * Gets the files attached to an item
     *
     * @param   int     $pk     The item's id
     *
     * @param   string  $table  The table's name
     *
     * @param   string  $field  The field's name
     *
     * @return  array  The files
     */
    public static function getFiles($pk, $table, $field)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select($field)
            ->from($table)
            ->where('id = ' . (int) $pk);

        $db->setQuery($query);

        return explode(',', $db->loadResult());
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    JObject
     *
     * @since    1.6
     */
    public static function getActions()
    {
        $user = Factory::getUser();
        $result = new JObject;

        $assetName = 'com_profiles';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete',
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**

     * Extracts the host from a URL.

     * @param string $url The URL to extract the host from.
     * @return string The host extracted from the URL.
     */
    public function getUrlHost($url)
    {
        preg_match("/^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n]+)/im", $url, $matches);
        $host = $matches[1];
        return $host;
    }

    /**

     * Removes tabs from a string.
     *
     * @param string $string The string from which to remove tabs.
     * @return string The cleaned string.
     */
    public function removeStingTabs($string)
    {
        return trim(preg_replace('/\t+/', '', $string));
    }

    /**
     * Method retuerns all existing rows in the database.
     *
     * @param array   $select Select fields.
     * @param string  $table  Database table name.
     *
     * @return array  Data results
     */
    public function getRows(array $select, $table)
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName($select))
            ->from($this->db->quoteName($table));
        $this->db->setQuery($query);
        $this->db->execute();
        return $this->db->loadObjectList();
    }

    /**

     * Encodes an array of positions into a JSON string.
     *
     * @param array $positions The array of positions to be encoded.
     * @return string The JSON encoded string representation of the positions.
     */
    public function positionsEncode(array $positions)
    {
        $count = 0;
        $positions_data = array();
        foreach ($positions as $position) {
            if (!empty($position)) {
                $positions_data["positions$count"] = (object) [
                    "position" => (string) $position,
                ];
            }
            $count++;
        }
        return json_encode($positions_data);
    }

    /**
     * Encode an array of external profiles into a JSON string.
     *
     * @param array $profiles The array of external profiles to encode.
     * @return string The encoded JSON string.
     */
    public function externalProfilesEncode(array $profiles)
    {
        $count = 0;
        $profiles_data = array();
        foreach ($profiles as $profile) {
            if (!empty($profile)) {
                $profiles_data["external_profiles$count"] = (object) [
                    "profile" => $this->getUrlHost($profile),
                    "profile_url" => (string) $profile,
                ];
            }
            $count++;
        }
        return json_encode($profiles_data);
    }

    /**

     * Encodes an array of publication objects into a JSON string.
     *
     * @param array $publications Array of publication objects
     * @return string JSON string of encoded publication objects
     */
    public function publicationsEncode($publications)
    {
        $count = 0;
        $publication_data = array();
        foreach ($publications as $publication) {

            $publication_data["publication_list$count"] = (object) [
                "publication" => $publication->publication,
                "publication_url" => $publication->publication_url,
            ];

            $count++;
        }
        return json_encode($publication_data);
    }

    /**
     *Insert data into the database.

     *@param array $columns An array of column names to be inserted
     *@param array $val An array of values to be inserted
     *@param string $table The name of the table to insert the data into
     *@return void
     */
    public function insert(array $columns, array $val, $table)
    {
        $values = array();
        $query = $this->db->getQuery(true);
        foreach ($val as $value) {
            $values[] = $this->db->quote($value);
        }

        $query->insert($this->db->quoteName($table))
            ->columns($this->db->quoteName($columns))
            ->values(implode(',', $values));
        $this->db->setQuery($query);
        $this->db->execute();
    }

}
