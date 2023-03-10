<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */
defined('_JEXEC') or die;

JLoader::register('ProfilesHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_profiles' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'profiles.php');

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Class ProfilesFrontendHelper
 *
 * @since  1.6
 */
class ProfilesHelpersProfiles
{
    /**
     * Get an instance of the named model
     *
     * @param   string  $name  Model name
     *
     * @return null|object
     */
    public static function getModel($name)
    {
        $model = null;

        // If the file exists, let's
        if (file_exists(JPATH_SITE . '/components/com_profiles/models/' . strtolower($name) . '.php')) {
            require_once JPATH_SITE . '/components/com_profiles/models/' . strtolower($name) . '.php';
            $model = BaseDatabaseModel::getInstance($name, 'ProfilesModel');
        }

        return $model;
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
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user = Factory::getUser();

        if ($user->authorise('core.edit', 'com_profiles') || (isset($item->created_by) && $user->authorise('core.edit.own', 'com_profiles') && $item->created_by == $user->id)) {
            $permission = true;
        }

        return $permission;
    }

    /**
     * Method transfer array of std objects to array of arrays.
     *
     * @param array  $data data.
     *
     * @return array data
     */
    public function decode($data)
    {
        return json_decode(json_encode($data), true);
    }

    /**
     * Method Grouping items by alphabet
     *
     * @param array   $data data.
     *
     * @return array  groupetd array.
     */
    public function grouping($items)
    {
        $grouped = array();
        foreach ($items as $item) {
            $grouped[$item->letter]['letter'] = $item->letter;

            $grouped[$item->letter]['items'][] = $item;
        }

        return $grouped;

    }

    /**
     * Method decoding Positions fields.
     *
     * @param object   $data fields object.
     *
     * @return object
     */
    public function decodePositions($data)
    {
        $decode = json_decode($data, true);
        $items = array();
        foreach ($decode as $item) {
            $items[] = (object) [
                'position' => $item['position'],
            ];
        }

        return $items;
    }

    /**
     * Method decoding Publications fields.
     *
     * @param object   $data fields object.
     *
     * @return object
     */
    public function decodePublications($data)
    {
        $decode = json_decode($data, true);
        $items = array();
        foreach ($decode as $item) {
            $items[] = (object) [
                'publication' => $item['publication'],
                'publication_url' => $item['publication_url'],
            ];
        }

        return $items;
    }

    /**
     * Method decoding Profiles fields.
     *
     * @param object   $data fields object.
     *
     * @return object
     */
    public function decodeProfiles($data)
    {
        $decode = json_decode($data, true);
        $items = array();
        foreach ($decode as $item) {
            $items[] = (object) [
                'profile' => $item['profile'],
                'profile_url' => $item['profile_url'],
            ];
        }

        return $items;
    }

    /**
     * Adds a `<strong>` tag to the last word of a string
     *
     * @param string $string The input string
     *
     * @return string The input string with the last word surrounded by `<strong>` tags
     */
    public function lastStrong($string)
    {
        $last_word = strrchr($string, ' ');
        $bold_last_word = " <strong>" . trim($last_word) . "</strong>";
        return str_replace($last_word, $bold_last_word, $string);
    }

    /**
     * Adds a `<strong>` tag to the first word of a string
     *
     * @param string $string The input string, separated by commas
     *
     * @return string The input string with the first word surrounded by `<strong>` tags
     */
    public function strongFirst($string)
    {
        $words = explode(" ", $string);
        $words[0] = "<strong>" . $words[0] . "</strong>";
        return implode(" ", $words);
    }

    /**
     * Adds bold tags around all the words before the first comma in a string.
     *
     * @param string $string The input string to modify.
     *
     * @return string The modified string with bold tags added.
     */
    public function boldWordsBeforeComma($string)
    {
        $pos = strpos($string, ",");
        $words_before_comma = substr($string, 0, $pos);
        $words_array = explode(" ", $words_before_comma);
        $bold_words_array = array_map(function ($word) {
            return "<strong>$word</strong>";
        }, $words_array);

        $bold_string = implode(" ", $bold_words_array) . substr($string, $pos);

        return $bold_string;
    }
}
