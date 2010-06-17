<?php
/**
 * Dizkus
 *
 * @copyright (c) 2001-now, Dizkus Development Team
 * @link http://code.zikula.org/dizkus
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Dizkus
 */

include_once 'modules/Dizkus/common.php';

/**
 * init
 */
function Dizkus_statisticsblock_init()
{
    SecurityUtil::registerPermissionSchema('Dizkus_Statisticsblock::', 'Block ID::');
}

/**
 * info
 */
function Dizkus_statisticsblock_info()
{
    $dom = ZLanguage::getModuleDomain('Dizkus');

    return array('module'         => 'Dizkus',
                 'text_type'      => __('Dizkus statistic', $dom),
                 'text_type_long' => __('Dizkus Statistics Block', $dom),
                 'allow_multiple' => true,
                 'form_content'   => false,
                 'form_refresh'   => false,
                 'show_preview'   => true);
}

/**
 * display the statisticsblock
 */
function Dizkus_statisticsblock_display($blockinfo)
{
    if (!ModUtil::available('Dizkus')) {
        return;
    }

    //check for Permission
    if (!SecurityUtil::checkPermission('Dizkus_Statisticsblock::', "$blockinfo[bid]::", ACCESS_READ)){ 
        return; 
    }

    // check if forum is turned off
    $disabled = dzk_available();
    if (!is_bool($disabled)) {
        $blockinfo['content'] = $disabled;
        return themesideblock($blockinfo);
    }

    // break out options from our content field
    $vars = BlockUtil::varsFromContent($blockinfo['content']);

    // check if cb_template is set, if not, use the default centerblock template
    if (empty($vars['sb_template'])) {
        $vars['sb_template'] = 'dizkus_statisticsblock_display.html';
    }
    if (empty($vars['sb_parameters'])) {
        $vars['sb_parameters'] = 'maxposts=5';
    }

    // build the output
    $render = Renderer::getInstance('Dizkus', false, null, true);

    $params = explode(',', $vars['sb_parameters']);

    if (is_array($params) && count($params) > 0) {
        foreach ($params as $param)
        {
            $paramdata = explode('=', $param);
            $render->assign(trim($paramdata[0]), trim($paramdata[1]));
        }
    }

    $blockinfo['content'] = $render->fetch(trim($vars['sb_template']));

    return themesideblock($blockinfo);
}

/**
 * Update the block
 */
function Dizkus_statisticsblock_update($blockinfo)
{
    if (!SecurityUtil::checkPermission('Dizkus_Statisticsblock::', "$blockinfo[bid]::", ACCESS_ADMIN)) {
        return false;
    }

    $sb_template   = FormUtil::getPassedValue('sb_template', 'dizkus_statisticsblock_display.html', 'POST');
    $sb_parameters = FormUtil::getPassedValue('sb_parameters', 'maxposts=5', 'POST');

    $blockinfo['content'] = BlockUtil::varsToContent(compact('sb_template', 'sb_parameters'));

    return($blockinfo);
}

/**
 * Modify the block
 */
function Dizkus_statisticsblock_modify($blockinfo)
{
    if (!SecurityUtil::checkPermission('Dizkus_Statisticsblock::', "$blockinfo[bid]::", ACCESS_ADMIN)) {
        return false;
    }

    // Break out options from our content field
    $vars = BlockUtil::varsFromContent($blockinfo['content']);

    if (!isset($vars['sb_parameters']) || empty($vars['sb_parameters'])) {
        $vars['sb_parameters'] = 'maxposts=5';
    }
    if (!isset($vars['sb_template']) || empty($vars['sb_template'])) {
        $vars['sb_template']   = 'dizkus_statisticsblock_display.html';
    }

    $render = Renderer::getInstance('Dizkus', false, null, true);

    $render->assign('vars', $vars);

    return $render->fetch('dizkus_statisticsblock_config.html');
}