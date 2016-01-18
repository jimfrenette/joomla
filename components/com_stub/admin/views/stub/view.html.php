<?php
/**
 * @package     Stub
 * @subpackage  com_stub
 *
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Stub component
 */
class StubViewImport extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$config = JComponentHelper::getParams('com_stub');
		$lang   = JFactory::getLanguage();

		// Include jQuery
		JHtml::_('jquery.framework');

		parent::display($tpl);
	}
}