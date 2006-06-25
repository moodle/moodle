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
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Index_SegmentWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_Writer
{
    /**
     * @todo Implement segment merger
     * @todo Implement mergeFactor, minMergeDocs, maxMergeDocs usage.
     * @todo Implement Analyzer substitution
     * @todo Implement Zend_Search_Lucene_Storage_DirectoryRAM and Zend_Search_Lucene_Storage_FileRAM to use it for
     *       temporary index files
     * @todo Directory lock processing
     */

    /**
     * File system adapter.
     *
     * @var Zend_Search_Lucene_Storage_Directory
     */
    private $_directory = null;


    /**
     * Index version
     * Counts how often the index has been changed by adding or deleting docs
     *
     * @var integer
     */
    private $_version;

    /**
     * Segment name counter.
     * Used to name new segments .
     *
     * @var integer
     */
    private $_segmentNameCounter;

    /**
     * Number of the segments in the index
     *
     * @var inteher
     */
    private $_segments;

    /**
     * Determines how often segment indices
     * are merged by addDocument().
     *
     * @var integer
     */
    public $mergeFactor;

    /**
     * Determines the minimal number of documents required before
     * the buffered in-memory documents are merging and a new Segment
     * is created.
     *
     * @var integer
     */
    public $minMergeDocs;

    /**
     * Determines the largest number of documents ever merged by addDocument().
     *
     * @var integer
     */
    public $maxMergeDocs;

    /**
     * List of the segments, created by index writer
     * Array of Zend_Search_Lucene_Index_SegmentInfo objects
     *
     * @var array
     */
    private $_newSegments;

    /**
     * Current segment to add documents
     *
     * @var Zend_Search_Lucene_Index_SegmentWriter
     */
    private $_currentSegment;

    /**
     * List of indexfiles extensions
     *
     * @var array
     */
    private static $_indexExtensions = array('.cfs' => '.cfs',
                                             '.fnm' => '.fnm',
                                             '.fdx' => '.fdx',
                                             '.fdt' => '.fdt',
                                             '.tis' => '.tis',
                                             '.tii' => '.tii',
                                             '.frq' => '.frq',
                                             '.prx' => '.prx',
                                             '.tvx' => '.tvx',
                                             '.tvd' => '.tvd',
                                             '.tvf' => '.tvf',
                                             '.del' => '.del'  );

    /**
     * Opens the index for writing
     *
     * IndexWriter constructor needs Directory as a parameter. It should be
     * a string with a path to the index folder or a Directory object.
     * Second constructor parameter create is optional - true to create the
     * index or overwrite the existing one.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param boolean $create
     */
    public function __construct(Zend_Search_Lucene_Storage_Directory $directory, $create = false)
    {
        $this->_directory = $directory;

        if ($create) {
            foreach ($this->_directory->fileList() as $file) {
                if ($file == 'deletable' ||
                    $file == 'segments'  ||
                    isset(self::$_indexExtensions[ substr($file, strlen($file)-4)]) ||
                    preg_match('/\.f\d+$/i', $file) /* matches <segment_name>.f<decimal_nmber> file names */) {
                        $this->_directory->deleteFile($file);
                    }
            }
            $segmentsFile = $this->_directory->createFile('segments');
            $segmentsFile->writeInt((int)0xFFFFFFFF);
            // write version
            $segmentsFile->writeLong(0);
            // write name counter
            $segmentsFile->writeInt(0);
            // write segment counter
            $segmentsFile->writeInt(0);

            $deletableFile = $this->_directory->createFile('deletable');
            // write counter
            $deletableFile->writeInt(0);

            $this->_version            = 0;
            $this->_segmentNameCounter = 0;
            $this->_segments           = 0;
        } else {
            $segmentsFile = $this->_directory->getFileObject('segments');
            $format = $segmentsFile->readInt();
            if ($format != (int)0xFFFFFFFF) {
                throw new Zend_Search_Lucene_Exception('Wrong segments file format');
            }

            // read version
            $this->_version            = $segmentsFile->readLong();
            // read counter
            $this->_segmentNameCounter = $segmentsFile->readInt();
            // read segment counter
            $this->_segments           = $segmentsFile->readInt();
        }

        $this->_newSegments = array();
        $this->_currentSegment = null;
    }

    /**
     * Adds a document to this index.
     *
     * @param Zend_Search_Lucene_Document $document
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        if ($this->_currentSegment === null) {
            $this->_currentSegment =
                new Zend_Search_Lucene_Index_SegmentWriter($this->_directory, $this->_newSegmentName());
        }
        $this->_currentSegment->addDocument($document);
        $this->_version++;
    }



    /**
     * Update segments file by adding current segment to a list
     * @todo !!!!!Finish the implementation
     *
     * @throws Zend_Search_Lucene_Exception
     */
    private function _updateSegments()
    {
        $segmentsFile   = $this->_directory->getFileObject('segments');
        $newSegmentFile = $this->_directory->createFile('segments.new');

        $newSegmentFile->writeInt((int)0xFFFFFFFF);
        $newSegmentFile->writeLong($this->_version);
        $newSegmentFile->writeInt($this->_segmentNameCounter);

        $this->_segments += count($this->_newSegments);
        $newSegmentFile->writeInt($this->_segments);

        $segmentsFile->seek(20);
        $newSegmentFile->writeBytes($segmentsFile->readBytes($this->_directory->fileLength('segments') - 20));

        foreach ($this->_newSegments as $segmentName => $segmentInfo) {
            $newSegmentFile->writeString($segmentName);
            $newSegmentFile->writeInt($segmentInfo->count());
        }

        $this->_directory->renameFile('segments.new', 'segments');
    }


    /**
     * Commit current changes
     * returns array of new segments
     *
     * @return array
     */
    public function commit()
    {
        if ($this->_currentSegment !== null) {
            $newSegment = $this->_currentSegment->close();
            if ($newSegment !== null) {
                $this->_newSegments[$newSegment->getName()] = $newSegment;
            }
            $this->_currentSegment = null;
        }

        if (count($this->_newSegments) != 0) {
            $this->_updateSegments();
        }

        $result = $this->_newSegments;
        $this->_newSegments = array();

        return $result;
    }


    /**
     * Merges the provided indexes into this index.
     *
     * @param array $readers
     * @return void
     */
    public function addIndexes($readers)
    {
        /**
         * @todo implementation
         */
    }


    /**
     * Returns the number of documents currently in this index.
     *
     * @return integer
     */
    public function docCount($readers)
    {
        /**
         * @todo implementation
         */
    }


    /**
     * Flushes all changes to an index and closes all associated files.
     *
     */
    public function close()
    {
        /**
         * @todo implementation
         */
    }


    /**
     * Merges all segments together into a single segment, optimizing
     * an index for search.
     *
     * return void
     */
    public function optimize()
    {
        /**
         * @todo implementation
         */
    }

    /**
     * Get name for new segment
     *
     * @return string
     */
    private function _newSegmentName()
    {
        return '_' . base_convert($this->_segmentNameCounter++, 10, 36);
    }

}
