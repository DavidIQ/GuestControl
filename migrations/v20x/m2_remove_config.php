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

class m2_remove_config extends \phpbb\db\migration\migration
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
		return array('\davidiq\GuestControl\migrations\v20x\m1_initial_schema');
	}

	/**
	 * Indicates the migration has successfully run
	 *
	 * @return boolean
	 * @access public
	 */
	public function effectively_installed()
	{
		return !isset($this->config['davidiq_guestcontrol']);
	}

	/**
	 * Convert old options into forums table and remove the old guest control config options.
	 *
	 * @return array Array of data to update
	 * @access public
	 */
	public function update_data()
	{
		$gc_viewforum_pages = (int)$this->config['gc_viewforum_pages'];
		$gc_viewtopic_pages = (int)$this->config['gc_viewtopic_pages'];
		$gc_viewtopic_posts = (int)$this->config['gc_viewtopic_posts'];

		if ($gc_viewforum_pages > -1 || $gc_viewtopic_pages > -1 || $gc_viewtopic_posts > -1)
		{
			$sql = "SELECT config_value
				FROM {$this->table_prefix}config_text
				WHERE config_name = 'gc_forums'";
			$result = $this->db->sql_query($sql);
			$gc_forums = explode(',', $this->db->sql_fetchfield('config_value'));
			$this->db->sql_freeresult($result);

			if (!count($gc_forums))
			{
				$gc_forums = array();
				// Get all forums
				$sql = "SELECT forum_id
						FROM {$this->table_prefix}forums
						WHERE forum_type = " . FORUM_POST;
				$result = $this->db->sql_query($sql);
				while ($row = $this->db->sql_fetchrow($result))
				{
					$gc_forums[] = (int)$row['forum_id'];
				}
				$this->db->sql_freeresult($result);
			}

			$this->db->sql_query("UPDATE {$this->table_prefix}forums
								SET gc_viewforum_pages = {$gc_viewforum_pages},
									gc_viewtopic_pages = {$gc_viewtopic_pages},
									gc_viewtopic_posts = {$gc_viewtopic_posts}
								WHERE " . $this->db->sql_in_set('forum_id', $gc_forums));
		}

		return array(
			array('config.remove', array('davidiq_guestcontrol')),
			array('config.remove', array('gc_viewforum_pages')),
			array('config.remove', array('gc_viewtopic_pages')),
			array('config.remove', array('gc_viewtopic_posts')),

			array('config_text.remove', array('gc_forums')),
		);
	}
}