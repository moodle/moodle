<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( __DIR__.'/soap.class.php' );
require_once( __DIR__.'/tiiclass.class.php' );
require_once( __DIR__.'/response.class.php' );
require_once( __DIR__.'/sdkexception.class.php' );

/**
 * @ignore
 */
class ClassSoap extends Soap {

    public $ns;

    public function __construct( $wsdl, $options ) {
        $this->ns = 'http://www.imsglobal.org/services/lis/cmsv1p0/wsdl11/sync/imscms_v1p0';
        parent::__construct( $wsdl, $options );
    }

    public function createClass( $class ) {
        try {
            $request = $this->buildClassRequest( $class );
            $soap = $this->createByProxyCourseSection( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $tiiClass = new TiiClass();
                $tiiClass->setClassId( $soap->sourcedId );
                $response->setClass( $tiiClass );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function updateClass( $class ) {
        try {
            $request = $this->buildClassRequest( $class, true );
            $soap = $this->updateCourseSection( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $tiiClass = new TiiClass();
                $response->setClass( $tiiClass );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readClass( $class ) {
        try {
            $soap = $this->readCourseSection( array( 'sourcedId' => $class->getClassId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $tiiClass = new TiiClass();
                $tiiClass->setClassId( $soap->courseSectionRecord->sourcedGUID->sourcedId );
                $tiiClass->setTitle( $soap->courseSectionRecord->courseSection->title->textString );
                $tiiClass->setEndDate( $soap->courseSectionRecord->courseSection->timeFrame->end );
                foreach ( $soap->courseSectionRecord->courseSection->extension->extensionField as $field ) {
                    $name = $field->fieldName;
                    $method = 'set'.$name;
                    if ( is_callable( array( $tiiClass, $method ) ) ) $tiiClass->$method( $field->fieldValue );
                }
                $response->setClass( $tiiClass );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readClasses( $class ) {
        try {
            $soap = $this->readCourseSections( array( 'sourcedIdSet' => array( 'sourcedId' => $class->getClassIds() ) ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $classes = array();
                if ( isset( $soap->courseSectionRecordSet->courseSectionRecord ) ) {
                    if (!is_array( $soap->courseSectionRecordSet->courseSectionRecord ) ) $soap->courseSectionRecordSet->courseSectionRecord = array( $soap->courseSectionRecordSet->courseSectionRecord );
                    foreach ( $soap->courseSectionRecordSet->courseSectionRecord as $record ) {
                        $tiiClass = new TiiClass();
                        $tiiClass->setClassId( $record->sourcedGUID->sourcedId );
                        $tiiClass->setTitle( $record->courseSection->title->textString );
                        $tiiClass->setEndDate( $record->courseSection->timeFrame->end );
                        foreach ( $record->courseSection->extension->extensionField as $field ) {
                            $name = $field->fieldName;
                            $method = 'set'.$name;
                            if ( is_callable( array( $tiiClass, $method ) ) ) $tiiClass->$method( $field->fieldValue );
                        }
                        $classes[] = $tiiClass;
                    }
                }
                $response->setClasses( $classes );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function deleteClass( $class ) {
        try {
            $this->deleteCourseSection( array( 'sourcedId' => $class->getClassId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $tiiClass = new TiiClass();
                $response->setClass( $tiiClass );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function findClasses( $class ) {
        try {
            $queryObject = array(
                'queryObject' => json_encode(
                    array(
                        'coursesection_title' => $class->getTitle(),
                        'date_from' => $class->getDateFrom(),
                        'integration_id' => $class->getIntegrationId(),
                        'user_id' => $class->getUserId(),
                        'user_role' => $class->getUserRole()
                        )
                    )
                );
            $soap = $this->discoverCourseSectionIds( $queryObject );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription(), parent::getLogPath() );
            } else {
                $tiiClass = new TiiClass();
                $classids = array();
                if ( isset( $soap->sourcedIdSet->sourcedId ) ) {
                    if ( !is_array( $soap->sourcedIdSet->sourcedId ) ) $soap->sourcedIdSet->sourcedId = array( $soap->sourcedIdSet->sourcedId );
                    foreach ( $soap->sourcedIdSet->sourcedId as $id ) {
                        $classids[] = $id;
                    }
                }
                $tiiClass->setClassIds( $classids );
                $response->setClass( $tiiClass );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    private function buildClassRequest( $class, $update = false ) {
        $request = array();
        if ( $update ) {
            $request['sourcedId'] = $class->getClassId();
        } else {
            $request['sourcedId'] = null;
        }
        $request['courseSectionRecord']['sourcedGUID']['sourcedId'] = $class->getClassId();
        $request['courseSectionRecord']['courseSection']['title']['language'] = parent::$lislanguage;
        $request['courseSectionRecord']['courseSection']['title']['textString'] = $class->getTitle();
        if ( $class->getEndDate() ) $request['courseSectionRecord']['courseSection']['timeFrame']['end'] = $class->getEndDate();
        return $request;
    }

}

//?>