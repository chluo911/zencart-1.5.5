<?php
/**
 * Temporary cache for sql
 *
 * @package classes
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Created by Data-Diggers.com http://www.data-diggers.com/
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Thu Apr 2 14:27:45 2015 -0400 Modified in v1.5.5 $
 *
 */
/**
 * QueryCache
 *
 */
 class QueryCache
 {
     public function __construct()
     {
         $this->queries = array();
     }

     // cache queries if and only if query is 'SELECT' statement
     // returns:
     //	TRUE - if and only if query has been stored in cache
     //	FALSE - otherwise
     public function cache($query, $result)
     {
         if ($this->isSelectStatement($query) === true) {
             $this->queries[$query] = $result;
         } else {
             return(false);
         }
         return(true);
     }

     public function getFromCache($query)
     {
         $ret = $this->queries[$query];
         mysqli_data_seek($ret, 0);
         return($ret);
     }

     public function inCache($query)
     {
         return(isset($this->queries[$query]));
     }

     public function isSelectStatement($q)
     {
         if (($q[0] == 's' || $q[0] == 'S')
                && ($q[1] == 'e' || $q[1] == 'E')
                && ($q[2] == 'l' || $q[2] == 'L')) {
             return(true);
         }
         return(false);
     }

     public function reset($query)
     {
         if ('ALL' == $query) {
             $this->queries = array();
             return false;
         }
         unset($this->queries[$query]);
     }
 }
