<?php

/**
 * 1_0_0.php
 *
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version Author: Zen4All
 */
$db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added)
                VALUES ('Set the file name for the color stylesheet', 'ZEN4ALL_COLORPICKER_STYLESHEET', 'stylesheet_colors.css', 'This used tho find the correct stylesheet in your css folders\r\n(default: stylesheet_colors.css)', " . $configuration_group_id . ", 2, now());");


if (!zen_page_key_exists('configZ4AColorPicker' && (int)$configuration_group_id > 0)) {
  zen_register_admin_page('configZ4AColorPicker', 'BOX_CONFIGURATION_Z4A_COLOR_PICKER', 'FILENAME_CONFIGURATION', 'gID=' . $configuration_group_id, 'configuration', 'Y', $configuration_group_id);
}
if (!zen_page_key_exists('z4AColorPicker' && (int)$configuration_group_id > 0)) {
  zen_register_admin_page('z4AColorPicker', 'BOX_TOOLS_TEMPLATE_COLORS', 'FILENAME_TEMPLATE_COLORS', '', 'tools', 'Y', $configuration_group_id);
}