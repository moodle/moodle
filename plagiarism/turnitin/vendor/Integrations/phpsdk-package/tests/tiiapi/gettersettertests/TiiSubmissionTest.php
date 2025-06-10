<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiSubmission;

class TiiSubmissionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiSubmission
     */
    protected $object;

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT,"\n" . __METHOD__ . "\n");
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TiiSubmission;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     *
     */
    public function testGetSubmissionId()
    {
        $expected = 12345;
        $this->object->setSubmissionId($expected);
        $result = $this->object->getSubmissionId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmissionId()
    {
        $expected = 12345;
        $this->object->setSubmissionId($expected);
    }

    /**
     *
     */
    public function testGetSubmissionIds()
    {
        $expected = array(12345,67890);
        $this->object->setSubmissionIds($expected);
        $result = $this->object->getSubmissionIds();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmissionIds()
    {
        $expected = array(12345,67890);
        $this->object->setSubmissionIds($expected);
    }

    /**
     *
     */
    public function testGetDateFrom()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDateFrom($expected);
        $result = $this->object->getDateFrom();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetDateFrom()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDateFrom($expected);
    }

    /**
     *
     */
    public function testGetAuthorUserId()
    {
        $expected = 12345;
        $this->object->setAuthorUserId($expected);
        $result = $this->object->getAuthorUserId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAuthorUserId()
    {
        $expected = 12345;
        $this->object->setAuthorUserId($expected);
    }

    /**
     *
     */
    public function testGetAssignmentId()
    {
        $expected = 12345;
        $this->object->setAssignmentId($expected);
        $result = $this->object->getAssignmentId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAssignmentId()
    {
        $expected = 12345;
        $this->object->setAssignmentId($expected);
    }

    /**
     *
     */
    public function testGetDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDate($expected);
    }

    /**
     *
     */
    public function testSetDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDate($expected);
        $result = $this->object->getDate();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetTitle()
    {
        $expected = "Test Title";
        $this->object->setTitle($expected);
        $result = $this->object->getTitle();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTitle()
    {
        $expected = "Test Title";
        $this->object->setTitle($expected);
    }

    /**
     *
     */
    public function testGetSubmitterUserId()
    {
        $expected = 12345;
        $this->object->setSubmitterUserId($expected);
        $result = $this->object->getSubmitterUserId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmitterUserId()
    {
        $expected = 12345;
        $this->object->setSubmitterUserId($expected);
    }

    /**
     *
     */
    public function testSetSubmitter()
    {
        $expected = 12345;
        $this->object->setSubmitter($expected);

        // check it is an alias for submitter Id
        $result = $this->object->getSubmitterUserId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetGrade()
    {
        $expected = 50;
        $this->object->setGrade($expected);
        $result = $this->object->getGrade();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetGrade()
    {
        $expected = 50;
        $this->object->setGrade($expected);
    }

    /**
     *
     */
    public function testGetOverallSimilarity()
    {
        $expected = 20;
        $this->object->setOverallSimilarity($expected);
        $result = $this->object->getOverallSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetOverallSimilarity()
    {
        $expected = 20;
        $this->object->setOverallSimilarity($expected);
    }

    /**
     *
     */
    public function testGetInternetSimilarity()
    {
        $expected = 20;
        $this->object->setInternetSimilarity($expected);
        $result = $this->object->getInternetSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetInternetSimilarity()
    {
        $expected = 20;
        $this->object->setInternetSimilarity($expected);
    }

    /**
     *
     */
    public function testGetPublicationsSimilarity()
    {
        $expected = 20;
        $this->object->setPublicationsSimilarity($expected);
        $result = $this->object->getPublicationsSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetPublicationsSimilarity()
    {
        $expected = 20;
        $this->object->setPublicationsSimilarity($expected);
    }

    /**
     *
     */
    public function testGetSubmittedDocumentsSimilarity()
    {
        $expected = 20;
        $this->object->setSubmittedDocumentsSimilarity($expected);
        $result = $this->object->getSubmittedDocumentsSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmittedDocumentsSimilarity()
    {
        $expected = 20;
        $this->object->setSubmittedDocumentsSimilarity($expected);
    }

    /**
     *
     */
    public function testGetTranslatedOverallSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedOverallSimilarity($expected);
        $result = $this->object->getTranslatedOverallSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTranslatedOverallSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedOverallSimilarity($expected);
    }

    /**
     *
     */
    public function testGetTranslatedInternetSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedInternetSimilarity($expected);
        $result = $this->object->getTranslatedInternetSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTranslatedInternetSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedInternetSimilarity($expected);
    }

    /**
     *
     */
    public function testGetTranslatedPublicationsSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedPublicationsSimilarity($expected);
        $result = $this->object->getTranslatedPublicationsSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTranslatedPublicationsSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedPublicationsSimilarity($expected);
    }

    /**
     *
     */
    public function testGetTranslatedSubmittedDocumentsSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedSubmittedDocumentsSimilarity($expected);
        $result = $this->object->getTranslatedSubmittedDocumentsSimilarity();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTranslatedSubmittedDocumentsSimilarity()
    {
        $expected = 20;
        $this->object->setTranslatedSubmittedDocumentsSimilarity($expected);
    }

    /**
     *
     */
    public function testGetAuthorLastViewedFeedback()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '-1 years' ) );
        $this->object->setAuthorLastViewedFeedback($expected);
        $result = $this->object->getAuthorLastViewedFeedback();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAuthorLastViewedFeedback()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '-1 years' ) );
        $this->object->setAuthorLastViewedFeedback($expected);
    }

    /**
     *
     */
    public function testGetVoiceComment()
    {
        $expected = true;
        $this->object->setVoiceComment($expected);
        $result = $this->object->getVoiceComment();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetVoiceComment()
    {
        $expected = true;
        $this->object->setVoiceComment($expected);
    }

    /**
     *
     */
    public function testGetFeedbackExists()
    {
        $expected = true;
        $this->object->setFeedbackExists($expected);
        $result = $this->object->getFeedbackExists();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetFeedbackExists()
    {
        $expected = true;
        $this->object->setFeedbackExists($expected);
    }

    /**
     *
     */
    public function testGetRole()
    {
        $input    = "Student";
        $expected = "Learner";
        $this->object->setRole($input);
        $result = $this->object->getRole();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetRole()
    {
        $expected = "Learner";
        $this->object->setRole($expected);
    }

    /**
     *
     */
    public function testGetSubmissionDataText()
    {
        $expected = "Some text lol";
        $this->object->setSubmissionDataText($expected);
        $result = $this->object->getSubmissionDataText();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmissionDataText()
    {
        $expected = "Some text lol";
        $this->object->setSubmissionDataText($expected);
    }

    /**
     *
     */
    public function testGetSubmissionDataPath()
    {
        $expected = "./myFile.doc";
        $this->object->setSubmissionDataPath($expected);
        $result = $this->object->getSubmissionDataPath();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmissionDataPath()
    {
        $expected = "./myFile.doc";
        $this->object->getSubmissionDataPath($expected);
    }

    /**
     *
     */
    public function testGetSubmissionDataWeb()
    {
        $url      = "http://localhost/test";
        $filename = "./myFile.doc";
        $this->object->setSubmissionDataWeb($url, $filename);

        $result = $this->object->getSubmissionDataUrl();
        $this->assertEquals($url,$result);

        $result = $this->object->getSubmissionDataFilename();
        $this->assertEquals($filename,$result);
    }

    /**
     *
     */
    public function testGetTextExtract()
    {
        $expected = "Some data extract lol";
        $this->object->setTextExtract($expected);
        $result = $this->object->getTextExtract();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTextExtract()
    {
        $expected = "Some data extract lol";
        $this->object->setTextExtract($expected);
    }

    /**
     *
     */
    public function testGetCustomCSS()
    {
        $expected = "http://url.to/custom.css";
        $this->object->setCustomCSS($expected);
        $result = $this->object->getCustomCSS();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetCustomCSS()
    {
        $expected = "http://url.to/custom.css";
        $this->object->setCustomCSS($expected);
    }

    /**
     *
     */
    public function testGetXmlResponse()
    {
        $expected = true;
        $this->object->setXmlResponse($expected);
        $result = $this->object->getXmlResponse();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetXmlResponse()
    {
        $expected = true;
        $this->object->setXmlResponse($expected);
    }

    /**
     *
     */
    public function testGetAnonymous()
    {
        $expected = false;
        $this->object->setAnonymous($expected);
        $result = $this->object->getAnonymous();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAnonymous()
    {
        $expected = false;
        $this->object->setAnonymous($expected);
    }

    /**
     *
     */
    public function testGetAnonymousRevealReason()
    {
        $expected = "A bad reason";
        $this->object->setAnonymousRevealReason($expected);
        $result = $this->object->getAnonymousRevealReason();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAnonymousRevealReason()
    {
        $expected = "A bad reason";
        $this->object->setAnonymousRevealReason($expected);
    }

    /**
     *
     */
    public function testGetAnonymousRevealUser()
    {
        $expected = 12345;
        $this->object->setAnonymousRevealUser($expected);
        $result = $this->object->getAnonymousRevealUser();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAnonymousRevealUser()
    {
        $expected = 12345;
        $this->object->setAnonymousRevealUser($expected);
    }

    /**
     *
     */
    public function testGetAnonymousRevealDateTime()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '-1 years' ) );
        $this->object->setAnonymousRevealDateTime($expected);
        $result = $this->object->getAnonymousRevealDateTime();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAnonymousRevealDateTime()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '-1 years' ) );
        $this->object->setAnonymousRevealDateTime($expected);
    }
}
