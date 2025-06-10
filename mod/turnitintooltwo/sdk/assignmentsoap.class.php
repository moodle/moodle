<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once( __DIR__.'/soap.class.php' );
require_once( __DIR__.'/tiiassignment.class.php' );
require_once( __DIR__.'/tiipeermarkassignment.class.php' );
require_once( __DIR__.'/response.class.php' );
require_once( __DIR__.'/sdkexception.class.php' );

/**
 * @ignore
 */
class AssignmentSoap extends Soap {

    public static $extensionname_vocab = 'http://www.turnitin.com/static/source/media/turnitinvocabularyv1p0.xml';
    public static $extensionvalue_vocab = 'http://www.imsglobal.org/vdex/lis/omsv1p0/extensionvocabularyv1p0.xml';
    public $ns;

    public function __construct( $wsdl, $options ) {
        $this->ns = 'http://www.imsglobal.org/services/lis/oms1p0/wsdl11/sync/imsoms_v1p0';
        parent::__construct( $wsdl, $options );
    }

    public function createAssignment( $assignment ) {
        try {
            $request = $this->buildAssignmentRequest( $assignment );
            $soap = $this->createByProxyLineItem( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiAssignment = new TiiAssignment();
                $tiiAssignment->setAssignmentId( $soap->sourcedId );
                $response->setAssignment( $tiiAssignment );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readAssignment( $assignment ) {
        try {
            $soap = $this->readLineItem( array( 'sourcedId' => $assignment->getAssignmentId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiAssignment = new TiiAssignment();
                $tiiAssignment->setAssignmentId( $soap->lineItemRecord->sourcedGUID->sourcedId );
                $tiiAssignment->setTitle( $soap->lineItemRecord->lineItem->label );
                $tiiAssignment->setClassId( $soap->lineItemRecord->lineItem->context->contextIdentifier );
                foreach ( $soap->lineItemRecord->lineItem->extension->extensionField as $field ) {
                    $name = $field->fieldName;
                    $method = 'set'.$name;
                    $value = ( $name == 'PeermarkAssignments' ) ? $this->peermarkJSONToObject( $field->fieldValue ) : $field->fieldValue;
                    if ( is_callable( array( $tiiAssignment, $method ) ) ) $tiiAssignment->$method( $value );
                }
                $response->setAssignment( $tiiAssignment );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function readAssignments( $assignment ) {
        try {
            $soap = $this->readLineItems( array( 'sourcedIdSet' => array( 'sourcedId' => $assignment->getAssignmentIds() ) ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $assignments = array();
                if ( isset( $soap->lineItemRecordSet->lineItemRecord ) ) {
                    if ( !is_array( $soap->lineItemRecordSet->lineItemRecord ) ) $soap->lineItemRecordSet->lineItemRecord = array( $soap->lineItemRecordSet->lineItemRecord );
                    foreach ( $soap->lineItemRecordSet->lineItemRecord as $lineitem ) {
                        $tiiAssignment = new TiiAssignment();
                        $tiiAssignment->setAssignmentId( $lineitem->sourcedGUID->sourcedId );
                        $tiiAssignment->setTitle( $lineitem->lineItem->label );
                        $tiiAssignment->setClassId( $lineitem->lineItem->context->contextIdentifier );
                        foreach ( $lineitem->lineItem->extension->extensionField as $field ) {
                            $name = $field->fieldName;
                            $method = 'set'.$name;
                            $value = ( $name == 'PeermarkAssignments' ) ? $this->peermarkJSONToObject( $field->fieldValue ) : $field->fieldValue;
                            if ( is_callable( array( $tiiAssignment, $method ) ) ) $tiiAssignment->$method($value);
                        }
                        $assignments[] = $tiiAssignment;
                    }
                }
                $response->setAssignments( $assignments );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function updateAssignment( $assignment ) {
        try {
            $request = $this->buildAssignmentRequest( $assignment, true );
            $this->updateLineItem( $request );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiAssignment = new TiiAssignment();
                $response->setAssignment( $tiiAssignment );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function deleteAssignment( $assignment ) {
        try {
            $this->deleteLineItem( array( 'sourcedId' => $assignment->getAssignmentId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiAssignment = new TiiAssignment();
                $response->setAssignment( $tiiAssignment );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    public function findAssignments( $assignment ) {
        try {
            $soap = $this->readLineItemIdsForCourseSection( array( 'sectionSourcedId' => $assignment->getClassId() ) );
            $response = new Response( $this );
            if ( $response->getStatus() == 'error' ) {
                throw new TurnitinSDKException( $response->getStatusCode(), $response->getDescription() );
            } else {
                $tiiAssignment = new TiiAssignment();
                $assignmentids = array();
                if ( isset( $soap->sourcedIdSet->sourcedId ) ) {
                    if ( !is_array( $soap->sourcedIdSet->sourcedId ) ) $soap->sourcedIdSet->sourcedId = array( $soap->sourcedIdSet->sourcedId );
                    foreach ( $soap->sourcedIdSet->sourcedId as $id ) {
                        $assignmentids[] = $id;
                    }
                }
                $tiiAssignment->setAssignmentIds( $assignmentids );
                $response->setAssignment( $tiiAssignment );
            }
            return $response;
        } catch ( SoapFault $e ) {
            throw new TurnitinSDKException( $e->faultcode, $e->faultstring, parent::getLogPath() );
        }
    }

    private function buildAssignmentRequest( $assignment, $update = false ) {
        $request = array();
        if ( $update ) {
            $request['sourcedId'] = $assignment->getAssignmentId();
        } else {
            $request['sourcedId'] = null;
        }
        $request['lineItemRecord']['sourcedGUID']['sourcedId'] = $assignment->getAssignmentId();
        $request['lineItemRecord']['lineItem']['context']['contextIdentifier'] = $assignment->getClassId();
        $request['lineItemRecord']['lineItem']['context']['contextType'] = 'courseSection';
        $request['lineItemRecord']['lineItem']['label'] = $assignment->getTitle();

        $i = 0;
        foreach ($this->extensions as $name => $type) {
            $method = 'get'.$name;
            $value = null;
            if ( is_callable( array( $assignment, $method ) ) ) $value = $assignment->$method();
            $value = ( gettype( $value ) == 'boolean' ) ? (integer)$value : $value;
            if (!is_null($value)) {
                $request['lineItemRecord']['lineItem']['extension']['extensionField'][$i]['fieldName'] = $name;
                $request['lineItemRecord']['lineItem']['extension']['extensionField'][$i]['fieldType'] = $type;
                $request['lineItemRecord']['lineItem']['extension']['extensionField'][$i]['fieldValue'] = ( $name == 'PeermarkAssignments' ) ? $this->peermarkObjectToJSON($value) : $value;

                $i++;
            }
        }
        if ( $i > 0 ) {
            $request['lineItemRecord']['lineItem']['extension']['extensionNameVocabulary'] = self::$extensionname_vocab;
            $request['lineItemRecord']['lineItem']['extension']['extensionValueVocabulary'] = self::$extensionvalue_vocab;
        }

        return $request;
    }

    private function peermarkJSONToObject( $json ) {
        $pm_array = array();

        if ( empty( $json ) ) return array();

        $peermarkassignments = json_decode( $json );

        foreach ($peermarkassignments as $peermark_assignment) {
            $peermarkassignment = new TiiPeermarkAssignment();
            foreach ($peermark_assignment as $k => $v ) {
                $method = 'set'.$k;
                if (is_callable(array($peermarkassignment, $method))) {
                    $peermarkassignment->$method( $v );
                }
            }
            $pm_array[] = $peermarkassignment;
        }

        return $pm_array;
    }

    private function peermarkObjectToJSON( $pm_objectarray ) {
        $peermarkassignments = array();
        $pmreflectionclass = new ReflectionClass( 'TiiPeermarkAssignment' );

        foreach ($pm_objectarray as $peermarkassignment) {
            $pm_assignment = array();
            foreach ($pmreflectionclass->getMethods() as $method ) {
                if (!strstr($method->name,'get')) continue;
                $name = $method->name;
                $name = str_replace( 'get', '', $name );
                $method = 'get'.$name;
                if (is_callable(array($peermarkassignment, $method))) {
                    $pm_assignment[$name] = $peermarkassignment->$method();
                }
            }
            $peermarkassignments[] = $pm_assignment;
        }
        return json_encode( $peermarkassignments );
    }

}

//?>