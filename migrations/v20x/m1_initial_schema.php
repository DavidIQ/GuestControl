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

namespace davidiq\GuestControl\migrations\v20x;

class m1_initial_schema extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	static public function depends_on()
	{
		return array('\davidiq\GuestControl\migrations\v10x\install_acp_module');
	}

	/**
	 * Add the guest control columns to the forums table.
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'gc_viewforum_pages'	=> array('INT:11', -1),
					'gc_viewtopic_pages'	=> array('INT:11', -1),
					'gc_viewtopic_posts'	=> array('INT:11', -1),
				),
			),
		);
	}

	/**
	 * Drop the guest control columns from the forums table.
	 *
	 * @return array Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'forums'	=> array(
					'gc_viewforum_pages',
					'gc_viewtopic_pages',
					'gc_viewtopic_posts',
				),
			),
		);
	}
}
