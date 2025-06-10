<?php

require_once(__DIR__ . '/../utilmethods.php');
require_once(__DIR__ . '/../testconsts.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');

use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TiiAssignment;

class AssignmentTest extends PHPUnit_Framework_TestCase
{
    protected static $sdk;
    protected static $tiiClass;
    protected static $studentOne;
    protected static $studentTwo;
    protected static $instructorOne;
    protected static $instructorTwo;
    protected static $instructorThree;
    protected static $instructorDefaults;
    protected static $instructorTwoMembershipId;
    protected static $instructorThreeMembershipId;
    protected static $instructorDefaultsMembershipId;
    protected $assignmentTeardownIds;
    protected $classTeardownIds;

    private static $classtitle = "AssignmentTest Class";

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // fwrite(STDOUT, "\nAccount ID: ".TII_ACCOUNT."\n");
        // fwrite(STDOUT, "\nBase URL: ".TII_APIBASEURL."\n");
        self::$sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);
        self::$sdk->setDebug(false);

        // create a class all assignments will be made to
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        self::$tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // add members to the class
        self::$instructorTwo = UtilMethods::getUser("instructortwophpsdk@vle.org.uk");
        self::$instructorThree = UtilMethods::getUser("instructorthreephpsdk@vle.org.uk");
        self::$instructorDefaults = UtilMethods::getUser("instructordefaultsphpsdk@vle.org.uk");

        // enroll users to class
        self::$instructorTwoMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$instructorTwo->getUserId(),
            "Instructor"
        );

        self::$instructorThreeMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$instructorThree->getUserId(),
            "Instructor"
        );

        self::$instructorDefaultsMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$instructorDefaults->getUserId(),
            "Instructor"
        );
    }

    protected function setUp()
    {
        // create/reset teardown arrays
        $this->assignmentTeardownIds = array();
        $this->classTeardownIds = array();
    }

    protected function tearDown()
    {
        // tear down test
    }

    public static function tearDownAfterClass()
    {
        $membership = new TiiMembership();
        $membership->setMembershipId(self::$instructorTwoMembershipId);
        self::$sdk->deleteMembership($membership);

        $membership = new TiiMembership();
        $membership->setMembershipId(self::$instructorThreeMembershipId);
        self::$sdk->deleteMembership($membership);

        $membership = new TiiMembership();
        $membership->setMembershipId(self::$instructorDefaultsMembershipId);
        self::$sdk->deleteMembership($membership);

        // Delete class
        self::$sdk->deleteClass(self::$tiiClass);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testCreateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testUpdateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->updateAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->readAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadsSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->readAssignments($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testDeleteSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->deleteAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testFindSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $assignment = new TiiAssignment();
        $api->findAssignments($assignment);
    }

    /**
     * @group smoke
     * @return array
     */
    public function testCreateAssignment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $instructorDefaults = UtilMethods::getUser("instructordefaultsphpsdk@vle.org.uk");

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setInstructorDefaultsSave($instructorDefaults->getUserId());

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        // Reading done in "testReadAssignment"
        return array('AssignmentToRead'=>$response->getAssignment(),'ExpectedAssignment'=>$assignment);
    }

    /**
     * @group smoke
     * @return array
     */
    public function testCreateNonDefaultAssignment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        // non-default properties from here
        $assignment->setInstructions("Some test text.");
        $assignment->setAuthorOriginalityAccess(true);
        $assignment->setSubmittedDocumentsCheck(false);
        $assignment->setInternetCheck(false);
        $assignment->setPublicationsCheck(false);
        $assignment->setInstitutionCheck(false);
        $assignment->setMaxGrade(75);
        $assignment->setLateSubmissionsAllowed(true);
        $assignment->setSubmitPapersTo(0);
        $assignment->setResubmissionRule(1);
        $assignment->setBibliographyExcluded(true);
        $assignment->setQuotedExcluded(true);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold(10);
        // $assignment->setAnonymousMarking(true); TODO update when account available
        // instructor defaults tested later
        $assignment->setErater(true);
        $assignment->setEraterSpelling(true);
        $assignment->setEraterGrammar(true);
        $assignment->setEraterUsage(true);
        $assignment->setEraterMechanics(true);
        $assignment->setEraterStyle(true);
        $assignment->setEraterSpellingDictionary("en_GB");
        $assignment->setEraterHandbook(3);
        $assignment->setTranslatedMatching(true);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        // Reading done in "testReadAssignment"
        return array('AssignmentToRead'=>$response->getAssignment(),'ExpectedAssignment'=>$assignment);
    }

    /**
     * @group smoke
     * @depends testCreateAssignment
     */
    public function testReadAssignment(array $assignments)
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");

        $assignmentToRead = $assignments['AssignmentToRead'];
        $expectedAssignment = $assignments['ExpectedAssignment'];

        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($expectedAssignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($expectedAssignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($expectedAssignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
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

    /**
     * @group smoke
     * @depends testCreateNonDefaultAssignment
     */
    public function testReadNonDefaultAssignment(array $assignments)
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");

        $assignmentToRead = $assignments['AssignmentToRead'];
        $expectedAssignment = $assignments['ExpectedAssignment'];

        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($expectedAssignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($expectedAssignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($expectedAssignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals($expectedAssignment->getInstructions(), $resultAssignment->getInstructions());
        $this->assertEquals($expectedAssignment->getAuthorOriginalityAccess(), $resultAssignment->getAuthorOriginalityAccess());
        $this->assertEquals($expectedAssignment->getSubmittedDocumentsCheck(), $resultAssignment->getSubmittedDocumentsCheck());
        $this->assertEquals($expectedAssignment->getInternetCheck(), $resultAssignment->getInternetCheck());
        $this->assertEquals($expectedAssignment->getPublicationsCheck(), $resultAssignment->getPublicationsCheck());
        $this->assertEquals($expectedAssignment->getInstitutionCheck(), $resultAssignment->getInstitutionCheck());
        $this->assertEquals($expectedAssignment->getMaxGrade(), $resultAssignment->getMaxGrade());
        $this->assertEquals($expectedAssignment->getLateSubmissionsAllowed(), $resultAssignment->getLateSubmissionsAllowed());
        $this->assertEquals($expectedAssignment->getSubmitPapersTo(), $resultAssignment->getSubmitPapersTo());
        $this->assertEquals($expectedAssignment->getResubmissionRule(), $resultAssignment->getResubmissionRule());
        $this->assertEquals($expectedAssignment->getBibliographyExcluded(), $resultAssignment->getBibliographyExcluded());
        $this->assertEquals($expectedAssignment->getQuotedExcluded(), $resultAssignment->getQuotedExcluded());
        $this->assertEquals($expectedAssignment->getSmallMatchExclusionType(), $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals($expectedAssignment->getSmallMatchExclusionThreshold(), $resultAssignment->getSmallMatchExclusionThreshold());
        $this->assertEquals($expectedAssignment->getAnonymousMarking(), $resultAssignment->getAnonymousMarking());
        $this->assertEquals($expectedAssignment->getInstructorDefaults(), $resultAssignment->getInstructorDefaults());
        $this->assertEquals($expectedAssignment->getInstructorDefaultsSave(), $resultAssignment->getInstructorDefaultsSave());
        $this->assertEquals($expectedAssignment->getErater(), $resultAssignment->getErater());
        $this->assertEquals($expectedAssignment->getEraterSpelling(), $resultAssignment->getEraterSpelling());
        $this->assertEquals($expectedAssignment->getEraterGrammar(), $resultAssignment->getEraterGrammar());
        $this->assertEquals($expectedAssignment->getEraterUsage(), $resultAssignment->getEraterUsage());
        $this->assertEquals($expectedAssignment->getEraterMechanics(), $resultAssignment->getEraterMechanics());
        $this->assertEquals($expectedAssignment->getEraterStyle(), $resultAssignment->getEraterStyle());
        $this->assertEquals($expectedAssignment->getEraterSpellingDictionary(), $resultAssignment->getEraterSpellingDictionary());
        $this->assertEquals($expectedAssignment->getEraterHandbook(), $resultAssignment->getEraterHandbook());
        $this->assertEquals($expectedAssignment->getTranslatedMatching(), $resultAssignment->getTranslatedMatching());
    }

    /**
     * @depends testCreateAssignment
     * @param array $assignments
     */
    public function testPartialSuccess(array $assignments)
    {
        $assignment_id = $assignments['AssignmentToRead']->getAssignmentId();
        // Search for the same ID twice
        $assignmentIds = array($assignment_id, $assignment_id);

        // read the assignments
        $assignmentsToRead = new TiiAssignment();
        $assignmentsToRead->setAssignmentIds($assignmentIds);
        $response = self::$sdk->readAssignments($assignmentsToRead);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("1 / 2 Assignment LineItems found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("partialdatastorage", $response->getStatusCode());
    }

    /**
     * @group smoke
     */
    public function testReadAssignments()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment1 = new TiiAssignment();
        $assignment1->setTitle("Testing assignment 1");
        $assignment1->setClassId(self::$tiiClass->getClassId());
        $assignment1->setStartDate($startDate);
        $assignment1->setDueDate($dueDate);
        $assignment1->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment1);
        $assignment1Id = $response->getAssignment()->getAssignmentId();

        $assignment2 = new TiiAssignment();
        $assignment2->setTitle("Testing assignment 2");
        $assignment2->setClassId(self::$tiiClass->getClassId());
        $assignment2->setStartDate($startDate);
        $assignment2->setDueDate($dueDate);
        $assignment2->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment2);
        $assignment2Id = $response->getAssignment()->getAssignmentId();

        $assignmentIds = array($assignment1Id,$assignment2Id);

        // read the assignments
        $assignmentsToRead = new TiiAssignment();
        $assignmentsToRead->setAssignmentIds($assignmentIds);
        $response = self::$sdk->readAssignments($assignmentsToRead);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 / 2 Assignment LineItems found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the resulting assignments
        $assignment1Found = false;
        $assignment2Found = false;
        $resultAssignments = $response->getAssignments();

        $this->assertEquals(2, count($resultAssignments));

        for ($i = 0; $i < count($resultAssignments); $i++) {
            if ($resultAssignments[$i]->getAssignmentId() == $assignment1Id) {
                $this->assertEquals($assignment1->getTitle(), $resultAssignments[$i]->getTitle());
                $this->assertFalse($assignment1Found);
                $assignment1Found = true;
            } elseif ($resultAssignments[$i]->getAssignmentId() == $assignment2Id) {
                $this->assertEquals($assignment2->getTitle(), $resultAssignments[$i]->getTitle());
                $this->assertFalse($assignment2Found);
                $assignment2Found = true;
            } else {
                $this->fail("Unexpected assignment returned!");
                $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignments[$i]->getClassId());
                $this->assertEquals($startDate, $resultAssignments[$i]->getStartDate());
                $this->assertEquals($dueDate, $resultAssignments[$i]->getDueDate());
                $this->assertEquals($postDate, $resultAssignments[$i]->getFeedbackReleaseDate());
                // check properties that were not set in create assignment call
                // but should have been set by default
                $this->assertEquals(null, $resultAssignments[$i]->getInstructions());
                $this->assertEquals(false, $resultAssignments[$i]->getAuthorOriginalityAccess());
                $this->assertEquals(true, $resultAssignments[$i]->getSubmittedDocumentsCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getInternetCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getPublicationsCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getInstitutionCheck());
                $this->assertEquals(100, $resultAssignments[$i]->getMaxGrade());
                $this->assertEquals(false, $resultAssignments[$i]->getLateSubmissionsAllowed());
                $this->assertEquals(1, $resultAssignments[$i]->getSubmitPapersTo());
                $this->assertEquals(0, $resultAssignments[$i]->getResubmissionRule());
                $this->assertEquals(false, $resultAssignments[$i]->getBibliographyExcluded());
                $this->assertEquals(false, $resultAssignments[$i]->getQuotedExcluded());
                $this->assertEquals(0, $resultAssignments[$i]->getSmallMatchExclusionType());
                $this->assertEquals(0, $resultAssignments[$i]->getSmallMatchExclusionThreshold());
                $this->assertEquals(false, $resultAssignments[$i]->getAnonymousMarking());
                $this->assertEquals(null, $resultAssignments[$i]->getInstructorDefaults());
                $this->assertEquals(null, $resultAssignments[$i]->getInstructorDefaultsSave());
                $this->assertEquals(false, $resultAssignments[$i]->getErater());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterSpelling());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterGrammar());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterUsage());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterMechanics());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterStyle());
                $this->assertEquals(null, $resultAssignments[$i]->getEraterSpellingDictionary());
                $this->assertEquals(null, $resultAssignments[$i]->getEraterHandbook());
                $this->assertEquals(false, $resultAssignments[$i]->getTranslatedMatching());
            }
        }
        $this->assertTrue($assignment1Found && $assignment2Found);
    }

    public function testUpdateTitleOnlyAssignment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create an ordinary assignment
        $assignments = $this->createDefaultAssignment();
        $assignmentToUpdate = $assignments['ResultAssignment'];
        $expectedAssignment = $assignments['InputAssignment'];

        // update the assingment title
        $assignmentToUpdate->setTitle("Updated Title");

        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // read assignment to check it was really updated
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId($assignmentToUpdate->getAssignmentId());
        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check the assignment
        $this->assertEquals("Updated Title", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($expectedAssignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($expectedAssignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($expectedAssignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
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

    /**
     * @group smoke
     */
    public function testUpdateAssignment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create an ordinary assignment
        $assignments = $this->createDefaultAssignment();
        $assignmentToUpdate = $assignments['ResultAssignment'];
        $expectedAssignment = $assignments['InputAssignment'];

        // update the assingment title
        $assignmentToUpdate->setTitle("Updated Title");
        $assignmentToUpdate->setAuthorOriginalityAccess(true);
        $assignmentToUpdate->setSmallMatchExclusionType(2);
        $assignmentToUpdate->setSmallMatchExclusionThreshold(10);

        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // read assignment to check it was really updated
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId($assignmentToUpdate->getAssignmentId());
        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check the assignment
        $this->assertEquals("Updated Title", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($expectedAssignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($expectedAssignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($expectedAssignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
        // check properties that were not set in create assignment call
        // but should have been set by default
        $this->assertEquals(null, $resultAssignment->getInstructions());
        $this->assertEquals(true, $resultAssignment->getAuthorOriginalityAccess());
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
        $this->assertEquals(2, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(10, $resultAssignment->getSmallMatchExclusionThreshold());
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

    public function testUpdateAddPeerMarkAssignment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create an ordinary assignment
        $assignments = $this->createDefaultAssignment();
        $assignmentToUpdate = $assignments['ResultAssignment'];

        $peerMarkAssignment = new \Integrations\PhpSdk\TiiPeermarkAssignment();
        $peerMarkAssignment->setTitle('PeerMark Title');

        $assignmentToUpdate->setPeermarkAssignments([$peerMarkAssignment]);

        $response = self::$sdk->updateAssignment($assignmentToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // read assignment to check it was really updated
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId($assignmentToUpdate->getAssignmentId());
        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
    }

    /**
     * @group smoke
     * @return mixed
     */
    public function testDeleteAssingment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create an ordinary assignment
        $assignments = $this->createDefaultAssignment();
        $assignmentToDelete = $assignments['ResultAssignment'];

        $response = self::$sdk->deleteAssignment($assignmentToDelete);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully deleted.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return $assignmentToDelete;
    }

    /**
     * @depends testDeleteAssingment
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testReadDeletedAssignment($assignmentToRead)
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        self::$sdk->readAssignment($assignmentToRead);
    }

    /**
     * @group smoke
     */
    public function testFindAssingments()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create a sperate class to place the
        // assignments into for this test.
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment1 = new TiiAssignment();
        $assignment1->setTitle("Testing assignment 1");
        $assignment1->setClassId($tiiClass->getClassId());
        $assignment1->setStartDate($startDate);
        $assignment1->setDueDate($dueDate);
        $assignment1->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment1);
        $assignment1Id = $response->getAssignment()->getAssignmentId();

        $assignment2 = new TiiAssignment();
        $assignment2->setTitle("Testing assignment 2");
        $assignment2->setClassId($tiiClass->getClassId());
        $assignment2->setStartDate($startDate);
        $assignment2->setDueDate($dueDate);
        $assignment2->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment2);
        $assignment2Id = $response->getAssignment()->getAssignmentId();

        // find the assignments
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId($tiiClass->getClassId());
        $response = self::$sdk->findAssignments($assignmentsToFind);
        $resultAssignments = $response->getAssignments();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 Assignment LineItems found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $assignment1Found = false;
        $assignment2Found = false;
        $resultAssignments = $response->getAssignment()->getAssignmentIds();

        $this->assertEquals(2, count($resultAssignments));

        for ($i = 0; $i < count($resultAssignments); $i++) {
            if ($resultAssignments[$i] == $assignment1Id) {
                $this->assertFalse($assignment1Found);
                $assignment1Found = true;
            } elseif ($resultAssignments[$i] == $assignment2Id) {
                $this->assertFalse($assignment2Found);
                $assignment2Found = true;
            } else {
                $this->fail("Unexpected assignment returned!");
            }
        }
        $this->assertTrue($assignment1Found && $assignment2Found);
    }

    public function testFindAssingmentsNoResults()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create a sperate class to place the
        // assignments into for this test.
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // find the assignments
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId($tiiClass->getClassId());
        $response = self::$sdk->findAssignments($assignmentsToFind);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("0 Assignment LineItems found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage title - This field is required
     */
    public function testCreateAssignmentWithoutTitle()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        //$assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testCreateAssignmentWithoutClassId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        //$assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_start - This field is required
     */
    public function testCreateAssignmentWithoutStartDate()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_due - This field is required
     */
    public function testCreateAssignmentWithoutDueDate()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_due - due date must be after start date
     */
    public function testCreateAssignmentStartDateAfterDueDate()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_post - post date must be after start date
     */
    public function testCreateAssignmentPostDateBeforeStartDate()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-2 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    public function testCreateAssignmentEraterOptionsSetTo1WhenEraterIs0()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setErater(false);
        $assignment->setEraterSpelling(true);
        $assignment->setEraterGrammar(true);
        $assignment->setEraterUsage(true);
        $assignment->setEraterMechanics(true);
        $assignment->setEraterStyle(true);
        $assignment->setEraterSpellingDictionary("en_GB");
        $assignment->setEraterHandbook(3);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $assignmentToRead = $response->getAssignment();

        $response = self::$sdk->readAssignment($assignmentToRead);
        $resultAssignment = $response->getAssignment();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check the assignment
        $this->assertEquals("Testing assignment", $resultAssignment->getTitle());
        $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignment->getClassId());
        $this->assertEquals($assignment->getStartDate(), $resultAssignment->getStartDate());
        $this->assertEquals($assignment->getDueDate(), $resultAssignment->getDueDate());
        $this->assertEquals($assignment->getFeedbackReleaseDate(), $resultAssignment->getFeedbackReleaseDate());
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

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_due - due date must be after start date
     */
    public function testUpdateAssignmentStartDateAfterDueDate()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create an ordinary assignment
        $assignments = $this->createDefaultAssignment();
        $assignmentToUpdate = $assignments['ResultAssignment'];

        // update the assingment title
        $assignmentToUpdate->setTitle("Updated Title");
        $assignmentToUpdate->setDueDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $assignmentToUpdate->setStartDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+1 days')));

        self::$sdk->updateAssignment($assignmentToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_post - cannot exceed the term length of the class
     */
    public function testCreateTooLongAssingment()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+1 years'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+8 years'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testReadAssignmentWithNullAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId(null);
        self::$sdk->readAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testReadAssignmentWithStringAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId("SomeString");
        self::$sdk->readAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testReadAssignmentWithSymbolAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId("!\"£$%^&*()");
        self::$sdk->readAssignment($assignmentToRead);
    }

     /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testReadAssignmentWithNegativeAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId(-1);
        self::$sdk->readAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage date_post - cannot exceed the term length of the class
     */
    public function testUpdateAssignmentTooLong()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);
        $assignmentToUpdate = $response->getAssignment();

        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+1 years'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+8 years'));

        $assignmentToUpdate->setStartDate($startDate);
        $assignmentToUpdate->setDueDate($dueDate);
        $assignmentToUpdate->setFeedbackReleaseDate($postDate);

        self::$sdk->updateAssignment($assignmentToUpdate);
    }

    public function testUpdateAssignmentLongerThanClass()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        // create a sperate class to place the
        // assignments into for this test.
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        $classToCreate->setEndDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+2 years')));
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // create an assignment in local class
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId($tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);

        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+3 years'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+3 years'));

        $assignmentToUpdate = $response->getAssignment();
        $assignmentToUpdate->setStartDate($startDate);
        $assignmentToUpdate->setDueDate($dueDate);
        $assignmentToUpdate->setFeedbackReleaseDate($postDate);

        self::$sdk->updateAssignment($assignmentToUpdate);

        // read the class to make sure it has been extended
        $response = self::$sdk->readClass($tiiClass);
        $resultClass = $response->getClass();

        $this->assertEquals($postDate, $resultClass->getEndDate());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testDeleteAssignmentWithNullAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId(null);
        self::$sdk->deleteAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testDeleteAssignmentWithStringAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId("SomeString");
        self::$sdk->deleteAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testDeleteAssignmentWithSymbolAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setAssignmentId("!\"£$%^&*()");
        self::$sdk->deleteAssignment($assignmentToRead);
    }

     /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testDeleteAssignmentWithNegativeAssignmentId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentToRead = new TiiAssignment();
        $assignmentToRead->setClassId(-1);
        self::$sdk->deleteAssignment($assignmentToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindAssignmentsWithNullClassId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId(null);
        self::$sdk->findAssignments($assignmentsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindAssignmentsWithStringClassId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId("SomeString");
        self::$sdk->findAssignments($assignmentsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindAssignmentsWithSymbolClassId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId("!\"£$%^&*()");
        self::$sdk->findAssignments($assignmentsToFind);
    }

     /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindAssignmentsWithNegativeClassId()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentsToFind = new TiiAssignment();
        $assignmentsToFind->setClassId(-1);
        self::$sdk->findAssignments($assignmentsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage 0 / 4 Assignment LineItems found successfully.
     */
    public function testReadAssingmentsWithInvalidData()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $assignmentsToRead = new TiiAssignment();
        $assignmentsToRead->setAssignmentIds(array(null, "SomeString", "!\"£$%^&*()", -1));
        self::$sdk->readAssignments($assignmentsToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - Value must be a positive integer
     */
    public function testCreateAssignmentSmallMatchPercentNegative()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold(-5);

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - Percentage exclusions must be set to a value between 1 and 100 percent.
     */
    public function testCreateAssignmentSmallMatchPercentOver100()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold(105);

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - This field is required
     */
    public function testCreateAssignmentSmallMatchPercentNull()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold(null);

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - Value must be an integer
     */
    public function testCreateAssignmentSmallMatchPercentString()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold("SomeString");

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - Value must be an integer
     */
    public function testCreateAssignmentSmallMatchWordcountString()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(1);
        $assignment->setSmallMatchExclusionThreshold("SomeString");

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - Value must be a positive integer
     */
    public function testCreateAssignmentSmallMatchWordcountNegative()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(1);
        $assignment->setSmallMatchExclusionThreshold(-5);

        self::$sdk->createAssignment($assignment);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage exclude_value - This field is required
     */
    public function testCreateAssignmentSmallMatchWordcountNull()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(1);
        $assignment->setSmallMatchExclusionThreshold(null);

        self::$sdk->createAssignment($assignment);
    }

    public function testCreateAssignmentSmallMatchThreshZeroPercent()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(2);
        $assignment->setSmallMatchExclusionThreshold(0);

        $response = self::$sdk->createAssignment($assignment);
        $resultAssignment = $response->getAssignment();

        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionThreshold());
    }

    public function testCreateAssignmentSmallMatchThreshZeroWordcount()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);
        $assignment->setSmallMatchExclusionType(1);
        $assignment->setSmallMatchExclusionThreshold(0);

        $response = self::$sdk->createAssignment($assignment);
        $resultAssignment = $response->getAssignment();

        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionType());
        $this->assertEquals(0, $resultAssignment->getSmallMatchExclusionThreshold());
    }

    public function testReadAssignmentsPartialSuccess()
    {
        //fwrite(STDOUT, "\n".__METHOD__ . "\n");
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment1 = new TiiAssignment();
        $assignment1->setTitle("Testing assignment 1");
        $assignment1->setClassId(self::$tiiClass->getClassId());
        $assignment1->setStartDate($startDate);
        $assignment1->setDueDate($dueDate);
        $assignment1->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment1);
        $assignment1Id = $response->getAssignment()->getAssignmentId();

        $assignment2 = new TiiAssignment();
        $assignment2->setTitle("Testing assignment 2");
        $assignment2->setClassId(self::$tiiClass->getClassId());
        $assignment2->setStartDate($startDate);
        $assignment2->setDueDate($dueDate);
        $assignment2->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment2);
        $assignment2Id = $response->getAssignment()->getAssignmentId();

        $assignmentIds = array($assignment1Id,$assignment2Id, -1);

        // read the assignments
        $assignmentsToRead = new TiiAssignment();
        $assignmentsToRead->setAssignmentIds($assignmentIds);
        $response = self::$sdk->readAssignments($assignmentsToRead);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 / 3 Assignment LineItems found successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("partialdatastorage", $response->getStatusCode());

        // check the resulting assignments
        $assignment1Found = false;
        $assignment2Found = false;
        $resultAssignments = $response->getAssignments();

        $this->assertEquals(2, count($resultAssignments));

        for ($i = 0; $i < count($resultAssignments); $i++) {
            if ($resultAssignments[$i]->getAssignmentId() == $assignment1Id) {
                $this->assertEquals($assignment1->getTitle(), $resultAssignments[$i]->getTitle());
                $this->assertFalse($assignment1Found);
                $assignment1Found = true;
            } elseif ($resultAssignments[$i]->getAssignmentId() == $assignment2Id) {
                $this->assertEquals($assignment2->getTitle(), $resultAssignments[$i]->getTitle());
                $this->assertFalse($assignment2Found);
                $assignment2Found = true;
            } else {
                $this->fail("Unexpected assignment returned!");

                $this->assertEquals(self::$tiiClass->getClassId(), $resultAssignments[$i]->getClassId());
                $this->assertEquals($startDate, $resultAssignments[$i]->getStartDate());
                $this->assertEquals($dueDate, $resultAssignments[$i]->getDueDate());
                $this->assertEquals($postDate, $resultAssignments[$i]->getFeedbackReleaseDate());
                // check properties that were not set in create assignment call
                // but should have been set by default
                $this->assertEquals(null, $resultAssignments[$i]->getInstructions());
                $this->assertEquals(false, $resultAssignments[$i]->getAuthorOriginalityAccess());
                $this->assertEquals(true, $resultAssignments[$i]->getSubmittedDocumentsCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getInternetCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getPublicationsCheck());
                $this->assertEquals(true, $resultAssignments[$i]->getInstitutionCheck());
                $this->assertEquals(100, $resultAssignments[$i]->getMaxGrade());
                $this->assertEquals(false, $resultAssignments[$i]->getLateSubmissionsAllowed());
                $this->assertEquals(1, $resultAssignments[$i]->getSubmitPapersTo());
                $this->assertEquals(0, $resultAssignments[$i]->getResubmissionRule());
                $this->assertEquals(false, $resultAssignments[$i]->getBibliographyExcluded());
                $this->assertEquals(false, $resultAssignments[$i]->getQuotedExcluded());
                $this->assertEquals(0, $resultAssignments[$i]->getSmallMatchExclusionType());
                $this->assertEquals(0, $resultAssignments[$i]->getSmallMatchExclusionThreshold());
                $this->assertEquals(false, $resultAssignments[$i]->getAnonymousMarking());
                $this->assertEquals(null, $resultAssignments[$i]->getInstructorDefaults());
                $this->assertEquals(null, $resultAssignments[$i]->getInstructorDefaultsSave());
                $this->assertEquals(false, $resultAssignments[$i]->getErater());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterSpelling());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterGrammar());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterUsage());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterMechanics());
                $this->assertEquals(false, $resultAssignments[$i]->getEraterStyle());
                $this->assertEquals(null, $resultAssignments[$i]->getEraterSpellingDictionary());
                $this->assertEquals(null, $resultAssignments[$i]->getEraterHandbook());
                $this->assertEquals(false, $resultAssignments[$i]->getTranslatedMatching());
            }
        }
        $this->assertTrue($assignment1Found && $assignment2Found);
    }

    private function createDefaultAssignment()
    {
        $startDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days'));
        $dueDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));
        $postDate = gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days'));

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassID());
        $assignment->setStartDate($startDate);
        $assignment->setDueDate($dueDate);
        $assignment->setFeedbackReleaseDate($postDate);

        // create the assignment
        $response = self::$sdk->createAssignment($assignment);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Assignment LineItem successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        // Reading done in "testReadAssignment"
        return array('ResultAssignment'=>$response->getAssignment(),'InputAssignment'=>$assignment);
    }
}
