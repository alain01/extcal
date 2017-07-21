<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    {@link https://xoops.org/ XOOPS Project}
 * @license      {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package      extcal
 * @since
 * @author       XOOPS Development Team,
 */

require_once __DIR__ . '/../include/constantes.php';

/******************************************************/
/* Ajour JJD - Evenements par categries               */
/******************************************************/
/**
 * @param $options
 *
 * @return array
 */
function bExtcalUpcomingByCategoryShow($options)
{
    global $xoopsUser, $extcalConfig;

    require_once __DIR__ . '/../class/config.php';

    // Retriving module config
    //     $extcalConfig = ExtcalConfig::getHandler();
    //     $xoopsModuleConfig = $extcalConfig->getModuleConfig();

    $eventHandler = xoops_getModuleHandler(_EXTCAL_CLS_EVENT, _EXTCAL_MODULE);

    $nbEvent     = $options[0];
    $titleLenght = $options[1];
    array_shift($options);
    array_shift($options);

    // Checking if no cat is selected
    if (0 == $options[0] && 1 == count($options)) {
        $options = 0;
    }

    $events = $eventHandler->objectToArray($eventHandler->getUpcommingEvent($nbEvent, $options));
    $eventHandler->serverTimeToUserTimes($events);
    $eventHandler->formatEventsDate($events, $extcalConfig['event_date_month']);

    /***********************************************************/
    $catHandler = xoops_getModuleHandler(_EXTCAL_CLS_CAT, _EXTCAL_MODULE);
    $t          = $catHandler->objectToArray($catHandler->getAllCat($xoopsUser, 'all'));
    $tCats      = array();
    for ($h = 0, $count = count($t); $h < $count; ++$h) {
        $tCats[$t[$h]['cat_id']] = $t[$h];
    }

    $te = array();

    for ($h = 0, $count = count($events); $h < $count; ++$h) {
        $catId = $events[$h]['cat_id'];
        if (!isset($te[$catId])) {
            $te[$catId]           = $tCats[$catId];
            $te[$catId]['events'] = array();
        }
        $te[$catId]['events'][] = $events[$h];
    }

    return $te;
}

/**
 * @param $options
 *
 * @return string
 */
function bExtcalUpcomingByCategoryEdit($options)
{
    global $xoopsUser;

    $catHandler = xoops_getModuleHandler(_EXTCAL_CLS_CAT, _EXTCAL_MODULE);

    $cats = $catHandler->getAllCat($xoopsUser, 'extcal_cat_view');

    $form = _MB_EXTCAL_DISPLAY . "&nbsp;\n";
    $form .= '<input name="options[0]" size="5" maxlength="255" value="' . $options[0] . '" type="text">&nbsp;' . _MB_EXTCAL_EVENT . '<br>';
    $form .= _MB_EXTCAL_TITLE_LENGTH . ' : <input name="options[1]" size="5" maxlength="255" value="' . $options[1] . '" type="text"><br>';
    array_shift($options);
    array_shift($options);
    $form .= _MB_EXTCAL_CAT_TO_USE . '<br><select name="options[]" multiple="multiple" size="5">';
    if (array_search(0, $options) === false) {
        $form .= '<option value="0">' . _MB_EXTCAL_ALL_CAT . '</option>';
    } else {
        $form .= '<option value="0" selected="selected">' . _MB_EXTCAL_ALL_CAT . '</option>';
    }
    foreach ($cats as $cat) {
        if (array_search($cat->getVar('cat_id'), $options) === false) {
            $form .= '<option value="' . $cat->getVar('cat_id') . '">' . $cat->getVar('cat_name') . '</option>';
        } else {
            $form .= '<option value="' . $cat->getVar('cat_id') . '" selected="selected">' . $cat->getVar('cat_name') . '</option>';
        }
    }
    $form .= '</select>';

    return $form;
}
