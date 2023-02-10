<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
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

        $items = array();
        foreach ($data as $item) {
            $items[] = (object) [
                'letter' => mb_substr($item['name'], 0, 1),
                'name' => $item['name'],
                'degree' => $item['degree'],
                'positions' => $helper->decodePositions($item['positions']),
                'e_mail' => $item['e_mail'],
                'publication_list' => $helper->decodePublications($item['publication_list']),
                'external_profiles' => $helper->decodeProfiles($item['external_profiles']),
            ];
        }

        // $decode = $helper->decode($items);
        $grouped = $helper->grouping($items);

        return $grouped;
    }

}
