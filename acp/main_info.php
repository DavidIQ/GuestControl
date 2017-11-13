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

namespace davidiq\GuestControl\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\davidiq\GuestControl\acp\main_module',
			'title'		=> 'ACP_GUESTCONTROL_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_GUESTCONTROL_SETTINGS',
					'auth'	=> 'ext_davidiq/GuestControl && acl_a_board',
					'cat'	=> array('ACP_GUESTCONTROL_TITLE')
				),
			),
		);
	}
}
