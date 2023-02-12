<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Profiles
 * @author     Lukas Plycneris <lukasplycneris@protonmail.com>
 * @copyright  2023 Lukas Plycneris
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

/**
 * Methods supporting a list of Profiles records.
 *
 * @since  1.6
 */
class ProfilesModelProfiles extends \Joomla\CMS\MVC\Model\ListModel

{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see        JController
     * @since      1.6
     */
    public function __construct(array $config = [])
    {
        $defaultFilterFields = [
            'id', 'a.id',
            'state', 'a.state',
            'ordering', 'a.ordering',
            'created_by', 'a.created_by',
            'modified_by', 'a.modified_by',
            'name', 'a.name',
            'degree', 'a.degree',
            'positions', 'a.positions',
            'e_mail', 'a.e_mail',
            'publication_list', 'a.publication_list',
            'external_profiles', 'a.external_profiles',
        ];

        $config['filter_fields'] = $config['filter_fields'] ?? $defaultFilterFields;

        parent::__construct($config);
    }
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   Elements order
     * @param   string  $direction  Order direction
     *
     * @return void
     *
     * @throws Exception
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();

        $list = $app->getUserState($this->context . '.list');

        $ordering = isset($list['filter_order']) ? $list['filter_order'] : null;
        $direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;

        if (empty($ordering)) {
            $ordering = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', $app->get('filter_order'));
            if (!in_array($ordering, $this->filter_fields)) {
                $ordering = 'name';
            }
            $this->setState('list.ordering', $ordering);
        }
        if (empty($direction)) {
            $direction = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', $app->get('filter_order_Dir'));
            if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
                $direction = 'ASC';
            }
            $this->setState('list.direction', $direction);
        }

        $list['limit'] = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'), 'uint');
        $list['start'] = $app->input->getInt('start', 0);
        $list['ordering'] = $ordering;
        $list['direction'] = $direction;

        $app->setUserState($this->context . '.list', $list);
        $app->input->set('list', null);

        parent::populateState($ordering, $direction);

        $searchContext = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $searchContext);

        $params = $app->getParams();
        $this->setState('params', $params);

        if (!empty($searchContext)) {
            $parts = FieldsHelper::extract($searchContext);

            if ($parts) {
                $this->setState('filter.component', $parts[0]);
                $this->setState('filter.section', $parts[1]);
            }
        }
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return   JDatabaseQuery
     *
     * @since    1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select($this->getState('list.select', 'DISTINCT a.*'));
        $query->from('#__profiles_list AS a');
        $query->select('uc.name AS uEditor')
            ->join('LEFT', '#__users AS uc ON uc.id = a.checked_out')
            ->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by')
            ->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');

        if (!Factory::getUser()->authorise('core.edit', 'com_profiles')) {
            $query->where('a.state = 1');
        } else {
            $query->where('a.state IN (0, 1)');
        }

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where('a.name LIKE ' . $search);
            }
        }

        $orderCol = $this->state->get('list.ordering', 'name');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }
    /**
     * Method to get an array of data items
     *
     * @return  mixed An array of data on success, false on failure.
     */
    public function getItems()
    {
        $items = parent::getItems();

        return $items;
    }

    /**
     * Overrides the default function to check Date fields format, identified by
     * "_dateformat" suffix, and erases the field if it's not correct.
     *
     * @return void
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $filters = $app->getUserState($this->context . '.filter', array());
        $error_dateformat = false;

        foreach ($filters as $key => $value) {
            if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null) {
                $filters[$key] = '';
                $error_dateformat = true;
            }
        }

        if ($error_dateformat) {
            $app->enqueueMessage(Text::_("COM_PROFILES_SEARCH_FILTER_DATE_FORMAT"), "warning");
            $app->setUserState($this->context . '.filter', $filters);
        }

        return parent::loadFormData();
    }

    /**
     * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
     *
     * @param   string  $date  Date to be checked
     *
     * @return bool
     */
    private function isValidDate($date)
    {
        $date = str_replace('/', '-', $date);
        return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
    }
}
