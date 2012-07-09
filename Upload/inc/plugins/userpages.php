<?php
/**
 * MyXBL
 *
 * @author euantor <admin@xboxgeneration.com>
 * @version 1.0
 * @copyright euantor 2011
 * @package MyXBL
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

 // Disallow Direct Access
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

/*
*	Plugin Information
*/
function userpages_info() {
    global $lang;
    
	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
    
    return array(
        "name" => $lang->userpages_title,
        "description" => $lang->userpages_desc,
        "website" => "http://codicio.us",
		"author" => "euantor / Codicious",
        "authorsite" => "http://euantor.com",
        "version" => "1.2",
		"guid" => "a777fe64a45ccf1a7f6e5af692f2480a",
		"compatability" => "16*"        
    );   
}
/*
*	End Plugin Information
*/

/*
*	Plugin Install
*/
function userpages_install() {
    global /*$mybb, */$db, $cache;
    
    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `userpage` TEXT NOT NULL");
    $db->query("ALTER TABLE `".TABLE_PREFIX."usergroups` ADD `canuserpage` INT(1) NOT NULL DEFAULT '0', ADD `canuserpageedit` INT(1) NOT NULL DEFAULT '0', ADD `canuserpagemod` INT(1) NOT NULL DEFAULT '0';");
	
    $db->query('UPDATE '.TABLE_PREFIX.'usergroups SET canuserpage = 1 WHERE canusercp = 1');
    $db->query('UPDATE '.TABLE_PREFIX.'usergroups SET canuserpageedit = 1, canuserpagemod = 1 WHERE gid IN (2, 3, 4, 6)');
	
    $cache->update_usergroups();
}
/*
*	End Plugin Install
*/

/*
*	Check if plugin is installed
*/
function userpages_is_installed() {
    global $db;
    return $db->field_exists("userpage", "users");
}
/*
*	End Check if plugin is installed
*/

/*
*	Plugin Activate
*/
function userpages_activate() {
    global $db, $mybb, $lang;
    
	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
	
    $settings_group = array(
        "gid" => "",
        "name" => "userpages",
        "title" => $lang->userpages_settings_title,
        "description" => $lang->userpages_settings_desc,
        "disporder" => "0",
        "isdefault" => "0",
    );
    
    $db->insert_query("settinggroups", $settings_group);
    $gid = $db->insert_id();
	
    $setting[0] = array(
	    "name" => "userpages_html_active",
	    "title" => $lang->userpages_html_active,
	    "description" => $lang->userpages_html_active_desc,
	    "optionscode" => "yesno",
	    "value" => "0",
	    "disporder" => "1",
	    "gid" => $gid,
	);
	
    $setting[1] = array(
	    "name" => "userpages_mycode_active",
	    "title" => $lang->userpages_mycode_active,
	    "description" => $lang->userpages_mycode_active_desc,
	    "optionscode" => "yesno",
	    "value" => "1",
	    "disporder" => "2",
	    "gid" => $gid,
	);
	
    $setting[2] = array(
	    "name" => "userpages_images_active",
	    "title" => $lang->userpages_images_active,
	    "description" => $lang->userpages_images_active_desc,
	    "optionscode" => "yesno",
	    "value" => "1",
	    "disporder" => "3",
	    "gid" => $gid,
	);
	
	$setting[3] = array(
	    "name" => "userpages_badwords_active",
	    "title" => $lang->userpages_badwords_active,
	    "description" => $lang->userpages_badwords_active_desc,
	    "optionscode" => "yesno",
	    "value" => "1",
	    "disporder" => "4",
	    "gid" => $gid,
	);
	
	$setting[4] = array(
	    "name" => "userpages_videos_active",
	    "title" => $lang->userpages_videos_active,
	    "description" => $lang->userpages_videos_active_desc,
	    "optionscode" => "yesno",
	    "value" => "1",
	    "disporder" => "5",
	    "gid" => $gid,
	);
	
	foreach ($setting as $row) {
	    $db->insert_query("settings", $row);
	}
    rebuild_settings();
    
    $template[0] = array(
		"title" => 'userpages_content',
		"template"	=> '<html>
	<head>
		<title>{$mybb->settings[\\\'bbname\\\']} - {$lang->viewinguserpage}</title>
		{$headerinclude}
	</head>
	<body>
		{$header}

		<table border="0" cellspacing="{$theme[\\\'borderwidth\\\']}" cellpadding="{$theme[\\\'tablespace\\\']}" class="tborder">
			<thead>
				<tr>
					<td class="thead"><strong>{$lang->viewinguserpage}</strong></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="tcat">
						{$memprofile[\\\'view_full_profile\\\']}
					</td>
				</tr>
				<tr>
					<td class="trow1">
						{$memprofile[\\\'userpage\\\']}
					</td>
				</tr>
			</tbody>
		</table>
		{$footer}
	</body>
</html>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
    $template[1] = array(
		"title" => 'userpages_usercp_main',
		"template"	=> '<html>
	<head>
		<title>{$mybb->settings[\\\'bbname\\\']} - {$lang->changeuserpage}</title>
		{$headerinclude}
	</head>
	<body>
		{$header}
		<table width="100%" border="0" align="center">
			<tr>
				{$usercpnav}
				<td valign="top">
					<form method="post" action="usercp.php">
						<table border="0" cellspacing="{$theme[\\\'borderwidth\\\']}" cellpadding="{$theme[\\\'tablespace\\\']}" class="tborder">
							<tr>
								<td class="thead" colspan="2"><strong>{$lang->changeuserpage}</strong></td>
							</tr>
							<tr>
								<td class="trow1">
									{$smilieinserter}
								</td>
								<td class="trow2">
									<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
									<input type="hidden" name="action" value="edituserpage_do" />
									<textarea name="userpage_content" id="userpage_content" rows="20" cols="70">{$currentuserpage}</textarea>
									{$codebuttons}
								</td>
							</tr>
							<tr>

							</tr>
						</table>
						<br />
						<div align="center">
							<input type="submit" value="{$lang->saveuserpage}" name="{$lang->saveuserpage}" class="button" />
						</div>
					</form>
				</td>
			</tr>
		</table>
		{$footer}
	</body>
</html>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	$template[2] = array(
		"title" => 'userpages_usercp_nav',
		"template"	=> '<tr>
	<td class="tcat">
		<div class="float_right"><img src="{$theme[\\\'imgdir\\\']}/collapse{$collapsedimg[\\\'usercpuserpages\\\']}.gif" id="usercpuserpages_img" class="expander" alt="[-]"/></div>
		<div><span class="smalltext"><strong>{$lang->usercp_userpages}</strong></span></div>
	</td>
</tr>
<tbody style="{$collapsed[\\\'usercpuserpages_e\\\']}" id="usercpuserpages_e">
	<tr><td class="trow1 smalltext"><a href="usercp.php?action=edituserpage" class="usercp_nav_item usercp_nav_profile">{$lang->changeuserpage}</a></td></tr>
</tbody>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	$template[3] = array(
		"title" => 'userpages_modcp_main',
		"template"	=> '<html>
	<head>
		<title>{$mybb->settings[\\\'bbname\\\']} - {$lang->userpages_modcp}</title>
		{$headerinclude}
	</head>
	<body>
		{$header}
		<table width="100%" border="0" align="center">
			<tr>
				{$modcp_nav}
				<td valign="top">
					{$multipage}
					<table border="0" cellspacing="{$theme[\\\'borderwidth\\\']}" cellpadding="{$theme[\\\'tablespace\\\']}" class="tborder">
						<tr>
							<td class="thead" colspan="3"><strong>{$lang->userpages_modcp}</strong></td>
						</tr>
						<tr>
							<td class="tcat"><strong>{$lang->username}</strong></td>
							<td class="tcat" colspan="2" align="center"><strong>{$lang->action}</strong></td>
						</tr>
						{$userpages_users}
					</table>
					{$multipage}
				</td>
			</tr>
		</table>
		{$footer}
	</body>
</html>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	$template[4] = array(
		"title" => 'userpages_modcp_singleuser',
		"template"	=> '<tr>
	<td class="{$altbg}">
		<a href="{$user[\\\'edituserpagelink\\\']}" title="{$lang->edituserpage}">{$user[\\\'username\\\']}</a>
	</td>
	<td class="{$altbg}" align="center">
		<a href="{$user[\\\'viewuserpagelink\\\']}">{$lang->viewuserpage}</a>
	</td>
	<td class="{$altbg}" align="center">
		<a href="{$user[\\\'edituserpagelink\\\']}">{$lang->edituserpage}</a>
	</td>
</tr>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	$template[5] = array(
		"title" => 'userpages_modcp_modify',
		"template"	=> '<html>
	<head>
		<title>{$mybb->settings[\\\'bbname\\\']} - {$lang->userpages_modcp_modify}</title>
		{$headerinclude}
	</head>
	<body>
		{$header}
		<table width="100%" border="0" align="center">
			<tr>
				{$modcp_nav}
				<td valign="top">
					<form method="post" action="modcp.php">
						<table border="0" cellspacing="{$theme[\\\'borderwidth\\\']}" cellpadding="{$theme[\\\'tablespace\\\']}" class="tborder">
							<tr>
								<td class="thead" colspan="2"><strong>{$lang->userpages_modcp_modify}</strong></td>
							</tr>
							<tr>
								<td class="trow1">
									{$smilieinserter}
								</td>
								<td class="trow2">
									<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
									<input type="hidden" name="action" value="userpages_edit_do" />
									<input type="hidden" name="uid" value="{$uid}" />
									<textarea name="userpage_content" id="userpage_content" rows="20" cols="70">{$content[\\\'userpage\\\']}</textarea>
									{$codebuttons}
								</td>
							</tr>
						</table>
						<br />
						<div align="center">
							<input type="submit" value="{$lang->saveuserpage}" name="{$lang->saveuserpage}" class="button" />
						</div>
					</form>
				</td>
			</tr>
		</table>
		{$footer}
	</body>
</html>',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	$template[6] = array(
		"title" => 'userpages_modcp_nav',
		"template"	=> '<tr>
		<td class="tcat">
			<div class="float_right"><img src="{$theme[\\\'imgdir\\\']}/collapse{$collapsedimg[\\\'modcpuserpages\\\']}.gif" id="modcpuserpages_img" class="expander" alt="[-]" title="[-]" /></div>
			<div><span class="smalltext"><strong>{$lang->userpages_modcp}</strong></span></div>
		</td>
	</tr>
	<tbody style="{$collapsed[\\\'modcpuserpages_e\\\']}" id="modcpuserpages_e">
		<tr><td class="trow1 smalltext"><a href="modcp.php?action=userpages" class="modcp_nav_item modcp_nav_editprofile">{$lang->moderate_userpages}</a></td></tr>
	</tbody>
',
		"sid"		=> "-1",
		"version"	=> "1.0",
		"dateline"	=> TIME_NOW
	);
	
	foreach ($template as $row) {
		$db->insert_query("templates", $row);
	}
	
	#include  MYBB_ROOT."/inc/adminfunctions_templates.php";
	include_once  MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("member_profile", "#".preg_quote('<span class="largetext"><strong>{$formattedname}</strong></span><br />')."#i", '<span class="largetext"><strong>{$formattedname}</strong></span><br />'."\n".'{$userpagelink}');
}
/*
*	End Plugin Activate
*/

/*
*	Plugin De-Activate
*/
function userpages_deactivate() {
    global $db;
    
    $query = $db->simple_select("settinggroups", "gid", "name='userpages'");
    $gid = $db->fetch_field($query, 'gid');
    $db->delete_query("settinggroups", "gid='".$gid."'");
    $db->delete_query("settings", "gid='".$gid."'");
    $db->delete_query("templates", "title LIKE 'userpages_%'");
    rebuild_settings();
    
    #include  MYBB_ROOT."/inc/adminfunctions_templates.php";
    include_once  MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("member_profile", "#".preg_quote("\n".'{$userpagelink}')."#i", '');
}
/*
*	End Plugin De-Activate
*/

/*
*	Plugin Uninstall
*/
function userpages_uninstall() {
    global $db, $cache;
    
    $db->query("ALTER TABLE `".TABLE_PREFIX."users` DROP `userpage`;");
    $db->query("ALTER TABLE `".TABLE_PREFIX."usergroups` DROP `canuserpage` , DROP `canuserpageedit` , DROP `canuserpagemod` ;");
    $cache->update_usergroups();
}
/*
*	End Plugin Uninstall
*/

/*
*	Usergroup permissions
*	This function writes the permission checkboxes out to the permissions page
*/
$plugins->add_hook("admin_formcontainer_end", "userpages_edit_group");
function userpages_edit_group()
{
	global $run_module, $form_container, $lang, $form;

	/*$lang->load("userpages");*/

	if($run_module == 'user' && !empty($form_container->_title) && !empty($lang->users_permissions) && $form_container->_title == $lang->users_permissions)
	{
		global $mybb;
		
		if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
		{
			$lang->load("userpages");
		}

		$userpages_options = array();
		$userpages_options[] = $form->generate_check_box('canuserpage', 1, $lang->userpages_perm_base, array('checked' => $mybb->input['canuserpage']));
		$userpages_options[] = $form->generate_check_box('canuserpageedit', 1, $lang->userpages_perm_edit, array('checked' => $mybb->input['canuserpageedit']));	
		$userpages_options[] = $form->generate_check_box('canuserpagemod', 1, $lang->userpages_perm_mod, array('checked' => $mybb->input['canuserpagemod']));

		$form_container->output_row($lang->userpages_perm, '', '<div class="group_settings_bit">'.implode('</div><div class="group_settings_bit">', $userpages_options).'</div>');
	}
}
/*
*	End Usergroup Permissions
*/

/*
*	Usergroup Permissions
*	This function retrieves the permissions sent from the previous function and saves the permission settings
*/
$plugins->add_hook("admin_user_groups_edit_commit", "userpages_edit_group_do");
function userpages_edit_group_do()
{
	global $updated_group, $mybb;

	$updated_group['canuserpage'] = intval($mybb->input['canuserpage']);
	$updated_group['canuserpageedit'] = intval($mybb->input['canuserpageedit']);
	$updated_group['canuserpagemod'] = intval($mybb->input['canuserpagemod']);
}
/*
*	End Usergroup Permissions
*/

/*
*	Cache templates for userpages
*/
$plugins->add_hook("global_start", "userpages_templatecache");
function userpages_templatecache() {
	global $templatelist;

	if(!isset($templatelist))
	{
		return;
	}

	$action = $GLOBALS['mybb']->input['action'];
	if(THIS_SCRIPT == 'member.php')
	{
		$templatelist .= ', userpages_content';
	}
	if(THIS_SCRIPT == 'usercp.php')
	{
		$templatelist .= ', userpages_usercp_nav';
		if($action == 'edituserpage')
		{
			$templatelist .= ', userpages_usercp_main, smilieinsert, codebuttons';
		}
	}
	if(THIS_SCRIPT == 'modcp.php')
	{
		$templatelist .= ', userpages_modcp_nav';
		if($action == 'userpages')
		{
			$templatelist .= ', userpages_modcp_singleuser, userpages_modcp_main';
		}
		elseif($action == 'userpages_edit')
		{
			$templatelist .= ', userpages_modcp_modify, smilieinsert, codebuttons';
		}
	}
}
/*
*	End cached templates for userpages
*/

/*
*	UserCP Menu
*	This function creates a link to the userpage editor at the bottom of the UserCP menu
*/
$plugins->add_hook("usercp_menu", "userpages_usercpmenu", 40);
function userpages_usercpmenu() 
{
	global /*$db, */$mybb, $templates, $theme, $usercpmenu, $lang, $collapsed, $collapsedimg/*, $lang, $cache*/;
	
	/*$usergroups_cache = $cache->read("usergroups");*/
	
	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
	
	
	if (/*$usergroups_cache[$mybb->user['usergroup']]['canuserpage'] && $usergroups_cache[$mybb->user['usergroup']]['canuserpageedit']*/$mybb->usergroup['canuserpage'] && $mybb->usergroup['canuserpageedit']) {
		eval("\$usercpmenu .= \"".$templates->get("userpages_usercp_nav")."\";");
	}
}
/*
*	End UserCP Menu
*/

/*
*	UserCP
*	This function handles everything else related to the UserCP
*/
$plugins->add_hook("usercp_start", "userpages_usercp");
function userpages_usercp() 
{
	global $mybb, $db, $lang, /*$cache, */$page, $templates, $theme, $headerinclude, $header, $footer, $usercpnav, $smilieinserter, $codebuttons, $currentuserpage;

	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
	
	/*$usergroups_cache = $cache->read("usergroups");*/
	
	if ($mybb->input['action'] == "edituserpage") {
		add_breadcrumb($lang->nav_usercp, "usercp.php");
		add_breadcrumb($lang->changeuserpage, "usercp.php?action=edituserpage");
	
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpage'] || !$usergroups_cache[$mybb->user['usergroup']]['canuserpageedit']*/!($mybb->usergroup['canuserpage'] && $mybb->usergroup['canuserpageedit'])) {
			error_no_permission();
		}
		
		$smilieinserter = build_clickable_smilies();
		$codebuttons = build_mycode_inserter("userpage_content");
		
		$currentuserpage = htmlspecialchars($db->fetch_field($db->simple_select("users", "userpage", "uid = ".$mybb->user['uid']), "userpage"));
		eval("\$page = \"".$templates->get('userpages_usercp_main')."\";");
		output_page($page);
		die();
	}
	elseif ($mybb->input['action'] == "edituserpage_do" && $mybb->request_method == "post") {
	
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpage'] || !$usergroups_cache[$mybb->user['usergroup']]['canuserpageedit']*/!($mybb->usergroup['canuserpage'] && $mybb->usergroup['canuserpageedit'])) {
			error_no_permission();
		}
		
		verify_post_check($mybb->input['my_post_key']);
		
		$updatequery = array(
			'userpage' => $db->escape_string(trim($mybb->input['userpage_content']))
		);
		
		if ($db->update_query("users", $updatequery, "uid = ".$mybb->user['uid'])) {
			redirect("usercp.php?action=edituserpage", $lang->userpage_updated);
		}
		else {
			redirect("usercp.php?action=edituserpage", $lang->userpage_notpdated);
		}
	}	
}
/*
*	End UserCP
*/


/*
*	ModCP
*	This function manages everything related to Userpages in the ModCP
*/
$plugins->add_hook("modcp_start", "userpages_modcp");
function userpages_modcp() 
{
	global $mybb, $db, /*$cache, */$lang, $templates, $theme, $headerinclude, $header, $footer, $modcp_nav, $altbg, $userpages_users, $multipage, $smilieinserter, $codebuttons;
	
	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
	
	/*$usergroups_cache = $cache->read("usergroups");*/
	
	/*
	*	Adding a link to the ModCP Menu.
	*	This has to be done by replacing the current ModCP Menu as it's created before the hook
	*/
	
	eval("\$newentry = \"".$templates->get('userpages_modcp_nav')."\";");
	$modcp_nav = str_replace("</table>", $newentry."</table>", $modcp_nav);
	
	if ($mybb->input['action'] == "userpages") {
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpagemod']*/!$mybb->usergroup['canuserpagemod']) {
			error_no_permission();
		}
		
		add_breadcrumb($lang->nav_modcp, "modcp.php");
		add_breadcrumb($lang->userpages_modcp, "modcp.php?action=userpages");
		
		$page = intval($mybb->input['page']);
		
		if($page < 1) {
			$page = 1;
		}
		
		$query = $db->simple_select("users", "uid, username, usergroup", "userpage != ''", array( 'limit_start' => (($page-1)*10), 'limit' => 10));
		
		$altbg = "trow2";
		
		$viewuserpage = $lang->viewuserpage;
		while ($user = $db->fetch_array($query)) {
			if ($altbg == "trow1") {
				$altbg = "trow2";
			}
			else {
				$altbg = "trow1";
			}
			
			$user['edituserpagelink'] = $mybb->settings['bburl']."/modcp.php?action=userpages_edit&amp;uid=".$user['uid'];
			
			$lang->viewuserpage = $lang->sprintf($viewuserpage, $user['username']);
			$user['username'] = format_name($user['username'], $user['usergroup']);
			
			if ($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && $_SERVER['SEO_SUPPORT'] == 1)) {
				$sep = "?";
			}
			else {
				$sep = "&amp;";
			}
			
			$user['viewuserpagelink'] = get_profile_link($user['uid']).$sep."area=userpage";
			eval("\$userpages_users .= \"".$templates->get('userpages_modcp_singleuser')."\";");
		}
		
		$numusers = $db->fetch_field($db->simple_select("users", "COUNT(uid) AS count", "userpage != ''"), "count");
		$multipage = multipage($numusers, $mybb->settings['threadsperpage'], $page, $_SERVER['PHP_SELF']."?action=userpages");
		
		eval("\$page = \"".$templates->get('userpages_modcp_main')."\";");
		output_page($page);
		die();
	}
	elseif ($mybb->input['action'] == "userpages_edit") {
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpagemod']*/!$mybb->usergroup['canuserpagemod']) {
			error_no_permission();
		}
		
		$uid = intval($mybb->input['uid']);
		$query = $db->simple_select("users", "username, userpage", "uid = ".$uid);
		$content = $db->fetch_array($query);
		
		$content['userpage'] = htmlspecialchars($content['userpage']);
		
		$smilieinserter = build_clickable_smilies();
		$codebuttons = build_mycode_inserter("userpage_content");
		
		$lang->userpages_modcp_modify = $lang->sprintf($lang->userpages_modcp_modify, $content['username']);
		
		add_breadcrumb($lang->nav_modcp, "modcp.php");
		add_breadcrumb($lang->userpages_modcp, "modcp.php?action=userpages");
		add_breadcrumb($lang->userpages_modcp_modify);
		
		eval("\$page = \"".$templates->get('userpages_modcp_modify')."\";");
		output_page($page);
		die();
	}
	elseif ($mybb->input['action'] == "userpages_edit_do" && $mybb->request_method == "post") {
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpagemod']*/!$mybb->usergroup['canuserpagemod']) {
			error_no_permission();
		}
		
		verify_post_check($mybb->input['my_post_key']);
		
		$updatequery = array(
			'userpage' => $db->escape_string(trim($mybb->input['userpage_content']))
		);
		
		if ($db->update_query("users", $updatequery, "uid = ".$mybb->input['uid'])) {
			redirect("modcp.php?action=userpages", $lang->userpage_updated);
		}
		else {
			redirect("modcp.php?action=userpages", $lang->userpage_notpdated);
		}
	}
}
/*
*	End ModCP
*/

/*
*	User Profile
*	This is the main function that displays the actual Userpage
*/
$plugins->add_hook("member_profile_start", "userpages_main");
function userpages_main() 
{
	global $mybb, $db, $memprofile, $lang/*, $cache*/, $userpage_parser, $templates, $theme, $headerinclude, $header, $footer, $page, $parser, $userpagelink;
	
	if(!(isset($lang->viewinguserpage) && isset($lang->usercp_userpages)))
	{
		$lang->load("userpages");
	}
	
	/*$usergroups_cache = $cache->read("usergroups");*/
	
	$memprofile = $db->fetch_array($db->simple_select("users", "userpage, username, uid", "uid = ".intval($mybb->input['uid'])), "userpage");
	
	if ($memprofile['userpage'] !== "") {
		if ($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && $_SERVER['SEO_SUPPORT'] == 1)) {
			$sep = "?";
		}
		else {
			$sep = "&amp;";
		}

		$userpagelink = '<span class="smalltext"><a href="'.get_profile_link(intval($mybb->input['uid'])).$sep.'area=userpage">'.$lang->sprintf($lang->viewuserpage, $memprofile['username']).'</a></span><br />';
	}
	
	if ($mybb->input['area'] == "userpage") {
		if (/*!$usergroups_cache[$mybb->user['usergroup']]['canuserpage']*/!$mybb->usergroup['canuserpage']) {
			error_no_permission();
		
		}

		$lang->nav_profile = $lang->sprintf($lang->nav_profile, $memprofile['username']);
		$lang->viewinguserpage = $lang->sprintf($lang->viewinguserpage, $memprofile['username']);

		add_breadcrumb($lang->nav_profile, get_profile_link($memprofile['uid']));
		add_breadcrumb($lang->viewinguserpage);

		$options = array(
			"allow_html" => $mybb->settings['userpages_html_active'],
			"allow_mycode" => $mybb->settings['userpages_mycode_active'],
			"allow_smilies" => 1,
			"allow_imgcode" => $mybb->settings['userpages_images_active'],
			"filter_badwords" => $mybb->settings['userpages_badwords_active'],
			"nl2br" => 1,
			"allow_videocode" => $mybb->settings['userpages_videos_active'],
			"me_username" => $memprofile['username'],
		);
		
		$memprofile['userpage'] = $parser->parse_message($memprofile['userpage'], $options);
			
		$memprofile['view_full_profile'] =  '<a href="'.get_profile_link($memprofile['uid']).'">&laquo; '.$lang->nav_profile.'</a>';
		
		eval("\$page = \"".$templates->get('userpages_content')."\";");
		output_page($page);
		die();
	}	
}
/*
*	End User Profile
*/
?>
