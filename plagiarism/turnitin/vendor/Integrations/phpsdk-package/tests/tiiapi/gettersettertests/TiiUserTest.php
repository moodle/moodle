<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiRubric;
use Integrations\PhpSdk\TiiUser;
use Integrations\PhpSdk\TiiAssignment;

class TiiUserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiUser
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
        $this->object = new TiiUser;
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
    public function testSetUserId()
    {
        $expected = 12345;
        $this->object->setUserId($expected);
    }

    /**
     *
     */
    public function testGetUserId()
    {
        $expected = 12345;
        $this->object->setUserId($expected);
        $result = $this->object->getUserId();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetUserIds()
    {
        $expected = array(12345,67890);
        $this->object->setUserIds($expected);
    }

    /**
     *
     */
    public function testGetUserIds()
    {
        $expected = array(12345,67890);
        $this->object->setUserIds($expected);
        $result = $this->object->getUserIds();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetEmail()
    {
        $expected = "someflippingemail@vle.org.uk";
        $this->object->setEmail($expected);
    }

    /**
     *
     */
    public function testGetEmail()
    {
        $expected = "someflippingemail@vle.org.uk";
        $this->object->setEmail($expected);
        $result = $this->object->getEmail();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetFirstName()
    {
        $expected = "John";
        $this->object->setFirstName($expected);
    }

    /**
     *
     */
    public function testGetFirstName()
    {
        $expected = "John";
        $this->object->setFirstName($expected);
        $result = $this->object->getFirstName();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetLastName()
    {
        $expected = "Johnson";
        $this->object->setLastName($expected);
    }

    /**
     *
     */
    public function testGetLastName()
    {
        $expected = "Johnson";
        $this->object->setLastName($expected);
        $result = $this->object->getLastName();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetUserMessages()
    {
        $expected = 5;
        $this->object->setUserMessages($expected);
        $result = $this->object->getUserMessages();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetUserMessages()
    {
        $expected = 5;
        $this->object->setUserMessages($expected);
    }

    /**
     *
     */
    public function testSetDefaultRole()
    {
        $expected = "Learner";
        $this->object->setDefaultRole($expected);
    }

    /**
     *
     */
    public function testGetDefaultRole()
    {
        $input = "Student";
        $expected = "Learner";
        $this->object->setDefaultRole($input);
        $result = $this->object->getDefaultRole();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testGetInstructorDefaults()
    {
        $expected = '{"QuotedExcluded":1,"AuthorOriginalityAccess":0,"SubmitPapersTo":2,"SmallMatchExclusionThreshold":12,"SmallMatchExclusionType":2,"LateSubmissionsAllowed":0,' .
        '"InstitutionCheck":0,"PublicationsCheck":1,"ResubmissionRule":0,"SubmittedDocumentsCheck":1,"InternetCheck":1,"BibliographyExcluded":0}';
        $this->object->setInstructorDefaults($expected);
        $result = $this->object->getInstructorDefaults();

        $expected = new TiiAssignment();
        $expected->setQuotedExcluded(true);
        $expected->setAuthorOriginalityAccess(false);
        $expected->setSubmitPapersTo(2);
        $expected->setSmallMatchExclusionThreshold(12);
        $expected->setSmallMatchExclusionType(2);
        $expected->setLateSubmissionsAllowed(0);
        $expected->setInstitutionCheck(false);
        $expected->setPublicationsCheck(true);
        $expected->setResubmissionRule(0);
        $expected->setSubmittedDocumentsCheck(true);
        $expected->setInternetCheck(true);
        $expected->setBibliographyExcluded(false);



        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetInstructorDefaults()
    {
        $expected = '{"QuotedExcluded":1,"AuthorOriginalityAccess":0,"SubmitPapersTo":2,"SmallMatchExclusionThreshold":12,"SmallMatchExclusionType":2,"LateSubmissionsAllowed":0,' .
        '"InstitutionCheck":0,"PublicationsCheck":1,"ResubmissionRule":0,"SubmittedDocumentsCheck":1,"InternetCheck":1,"BibliographyExcluded":0}';
        $this->object->setInstructorDefaults($expected);
    }

    /**
     *
     */
    public function testGetDefaultLanguage()
    {
        $expected = "en_us";
        $this->object->setDefaultLanguage($expected);
        $result = $this->object->getDefaultLanguage();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetDefaultLanguage()
    {
        $expected = "en_us";
        $this->object->setDefaultLanguage($expected);
    }

    /**
     *
     */
    public function testGetAcceptedUserAgreement()
    {
        $expected = true;
        $this->object->setAcceptedUserAgreement($expected);
        $result = $this->object->getAcceptedUserAgreement();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetAcceptedUserAgreement()
    {
        $expected = true;
        $this->object->setAcceptedUserAgreement($expected);
    }

    /**
     *
     */
    public function testGetSetInstructorRubric()
    {
        $expected = new TiiRubric();
        $expected->setRubricId(1234);
        $expected->setRubricName("Test Rubric");

        $this->object->setInstructorRubrics('[{ "RubricId": 1234, "RubricName": "Test Rubric" }]');
        $rubric = $this->object->getInstructorRubrics();

        $this->assertEquals($expected->getRubricId(), $rubric[0]->getRubricId());
        $this->assertEquals($expected->getRubricName(), $rubric[0]->getRubricName());
    }

}
