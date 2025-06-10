<?php
/* @ignore
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Integrations\PhpSdk;

/**
 * @ignore
 */
class SubmissionSoap extends Soap
{

    public static $extensionname_vocab = 'http://www.turnitin.com/static/source/media/turnitinvocabularyv1p0.xml';
    public static $extensionvalue_vocab = 'http://www.imsglobal.org/vdex/lis/omsv1p0/extensionvocabularyv1p0.xml';
    public $ns;

    public function __construct($wsdl, $options)
    {
        $this->ns = 'http://www.imsglobal.org/services/lis/oms1p0/wsdl11/sync/imsoms_v1p0';
        parent::__construct($wsdl, $options);
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readSubmission($submission)
    {
        try {
            $soap = $this->readResult(array('sourcedId' => $submission->getSubmissionId()));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $translatedScore = null;
                $tiiSubmission->setSubmissionId($soap->resultRecord->sourcedGUID->sourcedId);
                $tiiSubmission->setTitle($soap->resultRecord->result->resultValue->label);
                $tiiSubmission->setAssignmentId($soap->resultRecord->result->lineItemSourcedId);
                $tiiSubmission->setAuthorUserId($soap->resultRecord->result->personSourcedId);
                $tiiSubmission->setDate($soap->resultRecord->result->date);
                $tiiSubmission->setOverallSimilarity($soap->resultRecord->result->resultScore->textString);
                $translatedScore = $this->checkForTranslatedSimScore($soap->resultRecord->result->extension->extensionField);
                if (!is_null($translatedScore)) {
                    $tiiSubmission->setTranslatedOverallSimilarity($translatedScore);
                }
                foreach ($soap->resultRecord->result->extension->extensionField as $field) {
                    $name = $field->fieldName;
                    $method = 'set'.$name;
                    if (is_callable(array($tiiSubmission, $method))) {
                        $tiiSubmission->$method($field->fieldValue);
                    }
                }
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function readSubmissions($submission)
    {
        try {
            $soap = $this->readResults(array(
                'sourcedIdSet' => array(
                    'sourcedId' => $submission->getSubmissionIds()
                )
            ));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $submissions = array();
                $translatedScore = null;
                if (isset($soap->resultRecordSet->resultRecord)) {
                    if (!is_array($soap->resultRecordSet->resultRecord)) {
                        $soap->resultRecordSet->resultRecord = array($soap->resultRecordSet->resultRecord);
                    }
                    foreach ($soap->resultRecordSet->resultRecord as $submission) {
                        $tiiSubmission = new TiiSubmission();
                        $tiiSubmission->setSubmissionId($submission->sourcedGUID->sourcedId);
                        $tiiSubmission->setTitle($submission->result->resultValue->label);
                        $tiiSubmission->setAssignmentId($submission->result->lineItemSourcedId);
                        $tiiSubmission->setAuthorUserId($submission->result->personSourcedId);
                        $tiiSubmission->setDate($submission->result->date);
                        $tiiSubmission->setOverallSimilarity($submission->result->resultScore->textString);
                        $translatedScore = $this->checkForTranslatedSimScore($submission->result->extension->extensionField);
                        if (!is_null($translatedScore)) {
                            $tiiSubmission->setTranslatedOverallSimilarity($translatedScore);
                        }
                        foreach ($submission->result->extension->extensionField as $field) {
                            $name = $field->fieldName;
                            $method = 'set'.$name;
                            if (is_callable(array($tiiSubmission, $method))) {
                                $tiiSubmission->$method($field->fieldValue);
                            }
                        }
                        $submissions[] = $tiiSubmission;
                    }
                }
                $response->setSubmissions($submissions);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function deleteSubmission($submission)
    {
        try {
            $this->deleteResult(array(
                'sourcedId' => $submission->getSubmissionId()
            ));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function findSubmissions($submission)
    {
        try {
            $soap = $this->readResultIdsForLineItem(array(
                'lineItemSourcedid' => $submission->getAssignmentId()
            ));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $submissionids = array();
                if (isset($soap->sourcedIdSet->sourcedId)) {
                    if (!is_array($soap->sourcedIdSet->sourcedId)) {
                        $soap->sourcedIdSet->sourcedId = array($soap->sourcedIdSet->sourcedId);
                    }
                    foreach ($soap->sourcedIdSet->sourcedId as $id) {
                        $submissionids[] = $id;
                    }
                }
                $tiiSubmission->setSubmissionIds($submissionids);
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function findRecentSubmissions($submission)
    {
        try {
            $query = json_encode(array(
                'lineitem_sourcedid' => $submission->getAssignmentId(),
                'date_from' => $submission->getDateFrom()
            ));
            $soap = $this->discoverResultIds(array('queryObject' => $query));
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $submissionids = array();
                if (isset($soap->sourcedIdSet->sourcedId)) {
                    if (!is_array($soap->sourcedIdSet->sourcedId)) {
                        $soap->sourcedIdSet->sourcedId = array($soap->sourcedIdSet->sourcedId);
                    }
                    foreach ($soap->sourcedIdSet->sourcedId as $id) {
                        $submissionids[] = $id;
                    }
                }
                $tiiSubmission->setSubmissionIds($submissionids);
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function updateSubmission($submission)
    {
        try {
            $request = $this->buildSubmissionRequest($submission, true);
            $this->updateResult($request);
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return Response
     * @throws TurnitinApiException
     * @throws TurnitinSDKException
     */
    public function createSubmission($submission)
    {
        try {
            $request = array();
            $request['resultRecord']['sourcedGUID']['sourcedId'] = '';
            $request['resultRecord']['result']['personSourcedId'] = $submission->getAuthorUserId();
            $request['resultRecord']['result']['lineItemSourcedId'] = $submission->getAssignmentId();
            $request['resultRecord']['result']['extension']['extensionNameVocabulary'] = self::$extensionname_vocab;
            $request['resultRecord']['result']['extension']['extensionValueVocabulary'] = self::$extensionvalue_vocab;
            $request['resultRecord']['result']['extension']['extensionField'][0]['fieldName'] = 'Submitter';
            $request['resultRecord']['result']['extension']['extensionField'][0]['fieldType'] = 'Integer';
            $request['resultRecord']['result']['extension']['extensionField'][0]['fieldValue'] = $submission->getSubmitterUserId();
            $soap = $this->createByProxyResult($request);
            $response = new Response($this);
            if ($response->getStatus() == 'error') {
                throw new TurnitinApiException(
                    $response->getStatusCode(),
                    $response->getDescription()
                );
            } else {
                $tiiSubmission = new TiiSubmission();
                $tiiSubmission->setSubmissionId($soap->sourcedId);
                $response->setSubmission($tiiSubmission);
            }
            return $response;
        } catch (\SoapFault $e) {
            $this->logresponse = true;
            throw new TurnitinSDKException(
                $e->faultcode,
                $e->faultstring,
                parent::getLogPath(),
                $this
            );
        }
    }

    /**
     * @param $submission
     * @return array
     */
    private function buildSubmissionRequest($submission)
    {
        $request = array();
        $request['sourcedId'] = $submission->getSubmissionId();
        $request['resultRecord']['sourcedGUID']['sourcedId'] = $submission->getSubmissionId();
        $request['resultRecord']['result']['resultValue']['label'] = $submission->getTitle();
        $request['resultRecord']['result']['lineItemSourcedId'] = $submission->getAssignmentId();

        $i = 0;
        foreach ($this->extensions as $name => $type) {
            $method = 'get'.$name;
            $value = null;
            if (is_callable(array($submission, $method))) {
                $value = $submission->$method();
            }
            $value = (gettype($value) == 'boolean') ? (integer)$value : $value;
            if (!is_null($value)) {
                $request['resultRecord']['result']['extension']['extensionField'][$i]['fieldName'] = $name;
                $request['resultRecord']['result']['extension']['extensionField'][$i]['fieldType'] = $type;
                $request['resultRecord']['result']['extension']['extensionField'][$i]['fieldValue'] = $value;

                $i++;
            }
        }
        if ($i > 0) {
            $request['resultRecord']['result']['extension']['extensionNameVocabulary'] = self::$extensionname_vocab;
            $request['resultRecord']['result']['extension']['extensionValueVocabulary'] = self::$extensionvalue_vocab;
        }

        return $request;
    }

    /**
     * @param $array
     * @return string
     */
    private function checkForTranslatedSimScore($array) {
        foreach ($array as $object) {
            if ( ($object->fieldName == "TranslatedOverallSimilarity") && (!is_null($object->fieldValue)) ) {
                return $object->fieldValue;
            }
        }
        return null;
    }
}
