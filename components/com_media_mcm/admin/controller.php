<?php
/**
 * MediaMcm Controller
 * Depends on MediaController
 *
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

require_once JPATH_ADMINISTRATOR . '/components/com_media/controller.php';

class MediaMcmController extends MediaController
{
	public function upload_base64()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$params = JComponentHelper::getParams('com_media');

		// Get data from the request
		$data         = $this->input->get('base64str', null, null);
		$name         = $this->input->get('base64name', null, 'STRING');
		$return       = JFactory::getSession()->get('com_media.return_url');
		$this->folder = $this->input->get('folder', '', 'path');

		// Don't redirect to an external URL.
		if (!JUri::isInternal($return))
		{
			$return = '';
		}

		// Set the redirect
		if ($return)
		{
			$this->setRedirect($return . '&folder=' . $this->folder);
		}
		else
		{
			$this->setRedirect('index.php?option=com_media&folder=' . $this->folder);
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			JError::raiseWarning(100, JText::_('COM_MEDIA_MCM_ERROR_WARNUPLOADTOOLARGE'));

			return false;
		}

		$file = [];
		$file['content'] = $this->decode_base64($data);

		// validate the decoded base64 string
		if (!$this->validate_base64($file['content'], 'image/jpeg'))
		{
			// invalid base64 'image/jpeg'
			JError::raiseWarning(100, JText::_('COM_MEDIA_MCM_INVALID_REQUEST'));

			return false;
		}

		// Perform basic checks on file info before attempting anything
		$file['name']     = JFile::makeSafe($name);
		$file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_MCM_BASE, $this->folder, $file['name'])));
		$file['size']     = strlen($file['content']);

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		if (($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
			|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
		{
			// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
			JError::raiseWarning(100, JText::_('COM_MEDIA_ERROR_WARNFILETOOLARGE'));

			return false;
		}

		if (JFile::exists($file['filepath']))
		{
			// A file with this name already exists
			JError::raiseWarning(100, JText::_('COM_MEDIA_MCM_ERROR_FILE_EXISTS'));

			return false;
		}

		if (!isset($file['name']))
		{
			// No filename (after the name was cleaned by JFile::makeSafe)
			$this->setRedirect('index.php', JText::_('COM_MEDIA_MCM_INVALID_REQUEST'), 'error');

			return false;
		}

		$this->uploadFile($file);

	    return true;
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param   string  $action  - the action to be peformed (create or delete)
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_media'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));

			return false;
		}

		return true;
	}

	/**
	 * Task method
	 *
	 * @param   string  $data 	jinput parameter
	 *
	 */
	protected function decode_base64($data) {

		$data = explode(',', $data, 2);

		$decoded = base64_decode($data[1]);

		return $decoded;
	}

	/**
	 * Task method
	 *
	 * @param   string  $data 	decoded(base64)
	 *
	 */
	protected function validate_base64($data, $mimetype) {

		if ('image/jpeg' == $mimetype)
		{
		    $img = imagecreatefromstring($data);
		    if (!$img) {
		        return false;
		    }

		    imagejpeg($img, 'tmp.jpg');
		    $info = getimagesize('tmp.jpg');

		    unlink('tmp.jpg');

		    if ($info[0] > 0 && $info[1] > 0 && $info['mime']) {
		        return true;
		    }

		    return false;
		}
	}

	/**
	 * Task method
	 *
	 * @param   array  $file
	 *
	 */
	protected function uploadFile($file) {

		$dispatcher = JEventDispatcher::getInstance();

		$object_file = new JObject($file);
		$result = $dispatcher->trigger('onContentBeforeSave', array('COM_MEDIA_MCM.file', &$object_file, true));

		$success = file_put_contents($object_file->filepath, $object_file->content);

		if (!$success)
		{
			// Error in upload
			JError::raiseWarning(100, JText::_('COM_MEDIA_MCM_ERROR_UNABLE_TO_UPLOAD_FILE'));
			return false;
		}

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger('onContentAfterSave', array('com_media.file', &$object_file, true));
		$this->setMessage(JText::sprintf('COM_MEDIA_MCM_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_MCM_BASE))));
	}

}