<?php

require_once(__DIR__ . '/../utilmethods.php');
require_once(__DIR__ . '/../testconsts.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');

use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiMembership;

class MembershipTest extends PHPUnit_Framework_TestCase
{
    protected static $sdk;
    protected static $tiiClass;
    protected static $studentOne;
    protected static $studentTwo;
    protected static $studentThree;
    protected static $instructorOne;
    protected static $instructorTwo;
    protected static $instructorThree;
    protected $membershipTeardownIds;

    private static $classtitle = "MembershipTest Class";

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
        self::$studentOne = UtilMethods::getUser("studentonephpsdk@vle.org.uk");
        self::$studentTwo = UtilMethods::getUser("studenttwophpsdk@vle.org.uk");
        self::$studentThree = UtilMethods::getUser("studentthreephpsdk@vle.org.uk");
        self::$instructorOne = UtilMethods::getUser("instructoronephpsdk@vle.org.uk");
        self::$instructorTwo = UtilMethods::getUser("instructortwophpsdk@vle.org.uk");
        self::$instructorThree = UtilMethods::getUser("instructorthreephpsdk@vle.org.uk");
    }

    public static function tearDownAfterClass()
    {
        UtilMethods::clearClasses(self::$sdk, self::$classtitle);
    }

    /**
     * @group smoke
     * @return array
     */
    public function testCreateLearnerMembership()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$studentOne->getUserId());

        $response = self::$sdk->createStudentMembership($membershipToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership processed successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return array('MembershipToRead'=>$response->getMembership(),'ExpectedMembership'=>$membershipToCreate);
    }

    public function testCreateStudentMembership()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$studentThree->getUserId());
        $membershipToCreate->setRole('Student');

        $response = self::$sdk->createMembership($membershipToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership processed successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testCreateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $membership = new TiiMembership();
        $api->createMembership($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $membership = new TiiMembership();
        $api->readMembership($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadsSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $membership = new TiiMembership();
        $api->readMemberships($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testDeleteSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $membership = new TiiMembership();
        $api->deleteMembership($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testFindSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $membership = new TiiMembership();
        $api->findMemberships($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /0\/2 Membership Objects returned./
     */
    public function testReadMembershipsError()
    {
        $membership = new TiiMembership();
        $membership->setMembershipIds(array(0,0));
        self::$sdk->readMemberships($membership);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /Class CourseSection not found./
     */
    public function testFindMembershipsError()
    {
        $membership = new TiiMembership();
        self::$sdk->findMemberships($membership);
    }

    /**
     * @group smoke
     */
    public function testCreateInstructorMembership()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$instructorOne->getUserId());

        $response = self::$sdk->createInstructorMembership($membershipToCreate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership processed successfully.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return array('MembershipToRead'=>$response->getMembership(),'ExpectedMembership'=>$membershipToCreate);
    }

    /**
     * @group smoke
     * @depends testCreateLearnerMembership
     */
    public function testReadStudentMembership(array $memberships)
    {
        $membershipToRead = $memberships['MembershipToRead'];
        $expectedMembership = $memberships['ExpectedMembership'];

        $response = self::$sdk->readMembership($membershipToRead);
        $resultMembership = $response->getMembership();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership object found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting membership
        $this->assertEquals($membershipToRead->getMembershipId(), $resultMembership->getMembershipId());
        $this->assertEquals($expectedMembership->getClassId(), $resultMembership->getClassId());
        $this->assertEquals($expectedMembership->getUserId(), $resultMembership->getUserId());
        $this->assertEquals($expectedMembership->getRole(), $resultMembership->getRole());
        $this->assertNull($resultMembership->getMembershipIds());
    }

    /**
     * @group smoke
     * @depends testCreateInstructorMembership
     */
    public function testReadInstructorMembership(array $memberships)
    {
        $membershipToRead = $memberships['MembershipToRead'];
        $expectedMembership = $memberships['ExpectedMembership'];

        $response = self::$sdk->readMembership($membershipToRead);
        $resultMembership = $response->getMembership();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership object found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting membership
        $this->assertEquals($membershipToRead->getMembershipId(), $resultMembership->getMembershipId());
        $this->assertEquals($expectedMembership->getClassId(), $resultMembership->getClassId());
        $this->assertEquals($expectedMembership->getUserId(), $resultMembership->getUserId());
        $this->assertEquals($expectedMembership->getRole(), $resultMembership->getRole());
        $this->assertNull($resultMembership->getMembershipIds());
    }

    /**
     * @group smoke
     */
    public function testFindMemberships()
    {
        // create a sperate class to place the
        // memberships into for this test.
        $classToCreate = new TiiClass();
        $classToCreate->setTitle("Assignment Testing Class.");
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        $membership1ToCreate = new TiiMembership();
        $membership1ToCreate->setClassId($tiiClass->getClassId());
        $membership1ToCreate->setUserId(self::$instructorOne->getUserId());
        $membership1ToCreate->setRole("Instructor");

        $instructorMembershipId = self::$sdk->createMembership($membership1ToCreate)
                                            ->getMembership()
                                            ->getMembershipId();

        $membership2ToCreate = new TiiMembership();
        $membership2ToCreate->setClassId($tiiClass->getClassId());
        $membership2ToCreate->setUserId(self::$studentOne->getUserId());
        $membership2ToCreate->setRole("Learner");

        $studentMembershipId = self::$sdk->createMembership($membership2ToCreate)->getMembership()->getMembershipId();

        // now find the memberships
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId($tiiClass->getClassId());

        $response = self::$sdk->findMemberships($membershipsToFind);
        $resultMembershipIds = $response->getMembership()->getMembershipIds();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 Membership Objects found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $studentMembershipFound = false;
        $instructorMembershipFound = false;

        for ($i = 0; $i < count($resultMembershipIds); $i++) {
            if ($resultMembershipIds[$i] == $studentMembershipId) {
                $this->assertFalse($studentMembershipFound);
                $studentMembershipFound = true;
            } elseif ($resultMembershipIds[$i] == $instructorMembershipId) {
                $this->assertFalse($instructorMembershipFound);
                $instructorMembershipFound = true;
            } else {
                $this->fail("Unexpected Membership Id found!");
            }
        }

        $this->assertTrue($studentMembershipFound && $instructorMembershipFound);
    }

    /**
     * @group smoke
     */
    public function testFindMembershipsOneIdReturned()
    {
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        $membership = new TiiMembership();
        $membership->setClassId($tiiClass->getClassId());
        $membership->setUserId(self::$studentOne->getUserId());
        self::$sdk->createStudentMembership($membership);

        $findMembership = new TiiMembership();
        $findMembership->setClassId($tiiClass->getClassId());
        $response = self::$sdk->findMemberships($findMembership);

        $this->assertEquals(count($response->getMembership()->getMembershipIds()), 1);
        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    public function testFindMembershipsNoResults()
    {
        // create a sperate class to place the
        // memberships into for this test.
        $classToCreate = new TiiClass();
        $classToCreate->setTitle(self::$classtitle);
        $tiiClass = self::$sdk->createClass($classToCreate)->getClass();

        // now find the memberships
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId($tiiClass->getClassId());

        $response = self::$sdk->findMemberships($membershipsToFind);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("0 Membership Objects found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not found for this account
     */
    public function testDeleteMembership()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$studentTwo->getUserId());
        $membershipToCreate->setRole("Learner");

        $membershipToDelete = self::$sdk->createMembership($membershipToCreate)->getMembership();

        // now delete the memeberhsip
        $response = self::$sdk->deleteMembership($membershipToDelete);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Membership object successfully deleted.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // read to check it was really deleted
        $membershipToRead = new TiiMembership();
        $membershipToRead->setMembershipId($membershipToDelete->getmembershipId());

        self::$sdk->readMembership($membershipToRead);
    }

    /**
     * @group smoke
     * @return mixed
     */
    public function testReadMemberships()
    {
        $membership1ToCreate = new TiiMembership();
        $membership1ToCreate->setClassId(self::$tiiClass->getClassId());
        $membership1ToCreate->setUserId(self::$instructorTwo->getUserId());
        $membership1ToCreate->setRole("Instructor");

        $instructorTwoMembershipId = self::$sdk->createMembership($membership1ToCreate)
                                               ->getMembership()
                                               ->getMembershipId();

        $membership2ToCreate = new TiiMembership();
        $membership2ToCreate->setClassId(self::$tiiClass->getClassId());
        $membership2ToCreate->setUserId(self::$instructorThree->getUserId());
        $membership2ToCreate->setRole("Instructor");

        $instructorThreeMembershipId = self::$sdk->createMembership($membership2ToCreate)
                                                 ->getMembership()
                                                 ->getMembershipId();

        // now read the memberships
        $membershipsToRead = new TiiMembership();
        $membershipsToRead->setMembershipIds(array($instructorTwoMembershipId,$instructorThreeMembershipId));
        $response = self::$sdk->readMemberships($membershipsToRead);
        $resultMemberships = $response->getMemberships();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2/2 Membership Objects returned.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $instructorTwoFound = false;
        $instructorThreefound = false;

        for ($i = 0; $i < count($resultMemberships); $i++) {
            if ($resultMemberships[$i]->getMembershipId() == $instructorTwoMembershipId) {
                $this->assertFalse($instructorTwoFound);
                $instructorTwoFound = true;

                $this->assertEquals($membership1ToCreate->getClassId(), $resultMemberships[$i]->getClassId());
                $this->assertEquals($membership1ToCreate->getUserId(), $resultMemberships[$i]->getuserId());
                $this->assertEquals($membership1ToCreate->getRole(), $resultMemberships[$i]->getRole());
            } elseif ($resultMemberships[$i]->getMembershipId() == $instructorThreeMembershipId) {
                $this->assertFalse($instructorThreefound);
                $instructorThreefound = true;

                $this->assertEquals($membership2ToCreate->getClassId(), $resultMemberships[$i]->getClassId());
                $this->assertEquals($membership2ToCreate->getUserId(), $resultMemberships[$i]->getuserId());
                $this->assertEquals($membership2ToCreate->getRole(), $resultMemberships[$i]->getRole());
            } else {
                $this->fail("Unexpected membership returned!");
            }
        }

        $this->assertTrue($instructorTwoFound && $instructorThreefound);
         return $resultMemberships;
    }

    /**
     * @depends testCreateInstructorMembership
     * @param array $memberships
     */
    public function testReadMembershipsSingleId(array $memberships)
    {
        $membership = $memberships['MembershipToRead'];

        $membershipsToRead = new TiiMembership();
        $membershipsToRead->setMembershipIds([$membership->getMembershipId()]);

        $response = self::$sdk->readMemberships($membershipsToRead);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage User not found.
     */
    public function testCreateMembershipWithoutUserId()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setRole("Instructor");

        self::$sdk->createMembership($membershipToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testCreateMembershipWithoutClassId()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setUserId(self::$instructorTwo->getUserId());
        $membershipToCreate->setRole("Instructor");

        self::$sdk->createMembership($membershipToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage user_role - This field is required
     * @expectedExceptionMessage user_role - user_role not valid
     */
    public function testCreateMembershipWithoutRole()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$instructorTwo->getUserId());

        self::$sdk->createMembership($membershipToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage user_role - This field is required
     * @expectedExceptionMessage user_role - user_role not valid
     */
    public function testCreateMembershipWithInvalidRole()
    {
        $membershipToCreate = new TiiMembership();
        $membershipToCreate->setClassId(self::$tiiClass->getClassId());
        $membershipToCreate->setUserId(self::$instructorTwo->getUserId());
        $membershipToCreate->setRole("Manbat");

        self::$sdk->createMembership($membershipToCreate);
    }

    /**
     * @group smoke
     * @depends testReadMemberships
     */
    public function testReadMembershipsPartialSuccess(array $validMemberships)
    {
        $membershipsToRead = new TiiMembership();

        $memberships_array = array($validMemberships[0]->getMembershipId(), $validMemberships[1]->getMembershipId(), 0);
        $membershipsToRead->setMembershipIds($memberships_array);

        $response = self::$sdk->readMemberships($membershipsToRead);
        $resultMemberships = $response->getMemberships();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2/3 Membership Objects returned.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("partialdatastorage", $response->getStatusCode());

        $instructorTwoFound = false;
        $instructorThreefound = false;

        for ($i = 0; $i < count($resultMemberships); $i++) {
            if ($resultMemberships[$i]->getMembershipId() == $validMemberships[0]->getMembershipId()) {
                $this->assertFalse($instructorTwoFound);
                $instructorTwoFound = true;

                $this->assertEquals($validMemberships[0]->getClassId(), $resultMemberships[$i]->getClassId());
                $this->assertEquals($validMemberships[0]->getUserId(), $resultMemberships[$i]->getUserId());
                $this->assertEquals($validMemberships[0]->getRole(), $resultMemberships[$i]->getRole());
            } elseif ($resultMemberships[$i]->getMembershipId() == $validMemberships[1]->getMembershipId()) {
                $this->assertFalse($instructorThreefound);
                $instructorThreefound = true;

                $this->assertEquals($validMemberships[1]->getClassId(), $resultMemberships[$i]->getClassId());
                $this->assertEquals($validMemberships[1]->getUserId(), $resultMemberships[$i]->getuserId());
                $this->assertEquals($validMemberships[1]->getRole(), $resultMemberships[$i]->getRole());
            } else {
                $this->fail("Unexpected membership returned!");
            }
        }

        $this->assertTrue($instructorTwoFound && $instructorThreefound);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testReadMembershipWithNegativeMembershipId()
    {
        $membershipToRead = new TiiMembership();
        $membershipToRead->setMembershipId(-1);

        self::$sdk->readMembership($membershipToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testReadMembershipWithNullMembershipId()
    {
        $membershipToRead = new TiiMembership();
        $membershipToRead->setMembershipId(null);

        self::$sdk->readMembership($membershipToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testReadMembershipWithStringMembershipId()
    {
        $membershipToRead = new TiiMembership();
        $membershipToRead->setMembershipId("SomeString");

        self::$sdk->readMembership($membershipToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testReadMembershipWithSymbolMembershipId()
    {
        $membershipToRead = new TiiMembership();
        $membershipToRead->setMembershipId("!\"£$%^&*()");

        self::$sdk->readMembership($membershipToRead);
    }

    //

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testDeleteMembershipWithNegativeMembershipId()
    {
        $membershipToDelete = new TiiMembership();
        $membershipToDelete->setMembershipId(-1);

        self::$sdk->deleteMembership($membershipToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testDeleteMembershipWithNullMembershipId()
    {
        $membershipToDelete = new TiiMembership();
        $membershipToDelete->setMembershipId(null);

        self::$sdk->deleteMembership($membershipToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testDeleteMembershipWithStringMembershipId()
    {
        $membershipToDelete = new TiiMembership();
        $membershipToDelete->setMembershipId("SomeString");

        self::$sdk->deleteMembership($membershipToDelete);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage sourcedId - Not valid id, expected Integer
     */
    public function testDeleteMembershipWithSymbolMembershipId()
    {
        $membershipToDelete = new TiiMembership();
        $membershipToDelete->setMembershipId("!\"£$%^&*()");

        self::$sdk->deleteMembership($membershipToDelete);
    }

   /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Data type error.
     */
    public function readMembershipWithInvalidMembershipIds()
    {
        $membershipsToRead = new TiiMembership();
        $membershipsToRead->setMembershipIds(array(-1, null, "SomeString", "!\"£$%^&*()"));

        self::$sdk->readMemberships($membershipsToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindMembershipsWithNegativeClassId()
    {
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId(-1);

        self::$sdk->findMemberships($membershipsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindMembershipWithNullClassId()
    {
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId(null);

        self::$sdk->findMemberships($membershipsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindMembershipWithStringClassId()
    {
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId("SomeString");

        self::$sdk->findMemberships($membershipsToFind);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage Class CourseSection not found.
     */
    public function testFindMembershipWithSymbolClassId()
    {
        $membershipsToFind = new TiiMembership();
        $membershipsToFind->setClassId("!\"£$%^&*()");

        self::$sdk->findMemberships($membershipsToFind);
    }
}
