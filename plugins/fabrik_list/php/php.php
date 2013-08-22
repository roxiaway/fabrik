<?php
/**
 *  Add an action button to run PHP
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.list.php
 * @copyright   Copyright (C) 2005-2013 fabrikar.com - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-list.php';

/**
 *  Add an action button to run PHP
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.list.php
 * @since       3.0
 */

class PlgFabrik_ListPhp extends plgFabrik_List
{

	protected $buttonPrefix = 'php';

	protected $msg = null;

	/**
	 * Prep the button if needed
	 *
	 * @param   array  &$args  Arguements
	 *
	 * @return  bool;
	 */

	public function button(&$args)
	{
		parent::button($args);
		return true;
	}

	/**
	 * Get button image
	 *
	 * @since   3.1b
	 *
	 * @return   string  image
	 */

	protected function getImageName()
	{
		$img = parent::getImageName();
		if (FabrikWorker::j3() && $img === 'php.png')
		{
			$img = 'lightning.png';
		}
		return $img;
	}

	/**
	 * Get the button label
	 *
	 * @return  string
	 */

	protected function buttonLabel()
	{
		return $this->getParams()->get('table_php_button_label', parent::buttonLabel());
	}

	/**
	 * Get the parameter name that defines the plugins acl access
	 *
	 * @return  string
	 */

	protected function getAclParam()
	{
		return 'table_php_access';
	}

	/**
	 * Can the plug-in select list rows
	 *
	 * @return  bool
	 */

	public function canSelectRows()
	{
		return true;
	}

	/**
	 * Do the plug-in action
	 *
	 * @param   array   $opts    Custom options
	 *
	 * @return  bool
	 */

	public function process($opts = array())
	{
		$params = $this->getParams();
		$f = JFilterInput::getInstance();
		$file = $f->clean($params->get('table_php_file'), 'CMD');
		if ($file == -1 || $file == '')
		{
			$code = $params->get('table_php_code');
			@eval($code);
		}
		else
		{
			require_once JPATH_ROOT . '/plugins/fabrik_list/php/scripts/' . $file;
		}
		if (isset($statusMsg) && !empty($statusMsg))
		{
			$this->msg = $statusMsg;
		}
		return true;
	}

	/**
	 * Get the message generated in process()
	 *
	 * @param   int  $c  plugin render order
	 *
	 * @return  string
	 */

	public function process_result($c)
	{
		if (isset($this->msg))
		{
			return $this->msg;
		}
		else
		{
			$params = $this->getParams();
			$msg = $params->get('table_php_msg', JText::_('PLG_LIST_PHP_CODE_RUN'));
			return $msg;
		}
	}

	/**
	 * Return the javascript to create an instance of the class defined in formJavascriptClass
	 *
	 * @param   array  $args  Array [0] => string table's form id to contain plugin
	 *
	 * @return bool
	 */

	public function onLoadJavascriptInstance($args)
	{
		parent::onLoadJavascriptInstance($args);
		$opts = $this->getElementJSOptions();
		$params = $this->getParams();
		$opts->js_code = $params->get('table_php_js_code', '');
		$opts = json_encode($opts);
		$this->jsInstance = "new FbListPHP($opts)";
		return true;
	}

}
