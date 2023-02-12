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
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_profiles/css/front.css');?>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<div class="profiles">
	<div class="profiles__search">
<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
</div>

<div class="profiles__list">
  <?php foreach ($this->items as $item): ?>
    <h4 class="profiles__letter"><?php echo $item['letter'] ?></h4>
    <?php foreach ($item['items'] as $profile): ?>
      <?php echo '{slider open="false" title="' . $this->helper->lastStrong($profile->name) . ', ' . $profile->degree . '"}'; ?>
			<?php if(!empty($profile->positions)): ?>
			<div class="profiles__positions">
        <?php foreach ($profile->positions as $position): ?>
          <div class="profiles__position">
            <?php echo $this->helper->strongFirst($position->position) ?>
          </div>
        <?php endforeach;?>
        <hr>
      </div>
			<?php endif ?>
      <div class="profiles__profile">
        <div class="profiles__profile-items">
					<?php if(!empty($profile->e_mail)):  ?>
          <div class="profiles__profile-item">
            <strong>E-mail: </strong><a href="mailto: <?php echo $profile->e_mail ?>"><?php echo $profile->e_mail ?></a>
          </div>
					<?php endif ?>
					<?php if(!empty($profile->publication_list)):  ?>
          <div class="profiles__profile-item">
					<?php $i = 0; $len = count($profile->publication_list);?>
            <strong>Publication list:</strong>
            <?php foreach ($profile->publication_list as $publication): ?>
              <a href="<?php echo $publication->publication_url ?>"><?php echo $publication->publication ?></a>
							<?php if($i !== $len - 1): ?>
									<?php echo '/'?>
								<?php endif; ?>
								<?php $i++; ?>
            <?php endforeach;?>
          </div>
					<?php endif; ?>
					<?php if(!empty($profile->external_profiles)):  ?>
					<div class="profiles__profile-item">
            <strong>External profiles:</strong>
						<?php $i = 0; $len = count($profile->external_profiles);?>
            <?php foreach ($profile->external_profiles as $external): ?>
              <a href="<?php echo $external->profile_url ?>"><?php echo $external->profile?></a>
								<?php if($i !== $len - 1): ?>
									<?php echo '/'?>
								<?php endif; ?>
								<?php $i++; ?>
            <?php endforeach;?>
          </div>
					<?php endif; ?>
        </div>
      </div>
      <?php echo '{/sliders}' ?>
    <?php endforeach;?>
  <?php endforeach;?>
</div>

<div class="profiles__pagination">
<?php echo $this->pagination->getPagesLinks(); ?>
</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>

