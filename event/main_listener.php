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

namespace davidiq\GuestControl\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
            'core.viewforum_get_topic_data'     => 'check_forum_topic_data',
			'core.viewtopic_get_post_data'	    => 'check_topic_readability',
            'core.viewtopic_modify_post_row'    => 'check_post_readability',
            'core.viewtopic_before_f_read_check'=> 'check_for_post_login',
		);
	}

	/* @var \phpbb\config\config */
	protected $config;

    /* @var \phpbb\config\db_text */
    protected $config_text;

	/* @var \phpbb\template\template */
	protected $template;

    /** @var \phpbb\user */
    protected $user;

	/** @var \phpbb\request\request */
	protected $request;

    /** @var  string */
    protected $php_ext;

    /** @var  string */
    protected $phpbb_root_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config	        $config		        Configuration object
     * @param \phpbb\config\db_text         $config_text        Configuration text object
	 * @param \phpbb\template\template	    $template	        Template object
     * @param \phpbb\user                   $user               User object
	 * @param \phpbb\request\request        $request            Request object
     * @param string                        $php_ext            The PHP extension in use
     * @param string                        $phpbb_root_path    The root path for the phpBB installation
     */
	public function __construct(\phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\template\template $template, \phpbb\user $user, \phpbb\request\request $request, $php_ext, $phpbb_root_path)
	{
		$this->config = $config;
        $this->config_text = $config_text;
		$this->template = $template;
        $this->user = $user;
		$this->request = $request;
        $this->php_ext = $php_ext;
        $this->phpbb_root_path = $phpbb_root_path;
	}

    /**
     * Checks to see if the guest user can read the current page in viewforum.
     *
     * @param \phpbb\event\data	$event	Event object
     */
	public function check_forum_topic_data($event)
    {
        $this->check_read_access((int)$this->config['gc_viewforum_pages'], $event, (int)$this->config['topics_per_page'], 'topic');
    }

    /**
     * Checks to see if the guest user can read the current page in viewtopic.
     *
     * @param \phpbb\event\data	$event	Event object
     */
    public function check_topic_readability($event)
    {
        $this->check_read_access((int)$this->config['gc_viewtopic_pages'], $event, (int)$this->config['posts_per_page'], 'post');
    }

    /**
     * Checks if post can be read before rendering it out to the user.
     *
     * @param \phpbb\event\data $event  Event object
     */
    public function check_post_readability($event)
    {
        if ($this->gc_is_active_for_user($event))
        {
            $posts_to_display = (int)$this->config['gc_viewtopic_posts'];
            if ($posts_to_display >= 0)
            {
                $start = $this->request->variable('start', 0) + 1;
                $current_row_number = (int) $event['current_row_number'];
                $topic_post_num = $current_row_number + $start;

                if ($topic_post_num > $posts_to_display)
                {
                    $post_row = $event['post_row'];
                    $post_id = (int)$post_row['POST_ID'];
                    $topic_id = (int)$event['topic_data']['topic_id'];
                    $this->user->add_lang_ext('davidiq/GuestControl', 'guestcontrol');
                    if ($topic_post_num > ($posts_to_display + 1))
                    {
                        if ($current_row_number <= 1)
                        {
                            // There's no post to show so let's go with the login box. This is an error in configuration.
							$redirect_url = append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", "t=$topic_id&amp;postlogin=$post_id");
                            login_box($redirect_url, sprintf($this->user->lang('LOGIN_TO_READ_POST'), $redirect_url), $this->user->lang('LOGIN_TO_CONTINUE'));
                        }
                        // We only need the message to log in once so we clear the rest of the posts
                        $post_row = array();
                        $post_row['GC_EMPTY_POST'] = true;
                    }
                    else
                    {
                        // Set the post message for enforcing login
                        $post_row['MESSAGE'] = sprintf($this->user->lang('LOGIN_TO_READ_POST'), append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", "t=$topic_id&amp;postlogin=$post_id"));
                        // Don't render attachments
                        $post_row['S_HAS_ATTACHMENTS'] = false;
                    }

                    $event['post_row'] = $post_row;
                }
            }
        }
    }

    /**
     * Checks to see if user is trying to login to view a post
     *
     * @param $event
     */
    public function check_for_post_login($event)
    {
        if ($this->gc_is_active_for_user($event))
        {
            $post_login = $this->request->variable('postlogin', 0);
            if ($post_login)
            {
                $this->user->add_lang_ext('davidiq/GuestControl', 'guestcontrol');
                login_box(append_sid("{$this->phpbb_root_path}viewtopic.$this->php_ext", "p=$post_login") . "#p$post_login", $this->user->lang('LOGIN_TO_READ_POST_FORM'));
            }
        }
    }

    /**
     * Checks the forum read access for the guest user
     *
     * @param int   $gc_view_pages  The number of pages that guest control is checking for
     * @param \phpbb\event\data	$event	Event object
     * @param int   $num_per_page   The number of topics/posts per page as set in the board's configuration
	 * @param string $type			The type for which we are checking (topic or post)
     */
    private function check_read_access($gc_view_pages, $event, $num_per_page, $type)
    {
        if ($this->gc_is_active_for_user($event))
        {
            if ($gc_view_pages >= 0)
            {
                $start = $this->request->variable('start', 0);
                // Calculate the page number we're at
                $current_page = ($start / $num_per_page) + 1;
                if ($current_page > $gc_view_pages)
                {
                    $this->user->add_lang_ext('davidiq/GuestControl', 'guestcontrol');
                    login_box('', $this->user->lang('LOGIN_TO_CONTINUE'));
                }
                else
                {
                    $this->reset_sorts($event, $type);
                }
            }
        }
    }

    /**
     * Checks to see if guest control is enabled for the current user and forum
     *
     * @param \phpbb\event\data $event  Event object
     *
     * @return bool
     */
    private function gc_is_active_for_user($event)
    {
        if (!$this->user->data['is_registered'] && !$this->user->data['is_bot'])
        {
            $gc_forums = explode(',', $this->config_text->get('gc_forums'));
            // If we have no value for this or the first index is empty then this applies to all forums
            if (!sizeof($gc_forums) || $gc_forums[0] == '')
            {
                return true;
            }
            // Extract the current forum ID
			$current_forum_id = (int)$event[isset($event['forum_data']) ? 'forum_data' : 'topic_data']['forum_id'];
            return in_array($current_forum_id, $gc_forums);
        }
        return false;
    }

    /**
     * Resets the sorting as necessary
     *
     * @param \phpbb\event\data	$event	Event object
	 * @param string $type			The type for which we are resetting sorts (topic or post)
     */
    private function reset_sorts($event, $type)
    {
        // Reset sorts in case user is trying to go around the guest controls
        if (isset($event['sort_days']))
        {
            $event['sort_days'] = (!empty($this->user->data["user_{$type}_show_days"])) ? $this->user->data["user_{$type}_show_days"] : 0;
        }
        if (isset($event['sort_key']))
        {
            $event['sort_key'] = (!empty($this->user->data["user_{$type}_sortby_type"])) ? $this->user->data["user_{$type}_sortby_type"] : 't';
        }
        if (isset($event['sort_dir']))
        {
            $event['sort_dir'] = (!empty($this->user->data["user_{$type}_sortby_dir"])) ? $this->user->data["user_{$type}_sortby_dir"] : ($type == 'topic' ? 'd' : 'a');
        }
    }
}
