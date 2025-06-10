<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * @ignore
 */
class Logger extends KLogger {

    public static $keeplogs = 10;

    const LOGLEVEL = 6;

    
    public function __construct( $filepath ) {
        if ( $filepath == null ) return false;
        $this->rotateLogs( $filepath );
        parent::setDateFormat( 'Y-m-d G:i:s O' );
        parent::__construct($filepath, self::LOGLEVEL);
    }
    
    private function rotateLogs( $filepath ) {
        if ( !file_exists( $filepath ) ) {
            mkdir( $filepath, 0777, true );
        }
        $dir=opendir( $filepath );
        $files=array();
        while ($entry=readdir( $dir )) {
            if ( substr( basename( $entry ) ,0 ,1 )!='.' AND substr_count(basename( $entry ),parent::PREFIX ) > 0 ) {
                $files[]=basename( $entry );
            }
        }
        sort( $files );
        for ($i=0; $i<count( $files ) - self::$keeplogs; $i++ ) {
            unlink( $filepath . '/' . $files[$i] );
        }
    }
    
}

