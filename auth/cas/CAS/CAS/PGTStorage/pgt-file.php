<?php

/**
 * @file CAS/PGTStorage/pgt-file.php
 * Basic class for PGT file storage
 */

/**
 * @class PGTStorageFile
 * The PGTStorageFile class is a class for PGT file storage. An instance of 
 * this class is returned by CASClient::SetPGTStorageFile().
 *
 * @author Pascal Aubry <pascal.aubry at univ-rennes1.fr>
 *
 * @ingroup internalPGTStorageFile
 */

class PGTStorageFile extends PGTStorage
{
  /** 
   * @addtogroup internalPGTStorageFile 
   * @{ 
   */

  /**
   * a string telling where PGT's should be stored on the filesystem. Written by
   * PGTStorageFile::PGTStorageFile(), read by getPath().
   *
   * @private
   */
  var $_path;

  /**
   * This method returns the name of the directory where PGT's should be stored 
   * on the filesystem.
   *
   * @return the name of a directory (with leading and trailing '/')
   *
   * @private
   */
  function getPath()
    {
      return $this->_path;
    }

  /**
   * a string telling the format to use to store PGT's (plain or xml). Written by
   * PGTStorageFile::PGTStorageFile(), read by getFormat().
   *
   * @private
   */
  var $_format;

  /**
   * This method returns the format to use when storing PGT's on the filesystem.
   *
   * @return a string corresponding to the format used (plain or xml).
   *
   * @private
   */
  function getFormat()
    {
      return $this->_format;
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
      return "file";
    }

  /**
   * This method returns an informational string giving informations on the
   * parameters of the storage.(used for debugging purposes).
   *
   * @return an informational string.
   * @public
   */
  function getStorageInfo()
    {
      return 'path=`'.$this->getPath().'\', format=`'.$this->getFormat().'\'';
    }

  // ########################################################################
  //  CONSTRUCTOR
  // ########################################################################
  
  /**
   * The class constructor, called by CASClient::SetPGTStorageFile().
   *
   * @param $cas_parent the CASClient instance that creates the object.
   * @param $format the format used to store the PGT's (`plain' and `xml' allowed).
   * @param $path the path where the PGT's should be stored
   *
   * @public
   */
  function PGTStorageFile($cas_parent,$format,$path)
    {
      phpCAS::traceBegin();
      // call the ancestor's constructor
      $this->PGTStorage($cas_parent);

      if (empty($format) ) $format = CAS_PGT_STORAGE_FILE_DEFAULT_FORMAT;
      if (empty($path) ) $path = CAS_PGT_STORAGE_FILE_DEFAULT_PATH;

      // check that the path is an absolute path
      if (getenv("OS")=="Windows_NT"){
      	
      	 if (!preg_match('`^[a-zA-Z]:`', $path)) {
	     	phpCAS::error('an absolute path is needed for PGT storage to file');
      	}
      	
      }
      else
      {
      
      	if ( $path[0] != '/' ) {
			phpCAS::error('an absolute path is needed for PGT storage to file');
      	}

      	// store the path (with a leading and trailing '/')      
      	$path = preg_replace('|[/]*$|','/',$path);
      	$path = preg_replace('|^[/]*|','/',$path);
      }
      
      $this->_path = $path;
      // check the format and store it
      switch ($format) {
      case CAS_PGT_STORAGE_FILE_FORMAT_PLAIN:
      case CAS_PGT_STORAGE_FILE_FORMAT_XML:
	$this->_format = $format;
	break;
      default:
	phpCAS::error('unknown PGT file storage format (`'.CAS_PGT_STORAGE_FILE_FORMAT_PLAIN.'\' and `'.CAS_PGT_STORAGE_FILE_FORMAT_XML.'\' allowed)');
      }
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
      phpCAS::traceEnd();      
    }

  // ########################################################################
  //  PGT I/O
  // ########################################################################

  /**
   * This method returns the filename corresponding to a PGT Iou.
   *
   * @param $pgt_iou the PGT iou.
   *
   * @return a filename
   * @private
   */
  function getPGTIouFilename($pgt_iou)
    {
      phpCAS::traceBegin();
      $filename = $this->getPath().$pgt_iou.'.'.$this->getFormat();
      phpCAS::traceEnd($filename);
      return $filename;
    }
  
  /**
   * This method stores a PGT and its corresponding PGT Iou into a file. Echoes a
   * warning on error.
   *
   * @param $pgt the PGT
   * @param $pgt_iou the PGT iou
   *
   * @public
   */
  function write($pgt,$pgt_iou)
    {
      phpCAS::traceBegin();
      $fname = $this->getPGTIouFilename($pgt_iou);
      if ( $f=fopen($fname,"w") ) {
	if ( fputs($f,$pgt) === FALSE ) {
	  phpCAS::error('could not write PGT to `'.$fname.'\'');
	}
	fclose($f);
      } else {
	phpCAS::error('could not open `'.$fname.'\'');
      }
      phpCAS::traceEnd();      
    }

  /**
   * This method reads a PGT corresponding to a PGT Iou and deletes the 
   * corresponding file.
   *
   * @param $pgt_iou the PGT iou
   *
   * @return the corresponding PGT, or FALSE on error
   *
   * @public
   */
  function read($pgt_iou)
    {
      phpCAS::traceBegin();
      $pgt = FALSE;
      $fname = $this->getPGTIouFilename($pgt_iou);
      if ( !($f=fopen($fname,"r")) ) {
	phpCAS::trace('could not open `'.$fname.'\'');
      } else {
	if ( ($pgt=fgets($f)) === FALSE ) {
	  phpCAS::trace('could not read PGT from `'.$fname.'\'');
	} 
	fclose($f);
      }

      // delete the PGT file
      @unlink($fname);

      phpCAS::traceEnd($pgt);
      return $pgt;
    }
  
  /** @} */
  
}

  
?>