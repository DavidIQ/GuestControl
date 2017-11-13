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
		global $config, $request, $template, $user, $phpbb_container;

		$this->tpl_name = 'acp_guestcontrol_body';
		$this->page_title = $user->lang['ACP_GUESTCONTROL_TITLE'];
		add_form_key('davidiq/GuestControl');

        /* @var $config_text \phpbb\config\db_text */
        $config_text = $phpbb_container->get('config_text');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('davidiq/GuestControl'))
			{
				trigger_error('FORM_INVALID');
			}

            $config->set('gc_viewforum_pages', $request->variable('gc_viewforum_pages', -1));
            $config->set('gc_viewtopic_pages', $request->variable('gc_viewtopic_pages', -1));
            $config->set('gc_viewtopic_posts', $request->variable('gc_viewtopic_posts', -1));
            $config_text->set('gc_forums', implode(',', $request->variable('gc_forums', array(0))));

			trigger_error($user->lang('ACP_GUESTCONTROL_SETTING_SAVED') . adm_back_link($this->u_action));
		}

        $controlled_forums = explode(',', $config_text->get('gc_forums'));

        $forum_list = make_forum_select($controlled_forums, false, true, false, false, false, true);

        // Build forum options
        $s_forum_options = '';
        foreach ($forum_list as $f_id => $f_row)
        {
            $s_forum_options .= '<option value="' . $f_id . '"' . (($f_row['selected']) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
        }

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
            'GC_VIEWFORUM_PAGES'	=> $config['gc_viewforum_pages'],
			'GC_VIEWTOPIC_PAGES'	=> $config['gc_viewtopic_pages'],
            'GC_VIEWTOPIC_POSTS'	=> $config['gc_viewtopic_posts'],
            'S_FORUM_OPTIONS'       => $s_forum_options,
            'S_ALL_FORUMS'          => !sizeof($controlled_forums) || $controlled_forums[0] == '',
		));
	}
}
