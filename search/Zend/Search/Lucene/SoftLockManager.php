<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Storage_Directory */
require_once 'Zend/Search/Lucene/Storage/Directory.php';

/** Zend_Search_Lucene_Storage_File */
require_once 'Zend/Search/Lucene/Storage/File.php';



/**
 * This is an utility class which provides index locks processing functionality
 * 
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_LockManager
{
    const WRITE_LOCK_FILE        = 'write.lock.file';
    const READ_LOCK_FILE         = 'read.lock.file';
    const EXCLUSIVE_READ_LOCK_FILE  = 'exread.lock.file';
    const OPTIMIZATION_LOCK_FILE = 'optimization.lock.file';
    
    /**
     * Obtain exclusive write lock on the index
     *
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     * @return Zend_Search_Lucene_Storage_File
     * @throws Zend_Search_Lucene_Exception
     */
    public static function obtainWriteLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        // echo "obtain WL ";
        if ($lockDirectory->fileExists(self::WRITE_LOCK_FILE)){
            throw new Zend_Search_Lucene_Exception('Can\'t obtain exclusive index lock : somebody else is writing');
        }
        try {
            $lock = $lockDirectory->createFile(self::WRITE_LOCK_FILE);
            return $lock;
        }
        catch(Zend_Search_Lucene_Exception $e) {
            throw new Zend_Search_Lucene_Exception('Can\'t obtain exclusive index lock');
        }
        return null;
    }
    
    /**
     * Release exclusive write lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseWriteLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        // echo "release WL ";
        $lockDirectory->deleteFile(self::WRITE_LOCK_FILE);
    }
    
    /**
     * Obtain shared read lock on the index
     * 
     * It doesn't block other read or update processes, but prevent index from the premature cleaning-up
     *
     * @param Zend_Search_Lucene_Storage_Directory $defaultLockDirectory
     * @return Zend_Search_Lucene_Storage_File
     * @throws Zend_Search_Lucene_Exception
     */
    public static function obtainReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        // echo "obtain RL ";

        // is being written. Can't read
        if ($lockDirectory->fileExists(self::WRITE_LOCK_FILE)){
            throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock : write in progress');
        }

        if ($lockDirectory->fileExists(self::EXCLUSIVE_READ_LOCK_FILE)){
            throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock : read lock operation in progress');
        } else {
            $lock = $lockDirectory->createFile(self::EXCLUSIVE_READ_LOCK_FILE);
        }

        try {
            if (!$lockDirectory->fileExists(self::READ_LOCK_FILE)){
                $accesscount = 0;
            } else {
                // get the existing lock file
                $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE, false);
                $accesscount = $lock->readBytes($lock->size());
                $lock->close();
                $lockDirectory->deleteFile(self::READ_LOCK_FILE);
            }            

            // raise the access counter
            $accesscount++;

            // store the access counter
            $lock = $lockDirectory->createFile(self::READ_LOCK_FILE);
            $lock->writeBytes($accesscount);
            $lock->flush();
            $lock->close();            
        }
        catch (Zend_Search_Lucene_Exception $e) {
            $lockDirectory->deleteFile(self::EXCLUSIVE_READ_LOCK_FILE);
            print_object($e);
            throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock');
        }

        $lockDirectory->deleteFile(self::EXCLUSIVE_READ_LOCK_FILE);
        return $lock;
    }
    
    /**
     * Release shared read lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {

        // get exclusive operation lock on read lock file.
        // echo "release RL ";
        if ($lockDirectory->fileExists(self::EXCLUSIVE_READ_LOCK_FILE)){
            throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock : read lock operation in progress');
        } else {
            $lock = $lockDirectory->createFile(self::EXCLUSIVE_READ_LOCK_FILE);
        }

        try {
            $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE, false);
            $accesscounter = $lock->readBytes($lock->size());
            $lock->close();

            $accesscounter--;

            $lockDirectory->deleteFile(self::READ_LOCK_FILE);

            if ($accesscounter > 1){ // we have'nt released all locks. Create the file again.
                $lock = $lockDirectory->createFile(self::READ_LOCK_FILE);
                $lock->writeBytes($accesscount);
                $lock->flush();
                $lock->close();            
            }                
        }
        catch(Zend_Search_Lucene_Exception $e){
            $lockDirectory->deleteFile(self::EXCLUSIVE_READ_LOCK_FILE);
            // accept failover here
            // throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock');
        }

        $lockDirectory->deleteFile(self::EXCLUSIVE_READ_LOCK_FILE);
    }

    /**
     * Escalate Read lock to exclusive level
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     * @return boolean
     */
    public static function escalateReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        // echo "escalate RL ";
        // get exclusive operation lock on read lock file.
        if ($lockDirectory->fileExists(self::EXCLUSIVE_READ_LOCK_FILE)){
            return false;
        } else {
            $lock = $lockDirectory->createFile(self::EXCLUSIVE_READ_LOCK_FILE);
            return true;
        }
    }

    /**
     * De-escalate Read lock to shared level
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function deEscalateReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        // echo "deescalate RL ";
        $lockDirectory->deleteFile(self::EXCLUSIVE_READ_LOCK_FILE);
    }

    /**
     * Obtain exclusive optimization lock on the index
     * 
     * Returns lock object on success and false otherwise (doesn't block execution)
     *
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     * @return mixed
     */
    public static function obtainOptimizationLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        if ($lockDirectory->fileExists(self::OPTIMIZATION_LOCK_FILE)){
            return false;
        }
        $lock = $lockDirectory->createFile(self::OPTIMIZATION_LOCK_FILE);
        return $lock;
    }
    
    /**
     * Release exclusive optimization lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseOptimizationLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lockDirectory->deleteFile(self::OPTIMIZATION_LOCK_FILE);
    }    
}
