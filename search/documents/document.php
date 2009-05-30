<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Michael Campanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @contributor Tatsuva Shirai on UTF-8 multibyte fixing
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* Base search document from which other module/block types can
* extend.
*/

/**
*
*/
abstract class SearchDocument extends Zend_Search_Lucene_Document {
    public function __construct(&$doc, &$data, $course_id, $group_id, $user_id, $path, $additional_keyset = null) {

         $encoding = 'UTF-8';

         //document identification and indexing
         $this->addField(Zend_Search_Lucene_Field::Keyword('docid', $doc->docid, $encoding));
         //document type : the name of the Moodle element that manages it
         $this->addField(Zend_Search_Lucene_Field::Keyword('doctype', $doc->documenttype, $encoding));
         //allows subclassing information from complex modules.
         $this->addField(Zend_Search_Lucene_Field::Keyword('itemtype', $doc->itemtype, $encoding));
         //caches the course context.
         $this->addField(Zend_Search_Lucene_Field::Keyword('course_id', $course_id, $encoding));
         //caches the originator's group.
         $this->addField(Zend_Search_Lucene_Field::Keyword('group_id', $group_id, $encoding));
         //caches the originator if any
         $this->addField(Zend_Search_Lucene_Field::Keyword('user_id', $user_id, $encoding));
         // caches the context of this information. i-e, the context in which this information
         // is being produced/attached. Speeds up the "check for access" process as context in 
         // which the information resides (a course, a module, a block, the site) is stable. 
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('context_id', $doc->contextid, $encoding));
         
         //data for document
         $this->addField(Zend_Search_Lucene_Field::Text('title', $doc->title, $encoding));
         $this->addField(Zend_Search_Lucene_Field::Text('author', $doc->author, $encoding));
         $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $doc->contents, $encoding));
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('url', $doc->url, $encoding));
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('date', $doc->date, $encoding));
         
         //additional data added on a per-module basis
         $this->addField(Zend_Search_Lucene_Field::Binary('data', serialize($data)));
         
         // adding a path allows the document to know where to find specific library calls
         // for checking access to a module or block content. The Lucene records should only
         // be responsible to bring back to that call sufficient and consistent information
         // in order to perform the check.
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('path', $path, $encoding));
         /*
         // adding a capability set required for viewing. -1 if no capability required.
         // the capability required for viewing is depending on the local situation
         // of the document. each module should provide this information when pushing
         // out search document structure. Although capability model should be kept flat
         // there is no exclusion some module or block developpers use logical combinations
         // of multiple capabilities in their code. This possibility should be left open here.
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('capabilities', $caps));
         */
         
         /*
         // Additional key set allows a module to ask for extensible criteria based search
         // depending on the module internal needs.
         */
         if (!empty($additional_keyset)){
            foreach($additional_keyset as $keyname => $keyvalue){
                $this->addField(Zend_Search_Lucene_Field::Keyword($keyname, $keyvalue, $encoding)); 
            }            
         }
    }
}

/*
abstract class SearchDocument extends Zend_Search_Lucene_Document {
    public function __construct(&$doc, &$data, $course_id, $group_id, $user_id, $path, $additional_keyset = null) {
         //document identification and indexing
         $this->addField(Zend_Search_Lucene_Field::Keyword('docid', $doc->docid));
         //document type : the name of the Moodle element that manages it
         $this->addField(Zend_Search_Lucene_Field::Keyword('doctype', $doc->documenttype));
         //allows subclassing information from complex modules.
         $this->addField(Zend_Search_Lucene_Field::Keyword('itemtype', $doc->itemtype));
         //caches the course context.
         $this->addField(Zend_Search_Lucene_Field::Keyword('course_id', $course_id));
         //caches the originator's group.
         $this->addField(Zend_Search_Lucene_Field::Keyword('group_id', $group_id));
         //caches the originator if any
         $this->addField(Zend_Search_Lucene_Field::Keyword('user_id', $user_id));
         // caches the context of this information. i-e, the context in which this information
         // is being produced/attached. Speeds up the "check for access" process as context in 
         // which the information resides (a course, a module, a block, the site) is stable. 
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('context_id', $doc->contextid));
         
         //data for document
         $this->addField(Zend_Search_Lucene_Field::Text('title', $doc->title));
         $this->addField(Zend_Search_Lucene_Field::Text('author', $doc->author));
         $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $doc->contents));
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('url', $doc->url));
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('date', $doc->date));
         
         //additional data added on a per-module basis
         $this->addField(Zend_Search_Lucene_Field::Binary('data', serialize($data)));
         
         // adding a path allows the document to know where to find specific library calls
         // for checking access to a module or block content. The Lucene records should only
         // be responsible to bring back to that call sufficient and consistent information
         // in order to perform the check.
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('path', $path));
         /*
         // adding a capability set required for viewing. -1 if no capability required.
         // the capability required for viewing is depending on the local situation
         // of the document. each module should provide this information when pushing
         // out search document structure. Although capability model should be kept flat
         // there is no exclusion some module or block developpers use logical combinations
         // of multiple capabilities in their code. This possibility should be left open here.
         $this->addField(Zend_Search_Lucene_Field::UnIndexed('capabilities', $caps));
         */
         
         /*
         // Additional key set allows a module to ask for extensible criteria based search
         // depending on the module internal needs.
         *
         if (!empty($additional_keyset)){
            foreach($additional_keyset as $keyname => $keyvalue){
                $this->addField(Zend_Search_Lucene_Field::Keyword($keyname, $keyvalue)); 
            }            
         }
    }
}*/

?>