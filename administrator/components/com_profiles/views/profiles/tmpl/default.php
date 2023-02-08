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


use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'administrator/components/com_profiles/assets/css/profiles.css');
$document->addStyleSheet(Uri::root() . 'media/com_profiles/css/list.css');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_profiles');



if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_profiles&task=profiles.saveOrderAjax&tmpl=component';
    HTMLHelper::_('sortablelist.sortable', 'profileList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<form action="<?php echo Route::_('index.php?option=com_profiles&view=profiles'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="profileList">
				<thead>
				<tr>
					<th width="1%" >
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
					</th>
					
					
					
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_E_MAIL', 'a.e_mail', $listDirn, $listOrder); ?>
					</th>
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_DEGREE', 'a.degree', $listDirn, $listOrder); ?>
					</th>
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_POSITIONS', 'a.positions', $listDirn, $listOrder); ?>
					</th>
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_PUBLICATION_LIST', 'a.publication_list', $listDirn, $listOrder); ?>
					</th>
					<th class='nowrap'>
						<?php echo JHtml::_('searchtools.sort',  'COM_PROFILES_PROFILES_EXTERNAL_PROFILES', 'a.external_profiles', $listDirn, $listOrder); ?>
					</th>
					
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_profiles');
					$canEdit    = $user->authorise('core.edit', 'com_profiles');
					$canCheckin = $user->authorise('core.manage', 'com_profiles');
					$canChange  = $user->authorise('core.edit.state', 'com_profiles');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="text-center">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						
						
						
						<td class="">
							<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'profiles.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_profiles&task=profile.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?>
									</a>
								<?php else : ?>
												<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
						</td>
						<td class="">
							<?php echo $item->e_mail; ?>
						</td>
						<td class="">
							<?php echo $item->degree; ?>
						</td>
						<td class="">
								<?php if (!empty($item->positions)) :
								$subform_elements = json_decode($item->positions);
						foreach($subform_elements as $element)
						{
							foreach($element as $key =>$value)
							{
								echo '</br>'; 
								if(is_array($value))
								{
								echo $key.':&nbsp';
								foreach($value as $key => $val)
								{
									echo '&nbsp'.$val.'&nbsp&nbsp&nbsp';
								}
								}else
								{
								echo $key .':&nbsp'.$value.'&nbsp&nbsp&nbsp';
								}
							}
						} 
								endif; ?>
						</td>
						<td class="">
								<?php if (!empty($item->publication_list)) :
								$subform_elements = json_decode($item->publication_list);
						foreach($subform_elements as $element)
						{
							foreach($element as $key =>$value)
							{
								echo '</br>'; 
								if(is_array($value))
								{
								echo $key.':&nbsp';
								foreach($value as $key => $val)
								{
									echo '&nbsp'.$val.'&nbsp&nbsp&nbsp';
								}
								}else
								{
								echo $key .':&nbsp'.$value.'&nbsp&nbsp&nbsp';
								}
							}
						} 
								endif; ?>
						</td>
						<td class="">
								<?php if (!empty($item->external_profiles)) :
								$subform_elements = json_decode($item->external_profiles);
						foreach($subform_elements as $element)
						{
							foreach($element as $key =>$value)
							{
								echo '</br>'; 
								if(is_array($value))
								{
								echo $key.':&nbsp';
								foreach($value as $key => $val)
								{
									echo '&nbsp'.$val.'&nbsp&nbsp&nbsp';
								}
								}else
								{
								echo $key .':&nbsp'.$value.'&nbsp&nbsp&nbsp';
								}
							}
						} 
								endif; ?>
						</td>
						
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        Joomla.submitform(task);

        return false;
    };
</script>