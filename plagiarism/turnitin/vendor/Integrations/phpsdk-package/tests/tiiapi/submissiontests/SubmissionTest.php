<?php

require_once(__DIR__ . '/../utilmethods.php');
require_once(__DIR__ . '/../testconsts.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');

use Httpful\Http;
use Httpful\Mime;
use Integrations\PhpSdk\TiiLTI;
use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TiiAssignment;
use Integrations\PhpSdk\TiiSubmission;

class SubmissionTest extends PHPUnit_Framework_TestCase {
    protected static $sdk;
    protected static $tiiClass;
    protected static $studentOne;
    protected static $studentTwo;
    protected static $studentNothing;
    protected static $instructorOne;
    protected static $studentOneMembershipId;
    protected static $studentTwoMembershipId;
    protected static $studentNothingMembershipId;
    protected static $instructorOneMembershipId;
    protected static $testingAssignment;
    protected $membershipTeardownIds;

    private static $classtitle = "SubmissionTest Class";

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT,"\n" . __METHOD__ . "\n");
        self::$sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT, 'en');
        self::$sdk->setDebug(false);

        // create a class all memberships will be made to
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        self::$tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // add members to the class
        self::$studentOne     = UtilMethods::getUser("studentonephpsdk@vle.org.uk");
        self::$studentTwo     = UtilMethods::getUser("studenttwophpsdk@vle.org.uk");
        self::$studentNothing = UtilMethods::getUser("studentnothingphpsdk@vle.org.uk");
        self::$instructorOne  = UtilMethods::getUser("instructoronephpsdk@vle.org.uk");

        $assignment = new TiiAssignment();
        $assignment->setTitle("Testing assignment");
        $assignment->setClassId(self::$tiiClass->getClassId());
        $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days')));
        $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+14 days')));
        $assignment->setSubmitPapersTo(0);
        $assignment->setInstitutionCheck(false);
        $assignment->setInternetCheck(false);
        $assignment->setPublicationsCheck(false);
        $assignment->setSubmittedDocumentsCheck(false);
        $assignment->setResubmissionRule(1);
        $response = self::$sdk->createAssignment($assignment);
        $assignment = $response->getAssignment();
        self::$testingAssignment = self::$sdk->readAssignment($assignment)->getAssignment();

        // enroll users to class
        self::$studentOneMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$studentOne->getUserId(),
            "Learner"
        );

        self::$studentTwoMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$studentTwo->getUserId(),
            "Learner"
        );

        self::$studentNothingMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$studentNothing->getUserId(),
            "Learner"
        );

        self::$instructorOneMembershipId = UtilMethods::findOrCreateMembership(
            self::$sdk,
            self::$tiiClass->getClassId(),
            self::$instructorOne->getUserId(),
            "Instructor"
        );
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /The integration ID \(Source\) is not enabled/
     */
    public function testCreateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->createSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testCreateNothingSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->createNothingSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /The integration ID \(Source\) is not enabled/
     */
    public function testReplaceSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->replaceSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testUpdateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->updateSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->readSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadsSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->readSubmissions($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testDeleteSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->deleteSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testFindSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $api->findSubmissions($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testFindRecentSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $submission = new TiiSubmission();
        $submission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $api->findSubmissions($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /Assignment LineItem not found./
     */
    public function testFindRecentSubmissionsError()
    {
        $submission = new TiiSubmission();
        $submission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        self::$sdk->findSubmissions($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /User not found./
     */
    public function testCreateNothingError()
    {
        $submission = new TiiSubmission();
        self::$sdk->createNothingSubmission($submission);
    }

    public static function tearDownAfterClass()
    {
        //fwrite(STDOUT, self::$studentOneMembershipId . "\n");
        $membership = new TiiMembership();
        $membership->setMembershipId(self::$studentOneMembershipId);
        self::$sdk->deleteMembership($membership);

        $membership = new TiiMembership();
        $membership->setMembershipId(self::$studentTwoMembershipId);
        self::$sdk->deleteMembership($membership);

        $membership = new TiiMembership();
        $membership->setMembershipId(self::$studentNothingMembershipId);
        self::$sdk->deleteMembership($membership);

        $membership = new TiiMembership();
        $membership->setMembershipId(self::$instructorOneMembershipId);
        self::$sdk->deleteMembership($membership);

        self::$sdk->deleteAssignment(self::$testingAssignment);

        UtilMethods::clearClasses(self::$sdk, self::$classtitle);
    }

    /**
     * @group sanity
     * @group smoke
     * @return array
     */
    public function testCreateSubmission()
    {
        // create an assignment to submit to
        //fwrite(STDOUT, __METHOD__ . "\n");

        // now create a mint submission
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $response = self::$sdk->createSubmission($submission);

        // check response
        //$this->assertNotNull($response->getMessageId());
        $this->assertEquals("Your file has been saved successfully.", $response->getDescription());
        //$this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        $this->assertNotNull($response->getSubmission()->getSubmissionId());

        return array('ExpectedSubmission'=>$submission, 'ResultSubmission'=>$response->getSubmission());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /Submission Paper Data not found./
     */
    public function testCreateSubmissionNoFile()
    {
        // create an assignment to submit to
        //fwrite(STDOUT, __METHOD__ . "\n");

        // now create a bad submission
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath('/tmp/' . uniqid('file', true));
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        self::$sdk->createSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /XML Response could not be parsed/
     */
    public function testCreateSubmissionCurlFail()
    {
        // create an assignment to submit to
        //fwrite(STDOUT, __METHOD__ . "\n");
        $sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);

        // now create a bad submission
        $submission = new TiiSubmission();
        $submission->setSubmissionDataWeb('http://example.com', 'Example.html');
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        // Set Proxy Settings
        $sdk->setProxyHost('localhost');
        $sdk->setProxyPort(8080);
        $sdk->setProxyUser('test');
        $sdk->setProxyPassword('test');
        $sdk->setProxyBypass('test');
        $sdk->setProxyType('test');
        $sdk->setSSLCertificate('test');

        $sdk->createSubmission($submission);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /Submission Paper Data not found./
     */
    public function testReplaceSubmissionNoFile()
    {
        // create an assignment to submit to
        //fwrite(STDOUT, __METHOD__ . "\n");

        // Create Submission to Replace
        $submission = new TiiSubmission();
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());
        $submission->setTitle('Test Submission');
        $submission->setSubmitterUserId(self::$instructorOne->getUserId());
        $submission->setAuthorUserId(self::$studentOne->getUserId());
        $submission->setRole("Instructor");
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");

        // create the submission
        $response = self::$sdk->createSubmission($submission);
        $submission_id = $response->getSubmission()->getSubmissionId();

        // now create a bad submission
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath('/tmp/' . uniqid('file', true));
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setSubmissionId($submission_id);

        self::$sdk->replaceSubmission($submission);
    }

    public function testCreateFileOverrideFilename()
    {
        $new_file_name = 'newFileName.doc';

        // now create a submission with an overridden filename
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmissionDataFilename($new_file_name);
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $response = self::$sdk->createSubmission($submission);

        $submission_id = $response->getSubmission()->getSubmissionId();

        // check response
        $this->assertEquals("Your file has been saved successfully.", $response->getDescription());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        $this->assertNotNull($submission_id);


        $lti = new TiiLTI();
        $url = self::$sdk->getApiBaseUrl() . $lti::DOWNLOADORIGENDPOINT;
        $lti->setUserId(self::$studentOne->getUserId());
        $lti->setRole('Learner');
        $lti->setSubmissionId($submission_id);
        $lti->setAsJson(true);

        $code = 202;
        while ($code == 202) {
            sleep(2);
            $json = self::$sdk->outputDownloadOriginalFileForm($lti, true);
            $body = http_build_query(json_decode($json));

            $parse_method = function ($body) {
                try {
                    $body = gzdecode($body);
                } catch (Exception $e) {
                    // Eat Exception
                }
                return $body;
            };

            $response = \Httpful\Request::post($url)
                ->sendsType(Mime::FORM)
                ->method(Http::POST)
                ->parseWith($parse_method)
                ->body($body)
                ->send();

            $get_url = $response->headers['location'];
            $response = \Httpful\Request::get($get_url)
                ->parseWith($parse_method)
                ->send();

            $code = $response->code;
        }
        $content_disposition = $response->headers['content-disposition'];

        $this->assertContains($new_file_name, $content_disposition, 'Found overridden filename');
    }

    public function testReplaceFileOverrideFilename()
    {

        $new_file_name = 'newFileName.doc';

        // now create a submission with an overridden filename
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmissionDataFilename($new_file_name);
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing submission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $response = self::$sdk->createSubmission($submission);

        $submission_id = $response->getSubmission()->getSubmissionId();

        $new_new_file_name = 'newnewFileName.doc';

        // now replace submission and overwrite filename
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmissionDataFilename($new_new_file_name);
        $submission->setSubmitterUserId(self::$studentOne->getUserId());
        $submission->setTitle("Testing resubmission!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $response = self::$sdk->createSubmission($submission);

        $submission_id = $response->getSubmission()->getSubmissionId();

        // check response
        $this->assertEquals("Your file has been saved successfully.", $response->getDescription());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
        $this->assertNotNull($submission_id);

        $lti = new TiiLTI();
        $url = self::$sdk->getApiBaseUrl() . $lti::DOWNLOADORIGENDPOINT;
        $lti->setUserId(self::$studentOne->getUserId());
        $lti->setRole('Learner');
        $lti->setSubmissionId($submission_id);
        $lti->setAsJson(true);

        $code = 202;
        while ($code == 202) {
            sleep(2);
            $json = self::$sdk->outputDownloadOriginalFileForm($lti, true);
            $body = http_build_query(json_decode($json));

            $parse_method = function ($body) {
                try {
                    $body = gzdecode($body);
                } catch (Exception $e) {
                    // Eat Exception
                }
                return $body;
            };

            $response = \Httpful\Request::post($url)
                ->sendsType(Mime::FORM)
                ->method(Http::POST)
                ->parseWith($parse_method)
                ->body($body)
                ->send();

            $get_url = $response->headers['location'];
            $response = \Httpful\Request::get($get_url)
                ->parseWith($parse_method)
                ->send();

            $code = $response->code;
        }
        $content_disposition = $response->headers['content-disposition'];

        $this->assertContains($new_new_file_name, $content_disposition, 'Found overridden filename');
    }

    /**
     * @group sanity
     * @group smoke
     */
    public function testNothingSubmission()
    {
        $submission = new TiiSubmission();
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());
        $submission->setSubmitterUserId(self::$instructorOne->getUserId());
        $submission->setAuthorUserId(self::$studentNothing->getUserId());

        // create the nothing submission
        $response = self::$sdk->createNothingSubmission($submission);

        $this->assertNotNull($response->getSubmission());
        $this->assertTrue(get_class($response->getSubmission()) == 'Integrations\PhpSdk\TiiSubmission');
    }

    /**
     * @group smoke
     */
    public function testReplaceSubmission()
    {
        // Create Submission to Replace
        $submission = new TiiSubmission();
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());
        $submission->setTitle('Test Submission');
        $submission->setSubmitterUserId(self::$instructorOne->getUserId());
        $submission->setAuthorUserId(self::$studentNothing->getUserId());
        $submission->setRole("Instructor");
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");

        // create the submission
        $response = self::$sdk->createSubmission($submission);
        $submissionid = $response->getSubmission()->getSubmissionId();

        $this->assertNotNull($response->getSubmission());
        $this->assertTrue(get_class($response->getSubmission()) == 'Integrations\PhpSdk\TiiSubmission');

        $submission = new TiiSubmission();
        $submission->setSubmissionId($submissionid);
        $submission->setTitle('Test Submission');
        $submission->setSubmitterUserId(self::$instructorOne->getUserId());
        $submission->setAuthorUserId(self::$studentNothing->getUserId());
        $submission->setRole("Instructor");
        $submission->setSubmissionDataWeb('http://example.com', 'Example.html');

        // replace the submission
        $response = self::$sdk->replaceSubmission($submission);

        $this->assertNotNull($response->getSubmission());
        $this->assertTrue(get_class($response->getSubmission()) == 'Integrations\PhpSdk\TiiSubmission');

        $submission = new TiiSubmission();
        $submission->setSubmissionId($submissionid);
        $submission->setTitle('Test Submission');
        $submission->setSubmitterUserId(self::$instructorOne->getUserId());
        $submission->setAuthorUserId(self::$studentNothing->getUserId());
        $submission->setRole("Instructor");
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");

        // replace the submission
        $response = self::$sdk->replaceSubmission($submission);

        $this->assertNotNull($response->getSubmission());
        $this->assertTrue(get_class($response->getSubmission()) == 'Integrations\PhpSdk\TiiSubmission');
    }

    /**
     * @group sanity
     * @group smoke
     * @depends testCreateSubmission
     * @param array $submissions
     * @return array
     */
    public function testReadSubmission(array $submissions)
    {
        $submissionToRead = $submissions['ResultSubmission'];
        $expectedSubmission = $submissions['ExpectedSubmission'];

        UtilMethods::readSubmissionRetry(self::$sdk, $submissionToRead);

        $response = self::$sdk->readSubmission($submissionToRead);
        $resultSubmission = $response->getSubmission();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Object Result found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting submission

        // Anonymous marking stuff
        $this->assertEquals(false, $resultSubmission->getAnonymous());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealDateTime());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealReason());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealUser());

        // Basic stuff
        $this->assertEquals($submissionToRead->getSubmissionId(), $resultSubmission->getSubmissionId());
        $this->assertEquals(null, $resultSubmission->getAuthorLastViewedFeedback());
        $this->assertEquals($expectedSubmission->getSubmitterUserId(), $resultSubmission->getAuthorUserId());
        $this->assertEquals(null, $resultSubmission->getDateFrom());
        $this->assertEquals($expectedSubmission->getAssignmentId(), $resultSubmission->getAssignmentId());
        $this->assertEquals(false, $resultSubmission->getFeedbackExists());
        $this->assertEquals(null, $resultSubmission->getGrade());
        $this->assertEquals(null, $resultSubmission->getRole());
        $this->assertEquals(null, $resultSubmission->getSubmissionDataPath());
        $this->assertEquals(null, $resultSubmission->getSubmissionDataText());
        $this->assertEquals(null, $resultSubmission->getSubmissionIds());
        $this->assertEquals($expectedSubmission->getSubmitterUserId(), $resultSubmission->getSubmitterUserId());
        $this->assertEquals(null, $resultSubmission->getTextExtract());
        $this->assertEquals("Testing submission!", $resultSubmission->getTitle());
        $this->assertEquals(null, $resultSubmission->getTranslatedInternetSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedOverallSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedPublicationsSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedSubmittedDocumentsSimilarity());
        $this->assertEquals(false, $resultSubmission->getVoiceComment());

        return $submissions;
    }

    /**
     * @group sanity
     * @group smoke
     * @depends testReadSubmission
     */
    public function testUpdateSubmisionTitle($submissions)
    {
        $submissionToUpdate = $submissions['ResultSubmission'];
        $expectedSubmission = $submissions['ExpectedSubmission'];

        $submissionToUpdate->setTitle("Update Title!");

        $response = self::$sdk->updateSubmission($submissionToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Submission Result object successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // read the submission to check it was update
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId($submissionToUpdate->getSubmissionId());

        $resultSubmission = self::$sdk->readSubmission($submissionToRead)->getSubmission();

        // check resulting submission

        // Anonymous marking stuff
        $this->assertEquals(false, $resultSubmission->getAnonymous());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealDateTime());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealReason());
        $this->assertEquals(null, $resultSubmission->getAnonymousRevealUser());

        // Basic stuff
        $this->assertEquals($submissionToRead->getSubmissionId(), $resultSubmission->getSubmissionId());
        $this->assertEquals(null, $resultSubmission->getAuthorLastViewedFeedback());
        $this->assertEquals($expectedSubmission->getSubmitterUserId(), $resultSubmission->getAuthorUserId());
        $this->assertEquals(null, $resultSubmission->getDateFrom());
        $this->assertEquals($expectedSubmission->getAssignmentId(), $resultSubmission->getAssignmentId());
        $this->assertEquals(false, $resultSubmission->getFeedbackExists());
        $this->assertEquals(null, $resultSubmission->getGrade());
        $this->assertEquals(null, $resultSubmission->getRole());
        $this->assertEquals(null, $resultSubmission->getSubmissionDataPath());
        $this->assertEquals(null, $resultSubmission->getSubmissionDataText());
        $this->assertEquals(null, $resultSubmission->getSubmissionIds());
        $this->assertEquals($expectedSubmission->getSubmitterUserId(), $resultSubmission->getSubmitterUserId());
        $this->assertEquals(null, $resultSubmission->getTextExtract());
        $this->assertEquals("Update Title!", $resultSubmission->getTitle());
        $this->assertEquals(null, $resultSubmission->getTranslatedInternetSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedOverallSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedPublicationsSimilarity());
        $this->assertEquals(null, $resultSubmission->getTranslatedSubmittedDocumentsSimilarity());
        $this->assertEquals(false, $resultSubmission->getVoiceComment());

        return $submissions;
    }

    /**
     * @group sanity
     * @group smoke
     * @depends testUpdateSubmisionTitle
     */
    public function testDeleteSubmission($submissions)
    {
        $submissionToDelete = $submissions['ResultSubmission'];

        $response = self::$sdk->deleteSubmission($submissionToDelete);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Submission Result successfully deleted.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return $submissionToDelete->getSubmissionId();
    }

    /**
     * @depends testDeleteSubmission
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Error reading from database.
     */
    public function testReadDeletedSubmission($submissionId)
    {
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId($submissionId);

        self::$sdk->readSubmission($submissionToRead);
    }

    public function testReadSubmissions()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");

        // now create a mint submission
        $submission1 = new TiiSubmission();
        $submission1->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission1->setSubmitterUserId(self::$studentOne->getUserId());
        $submission1->setTitle("Testing submission 1!");
        $submission1->setRole("Learner");
        $submission1->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $resultSubmission1 = self::$sdk->createSubmission($submission1)->getSubmission();

        // now create a mint submission
        $submission2 = new TiiSubmission();
        $submission2->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission2->setSubmitterUserId(self::$studentTwo->getuserId());
        $submission2->setTitle("Testing submission 2!");
        $submission2->setRole("Learner");
        $submission2->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $resultSubmission2 = self::$sdk->createSubmission($submission2)->getSubmission();

        // Read the second submission to ensure they have both written
        // before doing a findSubmissions call
        $responseSubmission = UtilMethods::readSubmissionRetry(self::$sdk, $resultSubmission2);
        $this->assertNotNull($responseSubmission->getSubmissionId());

        // read the submissions
        $submissionsToRead = new TiiSubmission();
        $submission_array = array($resultSubmission1->getSubmissionId(), $resultSubmission2->getSubmissionId());
        $submissionsToRead->setSubmissionIds($submission_array);

        $response = self::$sdk->readSubmissions($submissionsToRead);
        $resultSubmissions = $response->getSubmissions();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2/2 Object Results found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $submission1found = false;
        $submission2found = false;

        $this->assertEquals(2, count($resultSubmissions));

        for ($i = 0; $i < count($resultSubmissions); $i++) {
            if ($resultSubmission1->getSubmissionId() == $resultSubmissions[$i]->getSubmissionId()) {
                $this->assertFalse($submission1found);
                $submission1found = true;

                $this->assertEquals(self::$studentOne->getUserId(), $resultSubmissions[$i]->getAuthorUserId());
                $this->assertEquals(self::$studentOne->getUserId(), $resultSubmissions[$i]->getSubmitterUserId());
                $this->assertEquals("Testing submission 1!", $resultSubmissions[$i]->getTitle());
            } elseif ($resultSubmission2->getSubmissionId() == $resultSubmissions[$i]->getSubmissionId()) {
                $this->assertFalse($submission2found);
                $submission2found = true;

                $this->assertEquals(self::$studentTwo->getUserId(), $resultSubmissions[$i]->getAuthorUserId());
                $this->assertEquals(self::$studentTwo->getUserId(), $resultSubmissions[$i]->getSubmitterUserId());
                $this->assertEquals("Testing submission 2!", $resultSubmissions[$i]->getTitle());
            } else {
                $this->fail("Unexpected submission returned!");

                // Anonymous marking stuff
                $this->assertEquals(false, $resultSubmissions[$i]->getAnonymous());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealDateTime());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealReason());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealUser());

                // Basic stuff
                $this->assertEquals(self::$testingAssignment->getAssignmentId(), $resultSubmissions[$i]->getAssignmentId());
                $this->assertEquals(null, $resultSubmissions[$i]->getAuthorLastViewedFeedback());
                $this->assertEquals(null, $resultSubmissions[$i]->getDateFrom());
                $this->assertEquals(false, $resultSubmissions[$i]->getFeedbackExists());
                $this->assertEquals(null, $resultSubmissions[$i]->getGrade());
                $this->assertEquals(null, $resultSubmissions[$i]->getRole());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionDataPath());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionDataText());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionIds());

                $this->assertEquals(null, $resultSubmissions[$i]->getTextExtract());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedInternetSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedOverallSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedPublicationsSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedSubmittedDocumentsSimilarity());
                $this->assertEquals(false, $resultSubmissions[$i]->getVoiceComment());
            }
        }
        $this->assertTrue($submission1found && $submission2found);
    }

    public function testReadSubmissionsSingleId()
    {
        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmitterUserId(self::$studentOne->getuserId());
        $submission->setTitle("Testing submission 1!");
        $submission->setRole("Learner");
        $submission->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $submission_id = self::$sdk->createSubmission($submission)->getSubmission()->getSubmissionId();

        $submission = new TiiSubmission();
        $submission->setSubmissionIds([$submission_id]);

        $response = self::$sdk->readSubmissions($submission);

        // check response
        $this->assertEquals(count($response->getSubmissions()), 1);
        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @group smoke
     */
    public function testFindSubmissions()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");

        $submission1 = new TiiSubmission();
        $submission1->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission1->setSubmitterUserId(self::$studentOne->getuserId());
        $submission1->setTitle("Testing submission 1!");
        $submission1->setRole("Learner");
        $submission1->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $submissionOneId = self::$sdk->createSubmission($submission1)->getSubmission()->getSubmissionId();

        $submission2 = new TiiSubmission();
        $submission2->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission2->setSubmitterUserId(self::$studentTwo->getuserId());
        $submission2->setTitle("Testing submission 2!");
        $submission2->setRole("Learner");
        $submission2->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $submissionTwo   = self::$sdk->createSubmission($submission2)->getSubmission();
        $submissionTwoId = $submissionTwo->getSubmissionId();

        // Read the second submission to ensure they have both written
        // before doing a findSubmissions call
        $responseSubmission = UtilMethods::readSubmissionRetry(self::$sdk, $submissionTwo);
        $this->assertNotNull($responseSubmission->getSubmissionId());

        // find the submissions
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $response = self::$sdk->findSubmissions($submissionsToFind);
        $resultSubmissionIds = $response->getSubmission()->getSubmissionIds();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertRegExp("/[0-9]+ Object Results found./", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $submissionOneFound = false;
        $submissionTwoFound = false;
        for ($i = 0; $i < count($resultSubmissionIds); $i++) {
            if ($resultSubmissionIds[$i] == $submissionOneId) {
                $this->assertFalse($submissionOneFound);
                $submissionOneFound = true;
            } elseif ($resultSubmissionIds[$i] ==  $submissionTwoId) {
                $this->assertFalse($submissionTwoFound);
                $submissionTwoFound = true;
            }
        }

        $this->assertTrue($submissionOneFound);
        $this->assertTrue($submissionTwoFound);

        // Find Submissions
        $findSubmission = new TiiSubmission();
        $findSubmission->setAssignmentId(self::$testingAssignment->getAssignmentId());
        $findresponse = self::$sdk->findSubmissions($findSubmission);
        $findSubmission = $findresponse->getSubmission();
        $submissions = $findSubmission->getSubmissionIds();
        $this->assertTrue(is_array($submissions));
        $this->assertTrue(count($submissions)>0);

        // Find Recent Submissions
        $findSubmission = new TiiSubmission();
        $findSubmission->setAssignmentId(self::$testingAssignment->getAssignmentId());
        $findSubmission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $findresponse = self::$sdk->findSubmissions($findSubmission);
        $findSubmission = $findresponse->getSubmission();
        $submissions = $findSubmission->getSubmissionIds();
        $this->assertTrue(is_array($submissions));
        $this->assertTrue(count($submissions)>0);
    }

    public function testFindSubmissionsSingleId()
    {
        $assignment = new TiiAssignment();
        $assignment->setTitle('Test Assignment');
        $assignment->setClassId(self::$tiiClass->getClassId());
        $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", strtotime('+2 days')));
        $assignment_id = self::$sdk->createAssignment($assignment)->getAssignment()->getAssignmentId();

        $submission = new TiiSubmission();
        $submission->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission->setSubmitterUserId(self::$studentOne->getuserId());
        $submission->setTitle("Testing submission 1!");
        $submission->setRole("Learner");
        $submission->setAssignmentId($assignment_id);

        self::$sdk->createSubmission($submission)->getSubmission()->getSubmissionId();

        $submission = new TiiSubmission();
        $submission->setAssignmentId($assignment_id);

        $response = self::$sdk->findSubmissions($submission);

        $this->assertEquals(count($response->getSubmission()->getSubmissionIds()), 1);
        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // Find with date from to trigger findRecentSubmissions()
        $submission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", strtotime('-1 days')));
        $response = self::$sdk->findSubmissions($submission);

        $this->assertEquals(count($response->getSubmission()->getSubmissionIds()), 1);
        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testReadSubmissionWithNullSubmissionId()
    {
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId(null);

        self::$sdk->readSubmission($submissionToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testReadSubmissionWithNegativeSubmissionId()
    {
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId(-1);

        self::$sdk->readSubmission($submissionToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testReadSubmissionWithStringSubmissionId()
    {
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId("SomeString");

        self::$sdk->readSubmission($submissionToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testReadSubmissionWithSymbolSubmissionId()
    {
        $submissionToRead = new TiiSubmission();
        $submissionToRead->setSubmissionId("!\"£$%^&*()");

        self::$sdk->readSubmission($submissionToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testDeleteSubmissionWithNullSubmissionId()
    {
        $submissionToDelete = new TiiSubmission();
        $submissionToDelete->setSubmissionId(null);

        self::$sdk->deleteSubmission($submissionToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testDeleteSubmissionWithNegativeSubmissionId()
    {
        $submissionToDelete = new TiiSubmission();
        $submissionToDelete->setSubmissionId(-1);

        self::$sdk->deleteSubmission($submissionToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testDeleteSubmissionWithStringSubmissionId()
    {
        $submissionToDelete = new TiiSubmission();
        $submissionToDelete->setSubmissionId("SomeString");

        self::$sdk->deleteSubmission($submissionToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testDeleteSubmissionWithSymbolSubmissionId()
    {
        $submissionToDelete = new TiiSubmission();
        $submissionToDelete->setSubmissionId("!\"£$%^&*()");

        self::$sdk->deleteSubmission($submissionToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testUpdateSubmissionWithNullSubmissionId()
    {
        $submissionToUpdate = new TiiSubmission();
        $submissionToUpdate->setSubmissionId(null);
        $submissionToUpdate->setTitle("Updated title!");

        self::$sdk->updateSubmission($submissionToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testUpdateSubmissionWithNegativeSubmissionId()
    {
        $submissionToUpdate = new TiiSubmission();
        $submissionToUpdate->setSubmissionId(-1);
        $submissionToUpdate->setTitle("Updated title!");

        self::$sdk->updateSubmission($submissionToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testUpdateSubmissionWithStringSubmissionId()
    {
        $submissionToUpdate = new TiiSubmission();
        $submissionToUpdate->setSubmissionId("SomeString");
        $submissionToUpdate->setTitle("Updated title!");

        self::$sdk->updateSubmission($submissionToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function testUpdateSubmissionWithSymbolSubmissionId()
    {
        $submissionToUpdate = new TiiSubmission();
        $submissionToUpdate->setSubmissionId("!\"£$%^&*()");
        $submissionToUpdate->setTitle("Updated title!");

        self::$sdk->updateSubmission($submissionToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage 0/4 Object Results found.
     */
    public function testReadSubmissionsWithInvalidSubmissionIds()
    {
        $submissionsToRead = new TiiSubmission();
        $submissionsToRead->setSubmissionIds(array(null, -1, "SomeString", "!\"£$%^&*()"));

        self::$sdk->readSubmissions($submissionsToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testFindSubmissionsWithNullAssignmentId()
    {
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId(null);

        self::$sdk->findSubmissions($submissionsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testFindSubmissionsWithNegativeAssignmentId()
    {
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId(-1);

        self::$sdk->findSubmissions($submissionsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testFindSubmissionsWithStringAssignmentId()
    {
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId("SomeString");

        self::$sdk->findSubmissions($submissionsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Assignment LineItem not found.
     */
    public function testFindSubmissionWithSymbolAssignmentId()
    {
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId("!\"£$%^&*()");

        self::$sdk->findSubmissions($submissionsToFind);
    }

    public function testFindSubmissionNoResults()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");

        // create a new empty assignment
        $response = self::$sdk->createAssignment(self::$testingAssignment);
        $empty_assignment = $response->getAssignment();
        $response = self::$sdk->readAssignment($empty_assignment);
        $empty_assignment = $response->getAssignment();

        // find the submissions
        $submissionsToFind = new TiiSubmission();
        $submissionsToFind->setAssignmentId($empty_assignment->getAssignmentId());

        $response = self::$sdk->findSubmissions($submissionsToFind);
        $resultSubmissionIds = $response->getSubmission()->getSubmissionIds();

        // check response
        $this->assertTrue(count($resultSubmissionIds) == 0);
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("0 Object Results found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    public function testReadSubmissionsPartialSuccess()
    {
        //fwrite(STDOUT, __METHOD__ . "\n");

        // now create a mint submission
        $submission1 = new TiiSubmission();
        $submission1->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission1->setSubmitterUserId(self::$studentOne->getuserId());
        $submission1->setTitle("Testing submission 1!");
        $submission1->setRole("Learner");
        $submission1->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $resultSubmission1 = self::$sdk->createSubmission($submission1)->getSubmission();

        // now create another mint submission
        $submission2 = new TiiSubmission();
        $submission2->setSubmissionDataPath(dirname(dirname(__FILE__))."/exampleFile.doc");
        $submission2->setSubmitterUserId(self::$studentTwo->getuserId());
        $submission2->setTitle("Testing submission 2!");
        $submission2->setRole("Learner");
        $submission2->setAssignmentId(self::$testingAssignment->getAssignmentId());

        $resultSubmission2 = self::$sdk->createSubmission($submission2)->getSubmission();

        // Read the second submission to ensure they have both written
        // before doing a findSubmissions call
        $responseSubmission = UtilMethods::readSubmissionRetry(self::$sdk, $resultSubmission2);
        $this->assertNotNull($responseSubmission->getSubmissionId());

        // read the submissions
        $submissionsToRead = new TiiSubmission();
        $submissions_array = array($resultSubmission1->getSubmissionId(), $resultSubmission2->getSubmissionId(), 0);
        $submissionsToRead->setSubmissionIds($submissions_array);

        $response = self::$sdk->readSubmissions($submissionsToRead);
        $resultSubmissions = $response->getSubmissions();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2/3 Object Results found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("partialdatastorage", $response->getStatusCode());

        $submission1found = false;
        $submission2found = false;

        $this->assertEquals(2, count($resultSubmissions));

        for ($i = 0; $i < count($resultSubmissions); $i++) {
            if ($resultSubmission1->getSubmissionId() == $resultSubmissions[$i]->getSubmissionId()) {
                $this->assertFalse($submission1found);
                $submission1found = true;

                $this->assertEquals(self::$studentOne->getUserId(), $resultSubmissions[$i]->getAuthorUserId());
                $this->assertEquals(self::$studentOne->getUserId(), $resultSubmissions[$i]->getSubmitterUserId());
                $this->assertEquals("Testing submission 1!", $resultSubmissions[$i]->getTitle());
            } elseif ($resultSubmission2->getSubmissionId() == $resultSubmissions[$i]->getSubmissionId()) {
                $this->assertFalse($submission2found);
                $submission2found = true;

                $this->assertEquals(self::$studentTwo->getUserId(), $resultSubmissions[$i]->getAuthorUserId());
                $this->assertEquals(self::$studentTwo->getUserId(), $resultSubmissions[$i]->getSubmitterUserId());
                $this->assertEquals("Testing submission 2!", $resultSubmissions[$i]->getTitle());
            } else {
                $this->fail("Unexpected submission returned!");

                // Anonymous marking stuff
                $this->assertEquals(false, $resultSubmissions[$i]->getAnonymous());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealDateTime());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealReason());
                $this->assertEquals(null, $resultSubmissions[$i]->getAnonymousRevealUser());

                // Basic stuff
                $this->assertEquals(self::$testingAssignment->getAssignmentId(), $resultSubmissions[$i]->getAssignmentId());
                $this->assertEquals(null, $resultSubmissions[$i]->getAuthorLastViewedFeedback());
                $this->assertEquals(null, $resultSubmissions[$i]->getDateFrom());
                //$this->assertCustomDateEquals("Submit date did not match expected submit date!",submitDate, resultSubmission.getDateSubmitted()); TODO
                $this->assertEquals(false, $resultSubmissions[$i]->getFeedbackExists());
                $this->assertEquals(null, $resultSubmissions[$i]->getGrade());
                //assertEquals(!, resultSubmission.getInternetSimilarity());
                //assertEquals(!, resultSubmission.getOverallSimilarity());
                //assertEquals(!, resultSubmission.getPublicationsSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getRole());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionDataPath());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionDataText());
                $this->assertEquals(null, $resultSubmissions[$i]->getSubmissionIds());
                //assertEquals(!, resultSubmission.getSubmittedDocumentsSimilarity());

                $this->assertEquals(null, $resultSubmissions[$i]->getTextExtract());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedInternetSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedOverallSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedPublicationsSimilarity());
                $this->assertEquals(null, $resultSubmissions[$i]->getTranslatedSubmittedDocumentsSimilarity());
                $this->assertEquals(false, $resultSubmissions[$i]->getVoiceComment());
            }
        }
        $this->assertTrue($submission1found);
        $this->assertTrue($submission2found);
    }
}
