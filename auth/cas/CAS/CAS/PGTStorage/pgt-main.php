<?php

/**
 * @file CAS/PGTStorage/pgt-main.php
 * Basic class for PGT storage
 */

/**
 * @class PGTStorage
 * The PGTStorage class is a generic class for PGT storage. This class should
 * not be instanciated itself but inherited by specific PGT storage classes.
 *
 * @author   Pascal Aubry <pascal.aubry at univ-rennes1.fr>
 *
 * @ingroup internalPGTStorage
 */

class PGTStorage
{
  /** 
   * @addtogroup internalPGTStorage
   * @{ 
   */

  // ########################################################################
  //  CONSTRUCTOR
  // ########################################################################
  
  /**
   * The constructor of the class, should be called only by inherited classes.
   *
   * @param $cas_parent the CASclient instance that creates the current object.
   *
   * @protected
   */
  function PGTStorage($cas_parent)
    {
      phpCAS::traceBegin();
      if ( !$cas_parent->isProxy() ) {
	phpCAS::error('defining PGT storage makes no sense when not using a CAS proxy'); 
      }
      phpCAS::traceEnd();
    }

  // ########################################################################
  //  DEBUGGING
  // ########################################################################
  
  /**
   * This virtual method returns an informational string giving the type of storage
   * used by the object (used for debugging purposes).
   *
   * @public
   */
  function getStorageType()
    {
      phpCAS::error(__CLASS__.'::'.__FUNCTION__.'() should never be called'); 
    }

  /**
   * This virtual method returns an informational string giving informations on the
   * parameters of the storage.(used for debugging purposes).
   *
   * @public
   */
  function getStorageInfo()
    {
      phpCAS::error(__CLASS__.'::'.__FUNCTION__.'() should never be called'); 
    }

  // ########################################################################
  //  ERROR HANDLING
  // ########################################################################
  
  /**
   * string used to store an error message. Written by PGTStorage::setErrorMessage(),
   * read by PGTStorage::getErrorMessage().
   *
   * @hideinitializer
   * @private
   * @deprecated not used.
   */
  var $_error_message=FALSE;

  /**
   * This method sets en error message, which can be read later by 
   * PGTStorage::getErrorMessage().
   *
   * @param $error_message an error message
   *
   * @protected
   * @deprecated not used.
   */
  function setErrorMessage($error_message)
    {
      $this->_error_message = $error_message;
    }

  /**
   * This method returns an error message set by PGTStorage::setErrorMessage().
   *
   * @return an error message when set by PGTStorage::setErrorMessage(), FALSE
   * otherwise.
   *
   * @public
   * @deprecated not used.
   */
  function getErrorMessage()
    {
      return $this->_error_message;
    }

  // ########################################################################
  //  INITIALIZATION
  // ########################################################################

  /**
   * a boolean telling if the storage has already been initialized. Written by 
   * PGTStorage::init(), read by PGTStorage::isInitialized().
   *
   * @hideinitializer
   * @private
   */
  var $_initialized = FALSE;

  /**
   * This method tells if the storage has already been intialized.
   *
   * @return a boolean
   *
   * @protected
   */
  function isInitialized()
    {
      return $this->_initialized;
    }

  /**
   * This virtual method initializes the object.
   *
   * @protected
   */
  function init()
    {
      $this->_initialized = TRUE;
    }

  // ########################################################################
  //  PGT I/O
  // ########################################################################

  /**
   * This virtual method stores a PGT and its corresponding PGT Iuo.
   * @note Should never be called.
   *
   * @param $pgt the PGT
   * @param $pgt_iou the PGT iou
   *
   * @protected
   */
  function write($pgt,$pgt_iou)
    {
      phpCAS::error(__CLASS__.'::'.__FUNCTION__.'() should never be called'); 
    }

  /**
   * This virtual method reads a PGT corresponding to a PGT Iou and deletes
   * the corresponding storage entry.
   * @note Should never be called.
   *
   * @param $pgt_iou the PGT iou
   *
   * @protected
   */
  function read($pgt_iou)
    {
      phpCAS::error(__CLASS__.'::'.__FUNCTION__.'() should never be called'); 
    }

  /** @} */
  
} 

// include specific PGT storage classes
include_once(dirname(__FILE__).'/pgt-file.php'); 
include_once(dirname(__FILE__).'/pgt-db.php');
  
?>