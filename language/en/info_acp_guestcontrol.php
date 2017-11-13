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
	'ACP_GUESTCONTROL_TITLE'			=> 'Guest Control',
    'ACP_GUESTCONTROL_SETTINGS'	        => 'Guest Control Settings',
    'ACP_GUESTCONTROL_SETTING_SAVED'	=> 'Guest Control settings have been saved successfully!',

    'ACP_GUESTCONTROL_VIEWFORUM_PAGES'  => 'Number of pages to allow guests to read when viewing a forum before requiring user to login/register',
    'ACP_GUESTCONTROL_VIEWFORUM_PAGES_EXPLAIN'  => 'Entering -1 will disable this feature; 0 will require user to login/register to view any forum. This feature will not take effect if user does not have read permission for the forum.',

    'ACP_GUESTCONTROL_VIEWTOPIC_PAGES'  => 'Number of pages to allow guests to read when viewing a topic before requiring user to login/register',
    'ACP_GUESTCONTROL_VIEWTOPIC_PAGES_EXPLAIN'  => 'Entering -1 will disable this feature; 0 will require user to login/register to read any topic. This feature will not take effect if user does not have read permission for the forum.',

    'ACP_GUESTCONTROL_VIEWTOPIC_POSTS' => 'Number of posts for which to display their contents when viewing a topic',
    'ACP_GUESTCONTROL_VIEWTOPIC_POSTS_EXPLAIN' => 'Entering -1 will disable this feature; 0 will require user to login/register to be able to read the contents of any post in the topic.',

    'ACP_GUESTCONTROL_FORUMS'           => 'Forums for which to apply Guest Control options',
    'ACP_GUESTCONTROL_FORUMS_EXPLAIN'   => 'These are the forums that the above options will apply to. To enable selection un-check the <em>All forums</em> checkbox. Hold the Ctrl key to select and un-select multiple options.',
));
