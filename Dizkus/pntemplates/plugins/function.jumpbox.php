<?php
/**
 * Dizkus
 *
 * @copyright (c) 2001-now, Dizkus Development Team
 * @link http://www.dizkus.com
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Dizkus
 */

/**
 * jumpbox plugin
 * creates a dropdown list with all available forums for the current user.
 * seleting a forum issue a direct forward to the viewforum() function
 *
 */
function smarty_function_jumpbox($params, &$smarty)
{
    extract($params);
	unset($params);

    if(!pnModAPILoad('Dizkus', 'admin')) {
        $smarty->trigger_error("loading Dizkus adminapi failed");
        return;
    }

    $out = "";
    $forums = pnModAPIFunc('Dizkus', 'admin', 'readforums');
    if(count($forums)>0) {
        Loader::includeOnce('modules/Dizkus/common.php');
        $out ='<form action="' . DataUtil::formatForDisplay(pnModURL('Dizkus', 'user', 'viewforum')) . '" method="get">
               <label for="dizkus_forum"><strong>' . DataUtil::formatForDisplay(_DZK_FORUM) . ': </strong></label>
               <select name="forum" id="dizkus_forum" onchange="location.href=this.options[this.selectedIndex].value">
	           <option value="'.DataUtil::formatForDisplay(pnModURL('Dizkus', 'user', 'main')).'">' . DataUtil::formatForDisplay(_DZK_QUICKSELECTFORUM) . '</option>';
        foreach($forums as $forum) {
            if(allowedtoreadcategoryandforum($forum['cat_id'], $forum['forum_id'])) {
            	$out .= '<option value="' . DataUtil::formatForDisplay(pnModURL('Dizkus', 'user', 'viewforum', array('forum' => $forum['forum_id']))) . '">' . DataUtil::formatForDisplay($forum['cat_title']) . '&nbsp;::&nbsp;' . DataUtil::formatForDisplay($forum['forum_name']) . '</option>';
            }
        }
        $out .= '</select>
                 </form>';
    }
    return $out;

}