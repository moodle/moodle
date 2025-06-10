<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiPseudoUser;
use Integrations\PhpSdk\TiiAssignment;

class TiiPseudoUserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiPseudoUser
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
        $this->object = new TiiPseudoUser( '@example.com' );
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
    public function testSetPseudoSalt()
    {
        $expected = 'SALT';
        $this->object->setPseudoSalt($expected);
    }

    /**
     *
     */
    public function testGetPseudoSalt()
    {
        $expected = 'SALT';
        $this->object->setPseudoSalt($expected);
        $result = $this->object->getPseudoSalt();

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
        $this->object->setPseudoSalt('SALT');
        $this->object->setEmail($expected);
    }

    /**
     *
     */
    public function testGetEmail()
    {

        $expected = "someflippingemail@vle.org.uk";

        // Test with @ in domain
        $this->object->setEmail($expected);
        $this->object->setPseudoSalt('SALT');
        $result = $this->object->getEmail();

        $email_string = $expected . $this->object->getPseudoSalt();
        $expected = sha1( $email_string ) . $this->object->getPseudoDomain();
        $this->assertEquals($expected,$result);

        // Test with no @ in domain
        $object = new TiiPseudoUser( 'example.com' );
        $object->setEmail($expected);
        $object->setPseudoSalt('SALT');
        $result = $object->getEmail();

        $email_string = $expected . $object->getPseudoSalt();
        $expected = sha1( $email_string ) . $object->getPseudoDomain();
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
        $expected = "Learner";
        $this->object->setDefaultRole($expected);
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
}
