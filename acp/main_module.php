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

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $request, $template, $user;

		$this->tpl_name = 'acp_guestcontrol_body';
		$this->page_title = $user->lang['ACP_GUESTCONTROL_TITLE'];
		add_form_key('davidiq/GuestControl');

		$gc_viewforum_pages = $request->variable('gc_viewforum_pages', -1);
		$gc_viewtopic_pages = $request->variable('gc_viewtopic_pages', -1);
		$gc_viewtopic_posts = $request->variable('gc_viewtopic_posts', -1);

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('davidiq/GuestControl'))
			{
				trigger_error('FORM_INVALID');
			}

			$gc_forums = array();
			$sql = "SELECT forum_id
						FROM " . FORUMS_TABLE . "
						WHERE forum_type = " . FORUM_POST;

			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$gc_forums[] = (int)$row['forum_id'];
			}
			$db->sql_freeresult($result);

			$sql = "UPDATE " . FORUMS_TABLE . "
					SET gc_viewforum_pages = {$gc_viewforum_pages},
						gc_viewtopic_pages = {$gc_viewtopic_pages},
						gc_viewtopic_posts = {$gc_viewtopic_posts}
					WHERE " . $db->sql_in_set('forum_id', $gc_forums);
			$db->sql_query($sql);

			trigger_error($user->lang('ACP_GUESTCONTROL_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
            'GC_VIEWFORUM_PAGES'	=> $gc_viewforum_pages,
			'GC_VIEWTOPIC_PAGES'	=> $gc_viewtopic_pages,
            'GC_VIEWTOPIC_POSTS'	=> $gc_viewtopic_posts,
		));
	}
}
