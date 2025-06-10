<?php

require_once(__DIR__ . '/../utilmethods.php');
require_once(__DIR__ . '/../testconsts.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');

use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiUser;
use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TiiAssignment;

class InstructorDefaultsTest extends PHPUnit_Framework_TestCase {
    protected static $sdk;
    protected static $tiiClass;
    protected static $studentOne;
    protected static $studentTwo;
    protected static $instructorOne;
    protected static $instructorTwo;
    protected static $instructorThree;
    protected static $instructorFour;
    protected $assignmentTeardownIds;
    protected $classTeardownIds;

    private static $classtitle = "InstructorDefaultsTest Class";

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT,"\n" . __METHOD__ . "\n");
        self::$sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);
        self::$sdk->setDebug(false);

        // create a class all assignment s will be made to
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        self::$tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // add members to the class
        self::$instructorTwo   = UtilMethods::getUser("instructortwophpsdk@vle.org.uk");
        self::$instructorThree = UtilMethods::getUser("instructorthreephpsdk@vle.org.uk");
        self::$instructorFour  = UtilMethods::getUser("brandnewuserphpsdk@vle.org.uk");

        $membershipInstructorTwo = new TiiMembership();
        $membershipInstructorTwo->setClassId(self::$tiiClass->getClassId());
        $membershipInstructorTwo->setUserId(self::$instructorTwo->getUserId());
        $membershipInstructorTwo->setRole("Instructor");

        $membershipInstructorThree = new TiiMembership();
        $membershipInstructorThree->setClassId(self::$tiiClass->getClassId());
        $membershipInstructorThree->setUserId(self::$instructorThree->getUserId());
        $membershipInstructorThree->setRole("Instructor");

        $membershipInstructorFour = new TiiMembership();
        $membershipInstructorFour->setClassId(self::$tiiClass->getClassId());
        $membershipInstructorFour->setUserId(self::$instructorFour->getUserId());
        $membershipInstructorFour->setRole("Instructor");

        self::$sdk->createMembership($membershipInstructorTwo);
        self::$sdk->createMembership($membershipInstructorThree);
        self::$sdk->createMembership($membershipInstructorFour);
    }

    protected function setUp()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        // create/reset teardown arrays
        $this->assignmentTeardownIds = array();
        $this->classTeardownIds = array();
    }

    protected function tearDown()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        // TODO cleardown things in cleardown arrays
    }

    public function testCreateAssignmentUsingInstructorDefaultsSave()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // read instructor defaults for "instructortwophpsdk@vle.org.uk"
        $userToFind = new TiiUser();
        $userToFind->setEmail("instructortwophpsdk@vle.org.uk");
        $resultUser = self::$sdk->findUser($userToFind)->getUser();

        // get the user instructor defaults
        $defaults = self::$sdk->readUser($resultUser)->getUser()->getInstructorDefaults();

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave($resultUser->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        if ($defaults == null) {
            // create a normal assignment and save the defaults
            $assignmentToCreate->setQuotedExcluded(true);
            $assignmentToCreate->setAuthorOriginalityAccess(true);
            $assignmentToCreate->setSubmitPapersTo(0);
            $assignmentToCreate->setSmallMatchExclusionThreshold(20);
            $assignmentToCreate->setSmallMatchExclusionType(1);
            $assignmentToCreate->setLateSubmissionsAllowed(true);
            $assignmentToCreate->setInstitutionCheck(false);
            $assignmentToCreate->setPublicationsCheck(true);
            $assignmentToCreate->setResubmissionRule(1);
            $assignmentToCreate->setSubmittedDocumentsCheck(true);
            $assignmentToCreate->setInternetCheck(false);
            $assignmentToCreate->setBibliographyExcluded(true);
        } else {
            // read the defaults and set them to something else in the assignment
            $assignmentToCreate->setQuotedExcluded(!$defaults->getQuotedExcluded());
            $assignmentToCreate->setAuthorOriginalityAccess(!$defaults->getAuthorOriginalityAccess());
            $assignmentToCreate->setSubmitPapersTo($defaults->getSubmitPapersTo() == 0 ? 1 : 0);

            $exclusion_threshold = $defaults->getSmallMatchExclusionThreshold();
            $assignmentToCreate->setSmallMatchExclusionThreshold($exclusion_threshold == 20 ? 15 : 20);
            $assignmentToCreate->setSmallMatchExclusionType($defaults->getSmallMatchExclusionType()==1?2:1);
            $assignmentToCreate->setLateSubmissionsAllowed(!$defaults->getLateSubmissionsAllowed());
            $assignmentToCreate->setInstitutionCheck(!$defaults->getInstitutionCheck());
            $assignmentToCreate->setPublicationsCheck(!$defaults->getPublicationsCheck());
            $assignmentToCreate->setResubmissionRule($defaults->getResubmissionRule()==1?2:1);
            $assignmentToCreate->setSubmittedDocumentsCheck(!$defaults->getSubmittedDocumentsCheck());
            $assignmentToCreate->setInternetCheck(!$defaults->getInternetCheck());
            $assignmentToCreate->setBibliographyExcluded(!$defaults->getBibliographyExcluded());
        }

        $response = self::$sdk->createAssignment($assignmentToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $resultAssignment = self::$sdk->readAssignment($response->getAssignment())->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($assignmentToCreate->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($assignmentToCreate->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($assignmentToCreate->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals($assignmentToCreate->getAuthorOriginalityAccess(), $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToCreate->getInternetCheck(), $resultAssignment->getInternetCheck());
        $this->assertEquals($assignmentToCreate->getPublicationsCheck(), $resultAssignment->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals($assignmentToCreate->getLateSubmissionsAllowed(), $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $resultAssignment->getSubmitPapersTo());
        $this->assertEquals($assignmentToCreate->getResubmissionRule(), $resultAssignment->getResubmissionRule());
        $this->assertEquals($assignmentToCreate->getBibliographyExcluded(), $resultAssignment->getBibliographyExcluded());
        $this->assertEquals($assignmentToCreate->getQuotedExcluded(), $resultAssignment->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser($resultUser)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // check the user defaults for the instructor
        $instructor = self::$sdk->readuser($resultUser)->getUser();

        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals($assignmentToCreate->getAuthorOriginalityAccess(), $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToCreate->getInternetCheck(), $savedDefaults->getInternetCheck());
        $this->assertEquals($assignmentToCreate->getPublicationsCheck(), $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals($assignmentToCreate->getLateSubmissionsAllowed(), $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals($assignmentToCreate->getResubmissionRule(), $savedDefaults->getResubmissionRule());
        $this->assertEquals($assignmentToCreate->getBibliographyExcluded(), $savedDefaults->getBibliographyExcluded());
        $this->assertEquals($assignmentToCreate->getQuotedExcluded(), $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());

        return $savedDefaults;
    }

    /**
     *
     * @depends testCreateAssignmentUsingInstructorDefaultsSave
     */
    public function testCreateAssignmentUsingInstructorDefaults($defaults)
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // read instructor defaults for "instructortwophpsdk@vle.org.uk"
        $userToFind = new TiiUser();
        $userToFind->setEmail("instructortwophpsdk@vle.org.uk");
        $resultUser = self::$sdk->findUser($userToFind)->getUser();

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave($resultUser->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        $assignmentToCreate->setInstructorDefaults($resultUser->getUserId());

        $response = self::$sdk->createAssignment($assignmentToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting assignment
        $resultAssignment = self::$sdk->readAssignment($response->getAssignment())->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($assignmentToCreate->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($assignmentToCreate->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($assignmentToCreate->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals($defaults->getAuthorOriginalityAccess(), $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals($defaults->getSubmittedDocumentsCheck(), $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals($defaults->getInternetCheck(), $resultAssignment->getInternetCheck());
        $this->assertEquals($defaults->getPublicationsCheck(), $resultAssignment->getPublicationsCheck());
        $this->assertEquals($defaults->getInstitutionCheck(), $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals($defaults->getLateSubmissionsAllowed(), $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals($defaults->getSubmitPapersTo(), $resultAssignment->getSubmitPapersTo());
        $this->assertEquals($defaults->getResubmissionRule(), $resultAssignment->getResubmissionRule());
        $this->assertEquals($defaults->getBibliographyExcluded(), $resultAssignment->getBibliographyExcluded());
        $this->assertEquals($defaults->getQuotedExcluded(), $resultAssignment->getQuotedExcluded());
        $this->assertEquals($defaults->getSmallMatchExclusionType(), $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals($defaults->getSmallMatchExclusionThreshold(), $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());
    }

    public function testCreateAssignmentUsingAndSavingInstructorDefaults()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // read instructor defaults for "instructortwophpsdk@vle.org.uk"
        $userToFind = new TiiUser();
        $userToFind->setEmail("instructortwophpsdk@vle.org.uk");
        $resultUser = self::$sdk->findUser($userToFind)->getUser();

        // get the user instructor defaults
        $defaults = self::$sdk->readUser($resultUser)->getUser()->getInstructorDefaults();

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave($resultUser->getUserId());
        $assignmentToCreate->setInstructorDefaults($resultUser->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        if ($defaults == null) {
            // create a normal assignment and save the defaults
            $assignmentToCreate->setQuotedExcluded(true);
            $assignmentToCreate->setAuthorOriginalityAccess(true);
            $assignmentToCreate->setSubmitPapersTo(0);
            $assignmentToCreate->setSmallMatchExclusionThreshold(20);
            $assignmentToCreate->setSmallMatchExclusionType(1);
            $assignmentToCreate->setLateSubmissionsAllowed(true);
            $assignmentToCreate->setInstitutionCheck(false);
            $assignmentToCreate->setPublicationsCheck(true);
            $assignmentToCreate->setResubmissionRule(1);
            $assignmentToCreate->setSubmittedDocumentsCheck(true);
            $assignmentToCreate->setInternetCheck(false);
            $assignmentToCreate->setBibliographyExcluded(true);
        } else {
            // read the defaults and set them to something else in the assignment
            $assignmentToCreate->setQuotedExcluded(!$defaults->getQuotedExcluded());
            $assignmentToCreate->setAuthorOriginalityAccess(!$defaults->getAuthorOriginalityAccess());
            $assignmentToCreate->setSubmitPapersTo($defaults->getSubmitPapersTo() == 0 ? 1 : 0);

            $exclusion_threshold = $defaults->getSmallMatchExclusionThreshold();
            $assignmentToCreate->setSmallMatchExclusionThreshold($exclusion_threshold == 20 ? 15 : 20);
            $assignmentToCreate->setSmallMatchExclusionType($defaults->getSmallMatchExclusionType() == 1 ? 2 : 1);
            $assignmentToCreate->setLateSubmissionsAllowed(!$defaults->getLateSubmissionsAllowed());
            $assignmentToCreate->setInstitutionCheck(!$defaults->getInstitutionCheck());
            $assignmentToCreate->setPublicationsCheck(!$defaults->getPublicationsCheck());
            $assignmentToCreate->setResubmissionRule($defaults->getResubmissionRule() == 1 ? 2 : 1);
            $assignmentToCreate->setSubmittedDocumentsCheck(!$defaults->getSubmittedDocumentsCheck());
            $assignmentToCreate->setInternetCheck(!$defaults->getInternetCheck());
            $assignmentToCreate->setBibliographyExcluded(!$defaults->getBibliographyExcluded());
        }

        $response = self::$sdk->createAssignment($assignmentToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $resultAssignment = self::$sdk->readAssignment($response->getAssignment())->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($assignmentToCreate->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($assignmentToCreate->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($assignmentToCreate->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals($assignmentToCreate->getAuthorOriginalityAccess(), $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToCreate->getInternetCheck(), $resultAssignment->getInternetCheck());
        $this->assertEquals($assignmentToCreate->getPublicationsCheck(), $resultAssignment->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals($assignmentToCreate->getLateSubmissionsAllowed(), $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $resultAssignment->getSubmitPapersTo());
        $this->assertEquals($assignmentToCreate->getResubmissionRule(), $resultAssignment->getResubmissionRule());
        $this->assertEquals($assignmentToCreate->getBibliographyExcluded(), $resultAssignment->getBibliographyExcluded());
        $this->assertEquals($assignmentToCreate->getQuotedExcluded(), $resultAssignment->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser($resultUser)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // check the user defaults for the instructor
        $instructor = self::$sdk->readuser($resultUser)->getUser();

        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals($assignmentToCreate->getAuthorOriginalityAccess(), $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToCreate->getInternetCheck(), $savedDefaults->getInternetCheck());
        $this->assertEquals($assignmentToCreate->getPublicationsCheck(), $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals($assignmentToCreate->getLateSubmissionsAllowed(), $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals($assignmentToCreate->getResubmissionRule(), $savedDefaults->getResubmissionRule());
        $this->assertEquals($assignmentToCreate->getBibliographyExcluded(), $savedDefaults->getBibliographyExcluded());
        $this->assertEquals($assignmentToCreate->getQuotedExcluded(), $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());
    }

    public function testCreateAssignmentUsingInstructorDefaultsWhereUserHasNoInstructorDefaultsSet()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // read instructor defaults for "instructorthreephpsdk@vle.org.uk"
        $userToFind = new TiiUser();
        $userToFind->setEmail("instructorthreephpsdk@vle.org.uk");
        $resultUser = self::$sdk->findUser($userToFind)->getUser();

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        $assignmentToCreate->setInstructorDefaults($resultUser->getUserId());

        $response = self::$sdk->createAssignment($assignmentToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting assignment
        $resultAssignment = self::$sdk->readAssignment($response->getAssignment())->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($resultAssignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($resultAssignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($resultAssignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(false, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(1, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(1, $resultAssignment->getInternetCheck());
        $this->assertEquals(1, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(true, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(0, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(1, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(0, $resultAssignment->getResubmissionRule());
        $this->assertEquals(0, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(0, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());
    }

    public function testCreateAssignmentSavingSomePropertiesToInstrucorDefaults()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // read instructor defaults for "instructortwophpsdk@vle.org.uk"
        $userToFind = new TiiUser();
        $userToFind->setEmail("instructortwophpsdk@vle.org.uk");
        $resultUser = self::$sdk->findUser($userToFind)->getUser();

        // get the user instructor defaults
        $defaults = self::$sdk->readUser($resultUser)->getUser()->getInstructorDefaults();

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave($resultUser->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        if ($defaults == null) {
            // create a normal assignment and save the defaults
            $assignmentToCreate->setSubmitPapersTo(0);
            $assignmentToCreate->setSmallMatchExclusionThreshold(20);
            $assignmentToCreate->setSmallMatchExclusionType(1);
            $assignmentToCreate->setInstitutionCheck(false);
            $assignmentToCreate->setSubmittedDocumentsCheck(true);
        } else {
            // read the defaults and set them to something else in the assignment
            $assignmentToCreate->setSubmitPapersTo($defaults->getSubmitPapersTo() == 0 ? 1 : 0);

            $exclusion_threshold = $defaults->getSmallMatchExclusionThreshold();
            $assignmentToCreate->setSmallMatchExclusionThreshold($exclusion_threshold == 20 ? 15 : 20);
            $assignmentToCreate->setSmallMatchExclusionType($defaults->getSmallMatchExclusionType() == 1 ? 2 : 1);
            $assignmentToCreate->setInstitutionCheck(!$defaults->getInstitutionCheck());
            $assignmentToCreate->setSubmittedDocumentsCheck(!$defaults->getSubmittedDocumentsCheck());
        }

        $response = self::$sdk->createAssignment($assignmentToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $resultAssignment = self::$sdk->readAssignment($response->getAssignment())->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($assignmentToCreate->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($assignmentToCreate->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($assignmentToCreate->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(false, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(true, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(false, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(0, $resultAssignment->getResubmissionRule());
        $this->assertEquals(false, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(false, $resultAssignment->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser($resultUser)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // check the user defaults for the instructor
        $instructor = self::$sdk->readuser($resultUser)->getUser();

        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals(false, $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToCreate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals(true, $savedDefaults->getInternetCheck());
        $this->assertEquals(true, $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToCreate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals(false, $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToCreate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals(0, $savedDefaults->getResubmissionRule());
        $this->assertEquals(false, $savedDefaults->getBibliographyExcluded());
        $this->assertEquals(false, $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToCreate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());
    }

    public function testUpdateAssignmentUsingInstructorDefaultsSave()
    {
        // first create an assignment using saving instructor defaults
        // so we know what they are.
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        self::$sdk->createAssignment($assignmentToCreate);

        // now an assignment has been made saving instructor defaults we
        // create another one that is not the same as the one just created.
        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        $assignmentToUpdate = self::$sdk->createAssignment($assignment)->getAssignment();

        // now update the assignment when using instructor defaults
        // which should have no affect
        $assignmentToUpdate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToUpdate->setQuotedExcluded(true);
        $assignmentToUpdate->setAuthorOriginalityAccess(true);
        $assignmentToUpdate->setSubmitPapersTo(0);
        $assignmentToUpdate->setSmallMatchExclusionThreshold(20);
        $assignmentToUpdate->setSmallMatchExclusionType(1);
        $assignmentToUpdate->setLateSubmissionsAllowed(true);
        $assignmentToUpdate->setInstitutionCheck(false);
        $assignmentToUpdate->setPublicationsCheck(true);
        $assignmentToUpdate->setResubmissionRule(1);
        $assignmentToUpdate->setSubmittedDocumentsCheck(true);
        $assignmentToUpdate->setInternetCheck(false);
        $assignmentToUpdate->setBibliographyExcluded(true);
        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment is the same as before
        $resultAssignment = self::$sdk->readAssignment($assignmentToUpdate)->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($startDate, $resultAssignment->getStartDate());
        $this->assertEquals($dueDate, $resultAssignment->getDueDate());
        $this->assertEquals($postDate, $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(true, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(true, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(false, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(false, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(true, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(0, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(1, $resultAssignment->getResubmissionRule());
        $this->assertEquals(true, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(true, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(1, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(20, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser(self::$instructorTwo)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // read the user to make sure instructor defaults were'nt saved
        $instructor = self::$sdk->readUser(self::$instructorTwo)->getUser();

        // check instructor defaults
        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals($assignmentToUpdate->getAuthorOriginalityAccess(), $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToUpdate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToUpdate->getInternetCheck(), $savedDefaults->getInternetCheck());
        $this->assertEquals($assignmentToUpdate->getPublicationsCheck(), $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToUpdate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals($assignmentToUpdate->getLateSubmissionsAllowed(), $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToUpdate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals($assignmentToUpdate->getResubmissionRule(), $savedDefaults->getResubmissionRule());
        $this->assertEquals($assignmentToUpdate->getBibliographyExcluded(), $savedDefaults->getBibliographyExcluded());
        $this->assertEquals($assignmentToUpdate->getQuotedExcluded(), $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());
    }

    public function testUpdateAssignmentUsingInstructorDefaults()
    {
        // first create an assignment using saving instructor defaults
        // so we know what they are.
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        $assignmentToCreate->setQuotedExcluded(true);
        $assignmentToCreate->setAuthorOriginalityAccess(true);
        $assignmentToCreate->setSubmitPapersTo(0);
        $assignmentToCreate->setSmallMatchExclusionThreshold(20);
        $assignmentToCreate->setSmallMatchExclusionType(1);
        $assignmentToCreate->setLateSubmissionsAllowed(true);
        $assignmentToCreate->setInstitutionCheck(false);
        $assignmentToCreate->setPublicationsCheck(true);
        $assignmentToCreate->setResubmissionRule(1);
        $assignmentToCreate->setSubmittedDocumentsCheck(true);
        $assignmentToCreate->setInternetCheck(false);
        $assignmentToCreate->setBibliographyExcluded(true);

        self::$sdk->createAssignment($assignmentToCreate);

        // now an assignment has been made saving instructor defaults we
        // create another one that is not the same as the one just created.
        $assignment = new TiiAssignment();
        $assignment->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        $assignmentToUpdate = self::$sdk->createAssignment($assignment)->getAssignment();

        // now update the assignment when using instructor defaults
        // which should have no affect
        $assignmentToUpdate->setInstructorDefaults(self::$instructorTwo->getUserId());
        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment is the same as before
        $resultAssignment = self::$sdk->readAssignment($assignmentToUpdate)->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($startDate, $resultAssignment->getStartDate());
        $this->assertEquals($dueDate, $resultAssignment->getDueDate());
        $this->assertEquals($postDate, $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(false, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(true, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(true, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(true, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(false, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(1, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(0, $resultAssignment->getResubmissionRule());
        $this->assertEquals(false, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(false, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());
    }

    public function testUpdateAssignmentUsingAndSavingInstructorDefaults()
    {
        // first create an assignment using saving instructor defaults
        // so we know what they are.
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        self::$sdk->createAssignment($assignmentToCreate);

        // now an assignment has been made saving instructor defaults we
        // create another one that is not the same as the one just created.
        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        $assignmentToUpdate = self::$sdk->createAssignment($assignment)->getAssignment();

        // now update the assignment when using instructor defaults
        // which should have no affect
        $assignmentToUpdate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToUpdate->setInstructorDefaults(self::$instructorTwo->getUserId());
        $assignmentToUpdate->setQuotedExcluded(true);
        $assignmentToUpdate->setAuthorOriginalityAccess(true);
        $assignmentToUpdate->setSubmitPapersTo(0);
        $assignmentToUpdate->setSmallMatchExclusionThreshold(20);
        $assignmentToUpdate->setSmallMatchExclusionType(1);
        $assignmentToUpdate->setLateSubmissionsAllowed(true);
        $assignmentToUpdate->setInstitutionCheck(false);
        $assignmentToUpdate->setPublicationsCheck(true);
        $assignmentToUpdate->setResubmissionRule(1);
        $assignmentToUpdate->setSubmittedDocumentsCheck(true);
        $assignmentToUpdate->setInternetCheck(false);
        $assignmentToUpdate->setBibliographyExcluded(true);
        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment is the same as before
        $resultAssignment = self::$sdk->readAssignment($assignmentToUpdate)->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($startDate, $resultAssignment->getStartDate());
        $this->assertEquals($dueDate, $resultAssignment->getDueDate());
        $this->assertEquals($postDate, $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(true, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(true, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(false, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(false, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(true, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(0, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(1, $resultAssignment->getResubmissionRule());
        $this->assertEquals(true, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(true, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(1, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(20, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser(self::$instructorTwo)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // read the user to make sure instructor defaults were'nt saved
        $instructor = self::$sdk->readUser(self::$instructorTwo)->getUser();

        // check instructor defaults
        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals($assignmentToUpdate->getAuthorOriginalityAccess(), $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToUpdate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals($assignmentToUpdate->getInternetCheck(), $savedDefaults->getInternetCheck());
        $this->assertEquals($assignmentToUpdate->getPublicationsCheck(), $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToUpdate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals($assignmentToUpdate->getLateSubmissionsAllowed(), $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToUpdate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals($assignmentToUpdate->getResubmissionRule(), $savedDefaults->getResubmissionRule());
        $this->assertEquals($assignmentToUpdate->getBibliographyExcluded(), $savedDefaults->getBibliographyExcluded());
        $this->assertEquals($assignmentToUpdate->getQuotedExcluded(), $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());
    }

    public function testUpdateAssignmentUsingInstructorDefaultsWhereUserHasNoInstructorDefaultsSet()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        // create an assignment to update
        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        $assignment->setQuotedExcluded(true);
        $assignment->setAuthorOriginalityAccess(true);
        $assignment->setSubmitPapersTo(0);
        $assignment->setSmallMatchExclusionThreshold(20);
        $assignment->setSmallMatchExclusionType(1);
        $assignment->setLateSubmissionsAllowed(true);
        $assignment->setInstitutionCheck(false);
        $assignment->setPublicationsCheck(true);
        $assignment->setResubmissionRule(1);
        $assignment->setSubmittedDocumentsCheck(true);
        $assignment->setInternetCheck(false);
        $assignment->setBibliographyExcluded(true);

        $assignmentToUpdate = self::$sdk->createAssignment($assignment)->getAssignment();

        // now update the assignment when using instructor defaults
        // which should have no affect
        $assignmentToUpdate->setInstructorDefaults(self::$instructorFour->getUserId());
        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment is the same as before
        $resultAssignment = self::$sdk->readAssignment($assignmentToUpdate)->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($startDate, $resultAssignment->getStartDate());
        $this->assertEquals($dueDate, $resultAssignment->getDueDate());
        $this->assertEquals($postDate, $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(true, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(true, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(false, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(false, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(true, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(0, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(1, $resultAssignment->getResubmissionRule());
        $this->assertEquals(true, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(true, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(1, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(20, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());
    }

    public function testUpdateAssignmentSavingSomePropertiesToInstrucorDefaults()
    {
        // first create an assignment using saving instructor defaults
        // so we know what they are.
        //fwrite(STDOUT, __METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignmentToCreate = new TiiAssignment();
        $assignmentToCreate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToCreate->setTitle("Testing assignment");
        $assignmentToCreate->setClassId(self::$tiiClass->getClassID());
        $assignmentToCreate->setStartDate($startDate);
        $assignmentToCreate->setDueDate($dueDate);
        $assignmentToCreate->setFeedbackReleaseDate($postDate);

        self::$sdk->createAssignment($assignmentToCreate);

        // now an assignment has been made saving instructor defaults we
        // create another one that is not the same as the one just created.
        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        $assignmentToUpdate = self::$sdk->createAssignment($assignment)->getAssignment();

        // now update the assignment when using instructor defaults
        // which should have no affect
        $assignmentToUpdate->setInstructorDefaultsSave(self::$instructorTwo->getUserId());
        $assignmentToUpdate->setSubmitPapersTo(0);
        $assignmentToUpdate->setSmallMatchExclusionThreshold(20);
        $assignmentToUpdate->setSmallMatchExclusionType(1);
        $assignmentToUpdate->setInstitutionCheck(false);
        $assignmentToUpdate->setSubmittedDocumentsCheck(false);
        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment is the same as before
        $resultAssignment = self::$sdk->readAssignment($assignmentToUpdate)->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($startDate, $resultAssignment->getStartDate());
        $this->assertEquals($dueDate, $resultAssignment->getDueDate());
        $this->assertEquals($postDate, $resultAssignment->getFeedbackReleaseDate());

        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(false, $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals(false, $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals(true, $resultAssignment->getInternetCheck());
        $this->assertEquals(true, $resultAssignment->getPublicationsCheck());
        $this->assertEquals(false, $resultAssignment->getInstitutionCheck());
        $this->assertEquals(100, $resultAssignment->getMaxGrade());
        $this->assertEquals(false, $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals(0, $resultAssignment->getSubmitPapersTo());
        $this->assertEquals(0, $resultAssignment->getResubmissionRule());
        $this->assertEquals(false, $resultAssignment->getBibliographyExcluded());
        $this->assertEquals(false, $resultAssignment->getQuotedExcluded());
        $this->assertEquals(1, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(20, $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals(false, $resultAssignment->getAnonymousMarking());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaults());
        $this->assertEquals(null, $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals(false, $resultAssignment->getErater());
        $this->assertEquals(false, $resultAssignment->getEraterSpelling());
        $this->assertEquals(false, $resultAssignment->getEraterGrammar());
        $this->assertEquals(false, $resultAssignment->getEraterUsage());
        $this->assertEquals(false, $resultAssignment->getEraterMechanics());
        $this->assertEquals(false, $resultAssignment->getEraterStyle());
        $this->assertEquals(null, $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals(null, $resultAssignment->getEraterHandbook());
        $this->assertEquals(false, $resultAssignment->getTranslatedMatching());

        // Bust the cache manually with an update
        $user = self::$sdk->readuser(self::$instructorTwo)->getUser();
        $user->setEmail(null);
        self::$sdk->updateUser($user);

        // read the user to make sure instructor defaults were'nt saved
        $instructor = self::$sdk->readUser(self::$instructorTwo)->getUser();

        // check instructor defaults
        $savedDefaults = $instructor->getInstructorDefaults();
        $this->assertEquals(false, $savedDefaults->getAuthorOriginalityAccess());
        $this->assertEquals($assignmentToUpdate->getSubmittedDocumentsCheck(), $savedDefaults->getSubmittedDocumentsCheck());
        $this->assertEquals(true, $savedDefaults->getInternetCheck());
        $this->assertEquals(true, $savedDefaults->getPublicationsCheck());
        $this->assertEquals($assignmentToUpdate->getInstitutionCheck(), $savedDefaults->getInstitutionCheck());
        $this->assertEquals(false, $savedDefaults->getLateSubmissionsAllowed());
        $this->assertEquals($assignmentToUpdate->getSubmitPapersTo(), $savedDefaults->getSubmitPapersTo());
        $this->assertEquals(0, $savedDefaults->getResubmissionRule());
        $this->assertEquals(false, $savedDefaults->getBibliographyExcluded());
        $this->assertEquals(false, $savedDefaults->getQuotedExcluded());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionType(), $savedDefaults->getSmallMatchExclusionType());
        $this->assertEquals($assignmentToUpdate->getSmallMatchExclusionThreshold(), $savedDefaults->getSmallMatchExclusionThreshold());
    }
}
