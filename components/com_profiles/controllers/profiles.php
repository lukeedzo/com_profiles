<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

// No direct access.
defined('_JEXEC') or die;
JLoader::register('ProfilesHelpersProfiles', JPATH_COMPONENT . './helpers/profiles.php');
/**
 * Profiles list controller class.
 *
 * @since  1.6
 */
class ProfilesControllerProfiles extends ProfilesController
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
    public function &getModel($name = 'Profiles', $prefix = 'ProfilesModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => false));
        $items = $model->getItems();

        return $items;
    }

    public function getItems()
    {
        $helper = new ProfilesHelpersProfiles();
        $data = $helper->decode($this->getModel());

        $items = [];
        foreach ($data as $item) {
            $decodedItem = [
                'name' => $item['name'],
                'e_mail' => $item['e_mail'],
                'degree' => $item['degree'],
                'letter' => substr($item['name'], 0, 1),
                'positions' => $helper->decodePositions($item['positions']),
                'publication_list' => $helper->decodePublications($item['publication_list']),
                'external_profiles' => $helper->decodeProfiles($item['external_profiles']),
            ];
            $items[] = (object) $decodedItem;
        }

        $grouped = $helper->grouping($items);

        return $grouped;
    }

}
