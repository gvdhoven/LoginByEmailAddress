<?php

/**
 * Enable the plugin: Add "LoginByEmailAddress" to 
 * the $rcmail_config['plugins'] - array in config/main.inc.php
 *
 * Automatically find out the mx-record from the users email-address
 * and set it as host to make the select-dropdown obsolete
 *
 * Hides the server-select-dropdown from the login-mask
 *
 * @version 0.1
 * @author Webunity
 */
class LoginByEmailAddress extends rcube_plugin
{
	public $task = "login|logout";
	
	/**
	 * We only need a javascript file and only hook the authenticate-process
	 */
	function init()
	{
		$this->add_hook('authenticate', array($this, 'process'));
		$this->include_script('LoginByEmailAddress.js'); //hides select dropdown
	}
	
	/**
	 * If the entered email's mxrecord is in the whitelist, we use it.
	 * Otherwise, we return "localhost" to let roundcube handle the login
	 */
	function process($args)
	{
		if (!isset($args['user']))
			return null;
		
		// Login with e-mail address
		if (strpos($args['user'], '@') !== false) {
			list($strUser, $strHost) = explode('@', $args['user']);
			return array('user' => $args['user'], 'host' => $strHost);
		}

		// Login from webmail.domain.ext
		if (substr(strtolower($_SERVER['HTTP_HOST']), 0, 8) == 'webmail.') {
			$strHost = $_SERVER['HTTP_HOST'];
			return array('host' => substr(strtolower($_SERVER['HTTP_HOST']), 8));
		}

		// Unknown login type
		return array('host' => 'localhost');
	}
}	
