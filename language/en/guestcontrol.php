<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'LOGIN_TO_CONTINUE'	            => 'You must be logged in to continue',
    'LOGIN_TO_READ_POST'            => '<p><em><strong>You must be <a href="%s">logged in</a> to read the additional posts in this topic</strong></em></p>',
    'LOGIN_TO_READ_POST_FORM'       => 'Log in to read the selected post',
));
