<?php
/**
 * @package     copied from Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('formbehavior.chosen', 'select');

// Load tooltip instance without HTML support because we have a HTML tag in the tip
JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));

$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;
$params = JComponentHelper::getParams('com_media');

/* ----------------------------------------------
 * plg_system_mcm
 * created folder = /images/asset/{assetid}
 * this tmpl creates a folder for the current
 * asset if needed and opens that folder in
 * the imageslist
---------------------------------------------- */
$imageBasePath = $params->get('image_path', 'images');
$imageAssetFolder = '/asset/' . $input->getCmd('asset');

if (!JFolder::exists($imageBasePath . $imageAssetFolder)) {
	JFolder::create($imageBasePath . $imageAssetFolder);
}

$this->state->folder = $imageAssetFolder;

JFactory::getDocument()->addScriptDeclaration(
	"
	var image_base_path = '" . $imageBasePath . "/';
	"
);
?>
<link rel="stylesheet" href="/media/com_media_mcm/css/loading.css" />
<style>
.control-inline {
	display: inline-block;
	margin-right: 1em;
}
.drag-area {
	background-color: #efefef;
	border: 3px dashed #cccccc;
	border-radius: 10px;
	text-align: center;
	padding: 30px; }
.drag-area.over {
	border-color: #ff0000;
	background-color: #FFE9EA; }
.imgpopup {
	position: absolute;
	top: 58px;
	left: 50%;
	background-color: #efefef;
	border: 1px solid #cccccc;
	border-radius: 10px;
	margin-left: -180px;
	padding: 15px;
	text-align: center;
	box-shadow: rgba(0,0,0,0.4) 0 2px 5px; }
.imgpopup img {
	display: block;
	width: 300px;
	margin-bottom: 15px;
}
#uploadform.well {
	margin-bottom: 0;
	padding-bottom: 0;
}
[data-role="image-manager"] {
	margin-top: 1em;
}
</style>
<form action="index.php?option=com_media&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author'); ?>" class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
	</div>
	<div class="well">
		<div class="row">
			<div class="pull-right" data-role="image-manager-buttons">
			<?php if ($user->authorise('core.delete', 'com_media')):?>
				<button class="btn btn-small disabled" id="image-delete" type="button" data-target="index.php?option=com_media&amp;task=file.delete&amp;tmpl=index&amp;<?php echo JSession::getFormToken(); ?>=1&amp;folder=<?php echo $this->state->folder; ?>">
					<span class="icon-trash"></span><?php echo JText::_('JACTION_DELETE'); ?>
				</button><?php endif; ?>
				<button class="btn btn-small disabled" id="image-apply" type="button">
					<span class="icon-apply icon-white"></span><?php echo JText::_('PLG_SYSTEM_MCM_APPLY') ?>
				</button>
				<button class="btn btn-small disabled" id="image-insert" type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jInsertFieldValue(document.getElementById('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php else:?>ImageManager.onok();<?php endif;?>window.parent.jModalClose();">
					<span class="icon-apply icon-white"></span><?php echo JText::_('COM_MEDIA_INSERT') ?>
				</button>
			</div>
		</div>
		<!-- Begin: Image Manager -->
		<div class="hide" data-role="image-manager">
			<div class="row">
				<div class="span5">
					<img src="" alt="" width="100%" />
				</div>
				<div class="span7 control-group">
					<div class="control-label control-inline">
						<label class="checkbox"><input type="checkbox" id="intro-image-flag" value="">Intro Image</label>
					</div>
					<div class="control-label control-inline">
						<label class="checkbox"><input type="checkbox" id="gallery-image-flag"value="">Gallery Image</label>
					</div>
				</div>
				<?php if (!$this->state->get('field.id')):?>
				<div class="span7 control-group">
					<div class="control-label">
						<label title="<?php echo JText::_('COM_MEDIA_ALIGN_DESC'); ?>" class="noHtmlTip" for="f_align"><?php echo JText::_('COM_MEDIA_ALIGN') ?></label>
					</div>
					<div class="controls">
						<select size="1" id="f_align">
							<option value="" selected="selected"><?php echo JText::_('COM_MEDIA_NOT_SET') ?></option>
							<option value="left"><?php echo JText::_('JGLOBAL_LEFT') ?></option>
							<option value="center"><?php echo JText::_('JGLOBAL_CENTER') ?></option>
							<option value="right"><?php echo JText::_('JGLOBAL_RIGHT') ?></option>
						</select>
					</div>
				</div>
				<div class="span7 control-group">
					<div class="control-label">
						<label for="f_caption"><?php echo JText::_('COM_MEDIA_CAPTION') ?></label>
					</div>
					<div class="controls">
						<input type="text" id="f_caption" value="" />
					</div>
				</div>
				<div class="span7 control-group">
					<div class="control-label">
						<label title="<?php echo JText::_('COM_MEDIA_CAPTION_CLASS_DESC'); ?>" class="noHtmlTip" for="f_caption_class"><?php echo JText::_('COM_MEDIA_CAPTION_CLASS_LABEL') ?></label>
					</div>
					<div class="controls">
						<input type="text" list="d_caption_class" id="f_caption_class" value="" />
						<datalist id="d_caption_class">
							<option value="text-left"></option>
							<option value="text-center"></option>
							<option value="text-right"></option>
						</datalist>
					</div>
				</div>
				<?php endif;?>
			</div>

			<input type="hidden" id="dirPath" name="dirPath" />
			<input type="hidden" id="f_alt" value="" />
			<input type="hidden" id="f_file" name="f_file" />
			<input type="hidden" id="f_title" value="" />
			<input type="hidden" id="f_url" value="" />
			<input type="hidden" id="tmpl" name="component" />

		</div><!-- End: Image Manager -->
	</div>

	<iframe id="imageframe" name="imageframe" src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>

	<input type="hidden" id="folderlist" name="folderlist" value="<?php echo $imageAssetFolder ?>" />
</form>

<?php if ($user->authorise('core.create', 'com_media')) :
/* ----------------------------------------------
 * plg_system_mcm
 * get params from plugin config
---------------------------------------------- */
$plg_system_mcm = JPluginHelper::getPlugin('system', 'mcm');
$plg_system_mcm_params = new JRegistry($plg_system_mcm->params);
?>

<div id="uploadform" class="well">
	<fieldset id="upload-noflash" class="actions">
		<div class="drag-area">
			<h3>Drag & Drop File Here</h3>
			<h4>Or Select File:</h4>
			<div>
				<input type="file" id="upload-file" name="Filedata[]" />
			</div>
		</div>

		<form action="<?php echo JUri::base(); ?>index.php?option=com_media_mcm&amp;task=upload_base64&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>&amp;view=images" id="uploadForm" class="form-horizontal" name="uploadForm" method="post">
			<div class="imgpopup hide" data-role="base64img-upload">
				<img src="" alt="" id="base64img" width="300" />
				<button class="btn btn-primary" id="upload-submit"><span class="icon-upload icon-white"></span> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></button>
				<button class="btn upload-cancel" type="button"><?php echo JText::_('JCANCEL') ?></button>
			<div>
			<input type="hidden" id="base64str" name="base64str" value="" />
			<input type="hidden" id="base64name" name="base64name" value="" />
		</form>
	</fieldset>
	<?php JFactory::getSession()->set('com_media.return_url', 'index.php?option=com_media&view=images&tmpl=component&fieldid=' . $input->getCmd('fieldid', '') . '&e_name=' . $input->getCmd('e_name') . '&asset=' . $input->getCmd('asset') . '&author=' . $input->getCmd('author')); ?>
</div>
<div class="loading hide">Loading&#8230;</div>

<script type="text/javascript">
// global app namespace
var com_media_mcm = com_media_mcm || {};

com_media_mcm.iresizer = {
	min_width: '<?php echo $plg_system_mcm_params->get('imgminwidth'); ?>',
	min_height: '<?php echo $plg_system_mcm_params->get('imgminheight'); ?>',
	max_width: '<?php echo $plg_system_mcm_params->get('imgmaxwidth'); ?>',
	max_height: '<?php echo $plg_system_mcm_params->get('imgmaxheight'); ?>',
	file_id: 'upload-file'
};
</script>
<script src="/media/com_media_mcm/js/images.js"></script>

<?php endif;
