<?php
defined('_JEXEC') or die;

// Load the com_stub language files, default to the admin file and fall back to site if one isn't found
$lang = JFactory::getLanguage();
$lang->load('com_stub', JPATH_ADMINISTRATOR, null, false, true)
||	$lang->load('com_stub', JPATH_SITE, null, false, true);

// Hand processing over to the admin base file
require_once JPATH_COMPONENT_ADMINISTRATOR . '/com_stub.php';
