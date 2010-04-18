<?php

/**
 * @file CAS/PGTStorage/pgt-db.php
 * Basic class for PGT database storage
 */

/**
 * @class PGTStorageDB
 * The PGTStorageDB class is a class for PGT database storage. An instance of 
 * this class is returned by CASClient::SetPGTStorageDB().
 *
 * @author Pascal Aubry <pascal.aubry at univ-rennes1.fr>
 *
 * @ingroup internalPGTStorageDB
 */

class PGTStorageDB extends PGTStorage
{
  /** 
   * @addtogroup internalPGTStorageDB
   * @{ 
   */

  /**
   * a string representing a PEAR DB URL to connect to the database. Written by
   * PGTStorageDB::PGTStorageDB(), read by getURL().
   *
   * @hideinitializer
   * @private
   */
  var $_url='';

  /**
   * This method returns the PEAR DB URL to use to connect to the database.
   *
   * @return a PEAR DB URL
   *
   * @private
   */
  function getURL()
    {
      return $this->_url;
    }

  /**
   * The handle of the connection to the database where PGT's are stored. Written by
   * PGTStorageDB::init(), read by getLink().
   *
   * @hideinitializer
   * @private
   */
  var $_link = null;

  /**
   * This method returns the handle of the connection to the database where PGT's are 
   * stored.
   *
   * @return a handle of connection.
   *
   * @private
   */
  function getLink()
    {
      return $this->_link;
    }

  /**
   * The name of the table where PGT's are stored. Written by 
   * PGTStorageDB::PGTStorageDB(), read by getTable().
   *
   * @hideinitializer
   * @private
   */
  var $_table = '';

  /**
   * This method returns the name of the table where PGT's are stored.
   *
   * @return the name of a table.
   *
   * @private
   */
  function getTable()
    {
      return $this->_table;
    }

  // ########################################################################
  //  DEBUGGING
  // ########################################################################
  
  /**
   * This method returns an informational string giving the type of storage
   * used by the object (used for debugging purposes).
   *
   * @return an informational string.
   * @public
   */
  function getStorageType()
    {
      return "database";
    }

  /**
   * This method returns an informational string giving informations on the
   * parameters of the storage.(used for debugging purposes).
   *
   * @public
   */
  function getStorageInfo()
    {
      return 'url=`'.$this->getURL().'\', table=`'.$this->getTable().'\'';
    }

  // ########################################################################
  //  CONSTRUCTOR
  // ########################################################################
  
  /**
   * The class constructor, called by CASClient::SetPGTStorageDB().
   *
   * @param $cas_parent the CASClient instance that creates the object.
   * @param $user the user to access the data with
   * @param $password the user's password
   * @param $database_type the type of the database hosting the data
   * @param $hostname the server hosting the database
   * @param $port the port the server is listening on
   * @param $database the name of the database
   * @param $table the name of the table storing the data
   *
   * @public
   */
  function PGTStorageDB($cas_parent,$user,$password,$database_type,$hostname,$port,$database,$table)
    {
      phpCAS::traceBegin();

      // call the ancestor's constructor
      $this->PGTStorage($cas_parent);

      if ( empty($database_type) ) $database_type = CAS_PGT_STORAGE_DB_DEFAULT_DATABASE_TYPE;
      if ( empty($hostname) ) $hostname = CAS_PGT_STORAGE_DB_DEFAULT_HOSTNAME;
      if ( $port==0 ) $port = CAS_PGT_STORAGE_DB_DEFAULT_PORT;
      if ( empty($database) ) $database = CAS_PGT_STORAGE_DB_DEFAULT_DATABASE;
      if ( empty($table) ) $table = CAS_PGT_STORAGE_DB_DEFAULT_TABLE;

      // build and store the PEAR DB URL
      $this->_url = $database_type.':'.'//'.$user.':'.$password.'@'.$hostname.':'.$port.'/'.$database;

      // XXX should use setURL and setTable
      phpCAS::traceEnd();
    }
  
  // ########################################################################
  //  INITIALIZATION
  // ########################################################################
  
  /**
   * This method is used to initialize the storage. Halts on error.
   *
   * @public
   */
  function init()
    {
      phpCAS::traceBegin();
      // if the storage has already been initialized, return immediatly
      if ( $this->isInitialized() )
		return;
      // call the ancestor's method (mark as initialized)
      parent::init();
      
	  //include phpDB library (the test was introduced in release 0.4.8 for 
	  //the integration into Tikiwiki).
	  if (!class_exists('DB')) {
		include_once('DB.php');
	  }

      // try to connect to the database
      $this->_link = DB::connect($this->getURL());
      if ( DB::isError($this->_link) ) {
	phpCAS::error('could not connect to database ('.DB::errorMessage($this->_link).')');
      }
      var_dump($this->_link);
      phpCAS::traceBEnd();
    }

  /** @} */
}

?>