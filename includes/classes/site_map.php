<?php
/**
 * site_map.php
 *
 * @package general
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Sat Oct 17 22:52:38 2015 -0400 Modified in v1.5.5 $
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}
/**
 * site_map.php
 *
 * @package general
 */
 class zen_SiteMapTree
 {
     public $root_category_id = 0;
     public $max_level = 0;
     public $data = array();
     public $root_start_string = '';
     public $root_end_string = '';
     public $parent_start_string = '';
     public $parent_end_string = '';
     public $parent_group_start_string = "\n<ul>";
     public $parent_group_end_string = "</ul>\n";
     public $child_start_string = '<li>';
     public $child_end_string = "</li>\n";
     public $spacer_string = '';
     public $spacer_multiplier = 1;

     public function __construct($load_from_database = true)
     {
         global $languages_id, $db;
         $this->data = array();
         $categories_query = "select c.categories_id, cd.categories_name, c.parent_id
                      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd
                      where c.categories_id = cd.categories_id
                      and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                      and c.categories_status != '0'
                      order by c.parent_id, c.sort_order, cd.categories_name";
         $categories = $db->Execute($categories_query);
         while (!$categories->EOF) {
             $this->data[$categories->fields['parent_id']][$categories->fields['categories_id']] = array('name' => $categories->fields['categories_name'], 'count' => 0);
             $categories->MoveNext();
         }
     }

     public function buildBranch($parent_id, $level = 0, $parent_link = '')
     {
         $result = $this->parent_group_start_string;

         if (isset($this->data[$parent_id])) {
             foreach ($this->data[$parent_id] as $category_id => $category) {
                 $category_link = $parent_link . $category_id;
                 $result .= $this->child_start_string;
                 if (isset($this->data[$category_id])) {
                     $result .= $this->parent_start_string;
                 }

                 if ($level == 0) {
                     $result .= $this->root_start_string;
                 }
                 $result .= str_repeat($this->spacer_string, $this->spacer_multiplier * $level) . '<a href="' . zen_href_link(FILENAME_DEFAULT, 'cPath=' . $category_link) . '">';
                 $result .= $category['name'];
                 $result .= '</a>';

                 if ($level == 0) {
                     $result .= $this->root_end_string;
                 }

                 if (isset($this->data[$category_id])) {
                     $result .= $this->parent_end_string;
                 }

//        $result .= $this->child_end_string;

                 if (isset($this->data[$category_id]) && (($this->max_level == '0') || ($this->max_level > $level+1))) {
                     $result .= $this->buildBranch($category_id, $level+1, $category_link . '_');
                 }
                 $result .= $this->child_end_string;
             }
         }

         $result .= $this->parent_group_end_string;

         return $result;
     }
     public function buildTree()
     {
         return $this->buildBranch($this->root_category_id);
     }
 }
