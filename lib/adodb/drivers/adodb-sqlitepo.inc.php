<?php
/**
 * SQLite Portable driver.
 *
 * Make it more similar to other database drivers. The main differences are
 * - When selecting (joining) multiple tables, in assoc mode the table
 *   names are included in the assoc keys in the "sqlite" driver.
 *   In "sqlitepo" driver, the table names are stripped from the returned
 *   column names. When this results in a conflict,  the first field gets
 *   preference.
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 * @author Herman Kuiper <herman@ozuzo.net>
 */

if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR.'/drivers/adodb-sqlite.inc.php');

class ADODB_sqlitepo extends ADODB_sqlite {
   var $databaseType = 'sqlitepo';
}

/*--------------------------------------------------------------------------------------
       Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordset_sqlitepo extends ADORecordset_sqlite {

   var $databaseType = 'sqlitepo';

   // Modified to strip table names from returned fields
   function _fetch($ignore_fields=false)
   {
      $this->fields = array();
      $fields = @sqlite_fetch_array($this->_queryID,$this->fetchMode);
      if(is_array($fields))
         foreach($fields as $n => $v)
         {
            if(($p = strpos($n, ".")) !== false)
               $n = substr($n, $p+1);
            $this->fields[$n] = $v;
         }

      return !empty($this->fields);
   }
}
