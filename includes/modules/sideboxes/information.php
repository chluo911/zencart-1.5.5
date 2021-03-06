<?php
/**
 * information sidebox - displays list of general info links, as defined in this file
 *
 * @package templateSystem
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Sat Oct 17 21:54:07 2015 -0400 Modified in v1.5.5 $
 */

  unset($information);

  if (DEFINE_SHIPPINGINFO_STATUS <= 1) {
      $information[] = '<a href="' . zen_href_link(FILENAME_SHIPPING) . '">' . BOX_INFORMATION_SHIPPING . '</a>';
  }
  if (DEFINE_PRIVACY_STATUS <= 1) {
      $information[] = '<a href="' . zen_href_link(FILENAME_PRIVACY) . '">' . BOX_INFORMATION_PRIVACY . '</a>';
  }
  if (DEFINE_CONDITIONS_STATUS <= 1) {
      $information[] = '<a href="' . zen_href_link(FILENAME_CONDITIONS) . '">' . BOX_INFORMATION_CONDITIONS . '</a>';
  }
  if (DEFINE_CONTACT_US_STATUS <= 1) {
      $information[] = '<a href="' . zen_href_link(FILENAME_CONTACT_US, '', 'SSL') . '">' . BOX_INFORMATION_CONTACT . '</a>';
  }

// forum/bb link:
  if (!empty($external_bb_url) && !empty($external_bb_text)) {
      $information[] = '<a href="' . $external_bb_url . '" target="_blank">' . $external_bb_text . '</a>';
  }

  if (DEFINE_SITE_MAP_STATUS <= 1) {
      $information[] = '<a href="' . zen_href_link(FILENAME_SITE_MAP) . '">' . BOX_INFORMATION_SITE_MAP . '</a>';
  }

  // only show GV FAQ when installed
  if (MODULE_ORDER_TOTAL_GV_STATUS == 'true') {
      $information[] = '<a href="' . zen_href_link(FILENAME_GV_FAQ) . '">' . BOX_INFORMATION_GV . '</a>';
  }
  // only show Discount Coupon FAQ when installed
  if (DEFINE_DISCOUNT_COUPON_STATUS <= 1 && MODULE_ORDER_TOTAL_COUPON_STATUS == 'true') {
      $information[] = '<a href="' . zen_href_link(FILENAME_DISCOUNT_COUPON) . '">' . BOX_INFORMATION_DISCOUNT_COUPONS . '</a>';
  }

  if (SHOW_NEWSLETTER_UNSUBSCRIBE_LINK == 'true') {
      $information[] = '<a href="' . zen_href_link(FILENAME_UNSUBSCRIBE) . '">' . BOX_INFORMATION_UNSUBSCRIBE . '</a>';
  }

  require($template->get_template_dir('tpl_information.php', DIR_WS_TEMPLATE, $current_page_base, 'sideboxes'). '/tpl_information.php');

  $title =  BOX_HEADING_INFORMATION;
  $title_link = false;

  require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base, 'common') . '/' . $column_box_default);
