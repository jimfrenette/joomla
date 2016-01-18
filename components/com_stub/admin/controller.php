<?php
/**
 * @package     Stub
 * @subpackage  com_stub
 *
 */

defined('_JEXEC') or die;

/**
 * Stub Component Controller
 */
class StubController extends JControllerLegacy
{

	public function display($cachable = false, $urlparams = false)
	{
		$vName = $this->input->get('view', 'someview');

		switch ($vName)
		{
			case 'someview':
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName   = 'somemodel'; //model name

				break;

			case 'tbd':
			default:
				$vName   = 'tbd';
				$vLayout = $this->input->get('layout', 'default', 'string');
				$mName   = 'tbd';

				break;
		}
		
		$document = JFactory::getDocument();
		$vType    = $document->getType();

		// Get/Create the view
		$view = $this->getView($vName, $vType);
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR . '/views/' . strtolower($vName) . '/tmpl');

		// Get/Create the model
		if ($model = $this->getModel($mName))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();

		return $this;
	}
}