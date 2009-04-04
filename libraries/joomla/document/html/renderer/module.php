<?php
/**
* @version		$Id$
* @package		Joomla.Framework
* @subpackage	Document
* @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
* @license		GNU General Public License, see LICENSE.php
*/

// No direct access
defined('JPATH_BASE') or die();

/**
 * JDocument Module renderer
 *
 * @package		Joomla.Framework
 * @subpackage	Document
 * @since		1.5
 */
class JDocumentRendererModule extends JDocumentRenderer
{
	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @access public
	 * @param string 	$name		The name of the module to render
	 * @param array 		$params		Associative array of values
	 * @return string	The output of the script
	 */
	public function render($module, $params = array(), $content = null)
	{
		if (!is_object($module))
		{
			$title	= isset($params['title']) ? $params['title'] : null;

			$module =& JModuleHelper::getModule($module, $title);

			if (!is_object($module))
			{
				if (is_null($content)) {
					return '';
				} else {
					/**
					 * If module isn't found in the database but data has been pushed in the buffer
					 * we want to render it
					 */
					$tmp = $module;
					$module = new stdClass();
					$module->params = null;
					$module->module = $tmp;
					$module->id = 0;
					$module->user = 0;
				}
			}
		}

		// get the user and configuration object
		$user =& JFactory::getUser();
		$conf =& JFactory::getConfig();

		// set the module content
		if (!is_null($content)) {
			$module->content = $content;
		}

		//get module parameters
		$mod_params = new JParameter($module->params);

		$contents = '';
		if ($mod_params->get('cache', 0) && $conf->getValue('config.caching'))
		{
			$cache =& JFactory::getCache($module->module);

			$cache->setLifeTime($mod_params->get('cache_time', $conf->getValue('config.cachetime') * 60));
			$cache->setCaching(true);

			$contents = $cache->get(array('JModuleHelper', 'renderModule'), array($module, $params), $module->id. $user->get('aid', 0));
		} else {
			$contents = JModuleHelper::renderModule($module, $params);
		}

		return $contents;
	}
}