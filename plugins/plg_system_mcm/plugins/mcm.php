<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport('joomla.plugin.plugin');
 
class plgSystemMyComMedia extends JPlugin { 
    /**
     * Load the language file on instantiation.
     * Note this is only available in Joomla 3.1 and higher.
     * If you want to support 3.0 series you must override the constructor
     *
     * @var boolean
     * @since 3.1
     */
    protected $autoloadLanguage = true;

    public function onAfterInitialise() {

        $input = JFactory::getApplication()->input;

        if('com_media' == $input->getCMD('option')) {

            $this->loadView($input->getCMD('view'));

        }

        return true;
    }

    protected function loadView($view)
    {
        if (('images' == $view) || ('imageslist' == $view)) {

            $overridePath = FOFPlatform::getInstance()->getTemplateOverridePath('com_media', true) . '/' . $view;

            require_once $overridePath . '/view.html.php';
        }
    }

}
?>
