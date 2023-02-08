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

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;

/**
 * Class ProfilesRouter
 *
 */
class ProfilesRouter extends RouterView
{
    private $noIDs;
    public function __construct($app = null, $menu = null)
    {
        $params = JComponentHelper::getComponent('com_profiles')->params;
        $this->noIDs = (bool) $params->get('sef_ids');

        $profiles = new RouterViewConfiguration('profiles');
        $this->registerView($profiles);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));

        if ($params->get('sef_advanced', 0)) {
            $this->attachRule(new StandardRules($this));
            $this->attachRule(new NomenuRules($this));
        } else {
            JLoader::register('ProfilesRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
            JLoader::register('ProfilesHelpersProfiles', __DIR__ . '/helpers/profiles.php');
            $this->attachRule(new ProfilesRulesLegacy($this));
        }
    }

}
