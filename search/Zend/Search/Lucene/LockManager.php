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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
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
        $lock = $lockDirectory->createFile(self::WRITE_LOCK_FILE);
        if (!$lock->lock(LOCK_EX)) {
            throw new Zend_Search_Lucene_Exception('Can\'t obtain exclusive index lock');
        }
        return $lock;
    }
    
    /**
     * Release exclusive write lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseWriteLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::WRITE_LOCK_FILE);
        $lock->unlock();
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
        $lock = $lockDirectory->createFile(self::READ_LOCK_FILE);
        if (!$lock->lock(LOCK_SH)) {
            throw new Zend_Search_Lucene_Exception('Can\'t obtain shared reading index lock');
        }
        return $lock;
    }
    
    /**
     * Release shared read lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);
        $lock->unlock();
    }

    /**
     * Escalate Read lock to exclusive level
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     * @return boolean
     */
    public static function escalateReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);
        
        // Try to escalate read lock
        if (!$lock->lock(LOCK_EX, true)) {
            // Restore lock state
            $lock->lock(LOCK_SH);
            return false;
        }
        return true;
    }

    /**
     * De-escalate Read lock to shared level
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function deEscalateReadLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::READ_LOCK_FILE);
        $lock->lock(LOCK_SH);
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
        $lock = $lockDirectory->createFile(self::OPTIMIZATION_LOCK_FILE);
        if (!$lock->lock(LOCK_EX, true)) {
            return false;
        }
        return $lock;
    }
    
    /**
     * Release exclusive optimization lock
     * 
     * @param Zend_Search_Lucene_Storage_Directory $lockDirectory
     */
    public static function releaseOptimizationLock(Zend_Search_Lucene_Storage_Directory $lockDirectory)
    {
        $lock = $lockDirectory->getFileObject(self::OPTIMIZATION_LOCK_FILE);
        $lock->unlock();
    }
    
}
