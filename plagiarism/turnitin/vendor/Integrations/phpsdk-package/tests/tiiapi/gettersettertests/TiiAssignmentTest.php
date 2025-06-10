<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiAssignment;

class TiiAssignmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiAssignment
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
        $this->object = new TiiAssignment();
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
    public function testSetClassId()
    {
        $expected = 12345;
        $this->object->setClassId($expected);
    }

    /**
     *
     */
    public function testGetClassId()
    {
        $expected = 12345;
        $this->object->setClassId($expected);
        $result = $this->object->getClassId();

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
    public function testSetAssignmentId()
    {
        $expected = 12345;
        $this->object->setAssignmentId($expected);
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
    public function testSetAssignmentIds()
    {
        $expected = array(12345,67890);
        $this->object->setAssignmentIds($expected);
    }

    /**
     *
     */
    public function testGetAssignmentIds()
    {
        $expected = array(12345,67890);
        $this->object->setAssignmentIds($expected);
        $result = $this->object->getAssignmentIds();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetStartDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setStartDate($expected);
    }

    /**
     *
     */
    public function testGetStartDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setStartDate($expected);
        $result = $this->object->getStartDate();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetDueDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDueDate($expected);
    }

    /**
     *
     */
    public function testGetDueDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setDueDate($expected);
        $result = $this->object->getDueDate();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetFeedbackReleaseDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setFeedbackReleaseDate($expected);
    }

    /**
     *
     */
    public function testGetFeedbackReleaseDate()
    {
        $expected = gmdate( "Y-m-d\TH:i:s\Z", strtotime( '+1 years' ) );
        $this->object->setFeedbackReleaseDate($expected);
        $result = $this->object->getFeedbackReleaseDate();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetInstructions()
    {
        $expected = "Some instructions";
        $this->object->setInstructions($expected);
    }

    /**
     *
     */
    public function testGetInstructions()
    {
        $expected = "Some instructions";
        $this->object->setInstructions($expected);
        $result = $this->object->getInstructions();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAuthorOriginalityAccess()
    {
        $expected = true;
        $this->object->setAuthorOriginalityAccess($expected);
    }

    /**
     *
     */
    public function testGetAuthorOriginalityAccess()
    {
        $expected = true;
        $this->object->setAuthorOriginalityAccess($expected);
        $result = $this->object->getAuthorOriginalityAccess();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmittedDocumentsCheck()
    {
        $expected = true;
        $this->object->setSubmittedDocumentsCheck($expected);
    }

    /**
     *
     */
    public function testGetSubmittedDocumentsCheck()
    {
        $expected = true;
        $this->object->setSubmittedDocumentsCheck($expected);
        $result = $this->object->getSubmittedDocumentsCheck();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetInternetCheck()
    {
        $expected = true;
        $this->object->setInternetCheck($expected);
    }

    /**
     *
     */
    public function testGetInternetCheck()
    {
        $expected = true;
        $this->object->setInternetCheck($expected);
        $result = $this->object->getInternetCheck();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetPublicationsCheck()
    {
        $expected = true;
        $this->object->setPublicationsCheck($expected);
    }

    /**
     *
     */
    public function testGetPublicationsCheck()
    {
        $expected = true;
        $this->object->setPublicationsCheck($expected);
        $result = $this->object->getPublicationsCheck();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetInstitutionCheck()
    {
        $expected = true;
        $this->object->setInstitutionCheck($expected);
    }

    /**
     *
     */
    public function testGetInstitutionCheck()
    {
        $expected = true;
        $this->object->setInstitutionCheck($expected);
        $result = $this->object->getInstitutionCheck();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetMaxGrade()
    {
        $expected = 100;
        $this->object->setMaxGrade($expected);
    }

    /**
     *
     */
    public function testGetMaxGrade()
    {
        $expected = 100;
        $this->object->setMaxGrade($expected);
        $result = $this->object->getMaxGrade();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetLateSubmissionsAllowed()
    {
        $expected = true;
        $this->object->setLateSubmissionsAllowed($expected);
    }

    /**
     *
     */
    public function testGetLateSubmissionsAllowed()
    {
        $expected = true;
        $this->object->setLateSubmissionsAllowed($expected);
        $result = $this->object->getLateSubmissionsAllowed();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmitPapersTo()
    {
        $expected = 0;
        $this->object->setSubmitPapersTo($expected);
    }

    /**
     *
     */
    public function testGetSubmitPapersTo()
    {
        $expected = 0;
        $this->object->setSubmitPapersTo($expected);
        $result = $this->object->getSubmitPapersTo();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetResubmissionRule()
    {
        $expected = 0;
        $this->object->setResubmissionRule($expected);
    }

    /**
     *
     */
    public function testGetResubmissionRule()
    {
        $expected = 0;
        $this->object->setResubmissionRule($expected);
        $result = $this->object->getResubmissionRule();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetBibliographyExcluded()
    {
        $expected = true;
        $this->object->setBibliographyExcluded($expected);
    }

    /**
     *
     */
    public function testGetBibliographyExcluded()
    {
        $expected = true;
        $this->object->setBibliographyExcluded($expected);
        $result = $this->object->getBibliographyExcluded();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetQuotedExcluded()
    {
        $expected = true;
        $this->object->setQuotedExcluded($expected);
    }

    /**
     *
     */
    public function testGetQuotedExcluded()
    {
        $expected = true;
        $this->object->setQuotedExcluded($expected);
        $result = $this->object->getQuotedExcluded();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSmallMatchExclusionType()
    {
        $expected = 2;
        $this->object->setSmallMatchExclusionType($expected);
    }

    /**
     *
     */
    public function testGetSmallMatchExclusionType()
    {
        $expected = 2;
        $this->object->setSmallMatchExclusionType($expected);
        $result = $this->object->getSmallMatchExclusionType();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSmallMatchExclusionThreshold()
    {
        $expected = 33;
        $this->object->setSmallMatchExclusionThreshold($expected);
    }

    /**
     *
     */
    public function testGetSmallMatchExclusionThreshold()
    {
        $expected = 33;
        $this->object->setSmallMatchExclusionThreshold($expected);
        $result = $this->object->getSmallMatchExclusionThreshold();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAnonymousMarking()
    {
        $expected = true;
        $this->object->setAnonymousMarking($expected);
    }

    /**
     *
     */
    public function testGetAnonymousMarking()
    {
        $expected = true;
        $this->object->setAnonymousMarking($expected);
        $result = $this->object->getAnonymousMarking();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetErater()
    {
        $expected = true;
        $this->object->setErater($expected);
    }

    /**
     *
     */
    public function testGetErater()
    {
        $expected = true;
        $this->object->setErater($expected);
        $result = $this->object->getErater();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterSpelling()
    {
        $expected = true;
        $this->object->setEraterSpelling($expected);
    }

    /**
     *
     */
    public function testGetEraterSpelling()
    {
        $expected = true;
        $this->object->setEraterSpelling($expected);
        $result = $this->object->getEraterSpelling();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterGrammar()
    {
        $expected = true;
        $this->object->setEraterGrammar($expected);
    }

    /**
     *
     */
    public function testGetEraterGrammar()
    {
        $expected = true;
        $this->object->setEraterGrammar($expected);
        $result = $this->object->getEraterGrammar();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterUsage()
    {
        $expected = true;
        $this->object->setEraterUsage($expected);
    }

    /**
     *
     */
    public function testGetEraterUsage()
    {
        $expected = true;
        $this->object->setEraterUsage($expected);
        $result = $this->object->getEraterUsage();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterMechanics()
    {
        $expected = true;
        $this->object->setEraterMechanics($expected);
    }

    /**
     *
     */
    public function testGetEraterMechanics()
    {
        $expected = true;
        $this->object->setEraterMechanics($expected);
        $result = $this->object->getEraterMechanics();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterStyle()
    {
        $expected = true;
        $this->object->setEraterStyle($expected);
    }

    /**
     *
     */
    public function testGetEraterStyle()
    {
        $expected = true;
        $this->object->setEraterStyle($expected);
        $result = $this->object->getEraterStyle();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterSpellingDictionary()
    {
        $expected = "en";
        $this->object->setEraterSpellingDictionary($expected);
    }

    /**
     *
     */
    public function testGetEraterSpellingDictionary()
    {
        $expected = "en";
        $this->object->setEraterSpellingDictionary($expected);
        $result = $this->object->getEraterSpellingDictionary();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterHandbook()
    {
        $expected = true;
        $this->object->setEraterHandbook($expected);
    }

    /**
     *
     */
    public function testGetEraterHandbook()
    {
        $expected = true;
        $this->object->setEraterHandbook($expected);
        $result = $this->object->getEraterHandbook();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterUsername()
    {
        $expected = 'test';
        $this->object->setEraterUsername($expected);
    }

    /**
     *
     */
    public function testGetEraterUsername()
    {
        $expected = 'test';
        $this->object->setEraterUsername($expected);
        $result = $this->object->getEraterUsername();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEraterPassword()
    {
        $expected = 'test';
        $this->object->setEraterPassword($expected);
    }

    /**
     *
     */
    public function testGetEraterPassword()
    {
        $expected = 'test';
        $this->object->setEraterPassword($expected);
        $result = $this->object->getEraterPassword();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetTranslatedMatching()
    {
        $expected = true;
        $this->object->setTranslatedMatching($expected);
    }

    /**
     *
     */
    public function testGetTranslatedMatching()
    {
        $expected = true;
        $this->object->setTranslatedMatching($expected);
        $result = $this->object->getTranslatedMatching();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetInstructorDefaults()
    {
        $expected = 12345;
        $this->object->setInstructorDefaults($expected);
    }

    /**
     *
     */
    public function testSetInstructorDefaults()
    {
        $expected = 12345;
        $this->object->setInstructorDefaults($expected);
        $result = $this->object->getInstructorDefaults();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetInstructorDefaultsSave()
    {
        $expected = 12345;
        $this->object->setInstructorDefaultsSave($expected);
    }

    /**
     *
     */
    public function testSetInstructorDefaultsSave()
    {
        $expected = 12345;
        $this->object->setInstructorDefaultsSave($expected);
        $result = $this->object->getInstructorDefaultsSave();

        $this->assertEquals($expected,$result);
    }
}
