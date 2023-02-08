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
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_profiles') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'profileform.xml');
$canEdit = $user->authorise('core.edit', 'com_profiles') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'profileform.xml');
$canCheckin = $user->authorise('core.manage', 'com_profiles');
$canChange = $user->authorise('core.edit.state', 'com_profiles');
$canDelete = $user->authorise('core.delete', 'com_profiles');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_profiles/css/list.css');
?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">


        <div class="table-responsive">
	<table class="table table-striped" id="profileList">
		<thead>
		<tr>

							<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_E_MAIL', 'a.e_mail', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_DEGREE', 'a.degree', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_POSITIONS', 'a.positions', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_PUBLICATION_LIST', 'a.publication_list', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort', 'COM_PROFILES_PROFILES_EXTERNAL_PROFILES', 'a.external_profiles', $listDirn, $listOrder); ?>
				</th>


				<?php if ($canEdit || $canDelete): ?>
				<th class="center">
					<?php echo JText::_('COM_PROFILES_PROFILES_ACTIONS'); ?>
				</th>
				<?php endif;?>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item): ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_profiles');?>


			<tr class="row<?php echo $i % 2; ?>">


				<td>

					<?php echo $item->name; ?>
				</td>
				<td>

					<?php echo $item->e_mail; ?>
				</td>
				<td>

					<?php echo $item->degree; ?>
				</td>
				<td>
								<?php if (!empty($item->positions)):
    $subform_elements = json_decode($item->positions);
    foreach ($subform_elements as $element) {
        foreach ($element as $key => $value) {
            echo '</br>';
            if (is_array($value)) {
                echo $key . ':&nbsp';
                foreach ($value as $key => $val) {
                    echo '&nbsp' . $val . '&nbsp&nbsp&nbsp';
                }
            } else {
                echo $key . ':&nbsp' . $value . '&nbsp&nbsp&nbsp';
            }
        }
    }
endif;?>
				</td>
				<td>
								<?php if (!empty($item->publication_list)):
    $subform_elements = json_decode($item->publication_list);
    foreach ($subform_elements as $element) {
        foreach ($element as $key => $value) {
            echo '</br>';
            if (is_array($value)) {
                echo $key . ':&nbsp';
                foreach ($value as $key => $val) {
                    echo '&nbsp' . $val . '&nbsp&nbsp&nbsp';
                }
            } else {
                echo $key . ':&nbsp' . $value . '&nbsp&nbsp&nbsp';
            }
        }
    }
endif;?>
				</td>
				<td>
								<?php if (!empty($item->external_profiles)):
    $subform_elements = json_decode($item->external_profiles);
    foreach ($subform_elements as $element) {
        foreach ($element as $key => $value) {
            echo '</br>';
            if (is_array($value)) {
                echo $key . ':&nbsp';
                foreach ($value as $key => $val) {
                    echo '&nbsp' . $val . '&nbsp&nbsp&nbsp';
                }
            } else {
                echo $key . ':&nbsp' . $value . '&nbsp&nbsp&nbsp';
            }
        }
    }
endif;?>
				</td>

				<?php if ($canEdit || $canDelete): ?>
					<td class="center">
					</td>
				<?php endif;?>

			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
        </div>
	<?php if ($canCreate): ?>
		<a href="<?php echo Route::_('index.php?option=com_profiles&task=profileform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo Text::_('COM_PROFILES_ADD_ITEM'); ?></a>
	<?php endif;?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php if ($canDelete): ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo Text::_('COM_PROFILES_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif;?>
