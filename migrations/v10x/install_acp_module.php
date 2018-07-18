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

namespace davidiq\GuestControl\migrations\v10x;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['davidiq_guestcontrol']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v319');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('davidiq_guestcontrol', 1)),
            array('config.add', array('gc_viewforum_pages', -1)),
            array('config.add', array('gc_viewtopic_pages', -1)),
            array('config.add', array('gc_viewtopic_posts', -1)),

            array('config_text.add', array('gc_forums', '')),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GUESTCONTROL_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_GUESTCONTROL_TITLE',
				array(
					'module_basename'	=> '\davidiq\GuestControl\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
