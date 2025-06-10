<?php

require_once(__DIR__ . '/../utilmethods.php');
require_once(__DIR__ . '/../testconsts.php');
require_once(__DIR__ . '/../../../vendor/autoload.php');

use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiUser;
use Integrations\PhpSdk\TiiAssignment;

class UserTest extends PHPUnit_Framework_TestCase
{
    protected static $sdk;
    protected static $studentOne;
    protected static $studentTwo;
    protected static $instructorOne;
    protected static $instructorTwo;
    protected static $instructorThree;
    protected static $updateUser;
    protected static $instructorDefaults;
    protected static $defaultSettings;

    private static $classtitle = "UserTest Class";

    public static function setUpBeforeClass()
    {
        // fwrite(STDOUT, "\n" . __METHOD__ . "\n");
        self::$sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);
        self::$sdk->setDebug(false);

        $classid = UtilMethods::findOrCreateClass(self::$sdk, self::$classtitle);

        // check users exist
        self::$studentOne = UtilMethods::getUser("studentonephpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$studentOne, "Learner");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$studentOne->getUserId(), 'Learner');

        self::$studentTwo = UtilMethods::getUser("studenttwophpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$studentTwo, "Learner");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$studentTwo->getUserId(), 'Learner');

        self::$instructorOne = UtilMethods::getUser("instructoronephpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$instructorOne, "Instructor");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$instructorOne->getUserId(), 'Instructor');

        self::$instructorTwo = UtilMethods::getUser("instructortwophpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$instructorTwo, "Instructor");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$instructorTwo->getUserId(), 'Instructor');

        self::$instructorThree = UtilMethods::getUser("instructorthreephpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$instructorThree, "Instructor");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$instructorThree->getUserId(), 'Instructor');

        self::$instructorDefaults = UtilMethods::getUser("instructordefaultsphpsdk@vle.org.uk");
        UtilMethods::setUserDefaultRole(self::$sdk, self::$instructorDefaults, "Instructor");
        UtilMethods::findOrCreateMembership(self::$sdk, $classid, self::$instructorDefaults->getUserId(), 'Instructor');

        self::$defaultSettings = UtilMethods::setInstructorDefaults(self::$sdk, self::$instructorDefaults);

        self::$updateUser = UtilMethods::getUser("updatinguserphpsdk@vle.org.uk");
    }

    public static function tearDownAfterClass()
    {
        UtilMethods::clearClasses(self::$sdk, self::$classtitle);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testCreateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $user = new TiiUser();
        $api->createUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testUpdateSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $user = new TiiUser();
        $api->updateUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $user = new TiiUser();
        $api->readUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testReadsSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $user = new TiiUser();
        $api->readUsers($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /API Login failed/
     */
    public function testFindSoapFault()
    {
        $api = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, 'bad', 0);
        $user = new TiiUser();
        $api->findUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     */
    public function testCreateStudentUserError()
    {
        $user = new TiiUser();
        $user->setFirstName(self::$studentOne->getFirstName());
        $user->setLastName(self::$studentOne->getLastName());
        $user->setEmail(self::$studentOne->getEmail());
        self::$sdk->createStudentUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     */
    public function testCreateInstructorUserError()
    {
        $user = new TiiUser();
        $user->setFirstName(self::$instructorOne->getFirstName());
        $user->setLastName(self::$instructorOne->getLastName());
        $user->setEmail(self::$instructorOne->getEmail());
        self::$sdk->createInstructorUser($user);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessageRegExp /Email address is invalid./
     */
    public function testFindUserError()
    {
        $user = new TiiUser();
        $user->setEmail('invalid');
        self::$sdk->findUser($user);
    }

    public function testCreateUser()
    {
        $user = new TiiUser();
        $user->setEmail(uniqid('testuseremail', true) . '@example.com');
        $user->setFirstName("Test");
        $user->setLastName("User");
        $user->setDefaultRole("Instructor");
        $user->setDefaultLanguage("en");

        $response = self::$sdk->createUser($user);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return $user;
    }

    public function testCreateStudent()
    {
        $user = new TiiUser();
        $user->setEmail(uniqid('testuseremail', true) . '@example.com');
        $user->setFirstName("Test");
        $user->setLastName("User");
        $user->setDefaultRole("Student");
        $user->setDefaultLanguage("en");

        $response = self::$sdk->createUser($user);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully created.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return $user;
    }

    public function testUpdateUser()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setFirstName("Newfirst");
        $userToUpdate->setLastName("Newlast");
        $userToUpdate->setDefaultRole("Instructor");
        $userToUpdate->setDefaultLanguage("es");

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        return $userToUpdate;
    }

    /**
     * @depends testUpdateUser
     */
    public function testReadUpdatedUser($expectedUser)
    {
        $userToRead = new TiiUser();
        $userToRead->setUserId(self::$updateUser->getUserId());

        $response = self::$sdk->readUser($userToRead);
        $resultUser = $response->getUser();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User Found", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check user object
        $this->assertEquals($userToRead->getUserId(), $resultUser->getuserId());
        $this->assertEquals($expectedUser->getFirstName(), $resultUser->getFirstName());
        $this->assertEquals($expectedUser->getLastName(), $resultUser->getLastName());
        $this->assertEquals($expectedUser->getDefaultRole(), $resultUser->getDefaultRole());
        $this->assertEquals($expectedUser->getDefaultLanguage(), $resultUser->getDefaultLanguage());
    }

    public function testReadStudentUser()
    {
        $response = self::$sdk->readUser(self::$studentOne);
        $resultUser = $response->getUser();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User Found", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check user object
        $this->assertEquals(self::$studentOne->getUserId(), $resultUser->getuserId());
        $this->assertEquals("Student", $resultUser->getFirstName());
        $this->assertEquals("One", $resultUser->getLastName());
        $this->assertEquals("Learner", $resultUser->getDefaultRole());
        $this->assertEquals("en_us", $resultUser->getDefaultLanguage());
        $this->assertEquals(new TiiAssignment(), $resultUser->getInstructorDefaults());
    }

    public function testReadInstructorUser()
    {
        $response = self::$sdk->readUser(self::$instructorDefaults);
        $resultUser = $response->getUser();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User Found", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check user object
        $this->assertEquals(self::$instructorDefaults->getUserId(), $resultUser->getuserId());
        $this->assertEquals("Instructor", $resultUser->getFirstName());
        $this->assertEquals("Defaults", $resultUser->getLastName());
        $this->assertEquals("Instructor", $resultUser->getDefaultRole());
        $this->assertEquals("en_us", $resultUser->getDefaultLanguage());
        $this->assertEquals(self::$defaultSettings, $resultUser->getInstructorDefaults());
    }

    public function testReadUsers()
    {
        $usersToRead = new TiiUser();
        $usersToRead->setUserIds(array(self::$studentOne->getUserId(),self::$instructorDefaults->getUserId()));

        $response = self::$sdk->readUsers($usersToRead);
        $resultUsers = $response->getUsers();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 / 2 Users Found", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        // check resulting users
        $studentOneFound = false;
        $defaultsFound = false;

        for ($i = 0; $i < count($resultUsers); $i++) {
            if (self::$studentOne->getUserId() == $resultUsers[$i]->getuserId()) {
                $this->assertFalse($studentOneFound);
                $studentOneFound = true;

                // check user object
                $this->assertEquals("Student", $resultUsers[$i]->getFirstName());
                $this->assertEquals("One", $resultUsers[$i]->getLastName());
                $this->assertEquals("Learner", $resultUsers[$i]->getDefaultRole());
                $this->assertEquals("en_us", $resultUsers[$i]->getDefaultLanguage());
                $this->assertEquals(new TiiAssignment(), $resultUsers[$i]->getInstructorDefaults());
            } elseif (self::$instructorDefaults->getUserId() == $resultUsers[$i]->getuserId()) {
                $this->assertFalse($defaultsFound);
                $defaultsFound = true;

                // check user object
                $this->assertEquals("Instructor", $resultUsers[$i]->getFirstName());
                $this->assertEquals("Defaults", $resultUsers[$i]->getLastName());
                $this->assertEquals("Instructor", $resultUsers[$i]->getDefaultRole());
                $this->assertEquals("en_us", $resultUsers[$i]->getDefaultLanguage());
                $this->assertEquals(self::$defaultSettings, $resultUsers[$i]->getInstructorDefaults());
            } else {
                $this->fail("Unexpected user returned!");
            }
        }

        $this->assertTrue($studentOneFound && $defaultsFound);
    }

    public function testReadUsersSingleUserId()
    {
        $usersToRead = new TiiUser();
        $usersToRead->setUserIds(array(self::$studentOne->getUserId()));

        $response = self::$sdk->readUsers($usersToRead);

        $this->assertNotNull($response->getMessageId());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    public function testFindUser()
    {
        $userToFind = new TiiUser();
        $userToFind->setEmail("studentonephpsdk@vle.org.uk");

        $response = self::$sdk->findUser($userToFind);
        $resultUserId = $response->getUser()->getUserId();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("Discovered 1 user/s.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());

        $this->assertEquals(self::$studentOne->getUserId(), $resultUserId);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage first_name - This field is required
     */
    public function testCreateUserWithoutFirstName()
    {
        $userToCreate = new TiiUser();
        //$userToCreate->setFirstName("Firstname");
        $userToCreate->setLastName("Lastname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Learner");
        $userToCreate->setEmail(uniqid()."phpsdktest@vle.org.uk");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage last_name - This field is required
     */
    public function testCreateUserWithoutLastName()
    {
        $userToCreate = new TiiUser();
        $userToCreate->setFirstName("Firstname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Learner");
        $userToCreate->setEmail(uniqid()."phpsdktest@vle.org.uk");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage email - This field is required
     */
    public function testCreateUserWithoutEmail()
    {
        $userToCreate = new TiiUser();
        $userToCreate->setFirstName("Firstname");
        $userToCreate->setLastName("Lastname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Learner");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage email - Email should be of the format someuser@example.com
     */
    public function testCreateUserWithInvalidEmail()
    {
        $userToCreate = new TiiUser();
        $userToCreate->setFirstName("Firstname");
        $userToCreate->setLastName("Lastname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Learner");
        $userToCreate->setEmail(uniqid()."phpsdktest@@vle.org.uk");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage default_user_type - This field is required
     */
    public function testCreateUserWithInvalidRole()
    {
        $userToCreate = new TiiUser();
        $userToCreate->setFirstName("Firstname");
        $userToCreate->setLastName("Lastname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Aquaman");
        $userToCreate->setEmail(uniqid()."phpsdktest@vle.org.uk");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage email - Sorry, this email already is in use
     */
    public function testCreateuserTwice()
    {
        $userToCreate = new TiiUser();
        $userToCreate->setFirstName("Firstname");
        $userToCreate->setLastName("Lastname");
        $userToCreate->setDefaultLanguage("en_us");
        $userToCreate->setDefaultRole("Learner");
        $userToCreate->setEmail("studentonephpsdk@vle.org.uk");

        self::$sdk->createuser($userToCreate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage User not found.
     */
    public function testReadUserWithNegativeUserId()
    {
        $userToRead = new TiiUser();
        $userToRead->setUserId(-1);

        self::$sdk->readUser($userToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage User not found.
     */
    public function testReadUserWithNullUserId()
    {
        $userToRead = new TiiUser();
        $userToRead->setUserId(null);

        self::$sdk->readUser($userToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage User not found.
     */
    public function testReadUserWithStringUserId()
    {
        $userToRead = new TiiUser();
        $userToRead->setUserId("SomeString");

        self::$sdk->readUser($userToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage User not found.
     */
    public function testReadUserWithSymbolUserId()
    {
        $userToRead = new TiiUser();
        $userToRead->setUserId("!\"£$%^&*()");

        self::$sdk->readUser($userToRead);
    }

    public function testReadUsersPartialSuccess()
    {
        $usersToRead = new TiiUser();
        $usersToRead->setUserIds(array(self::$studentOne->getUserId(), self::$instructorDefaults->getUserId(), 0));

        $response = self::$sdk->readUsers($usersToRead);
        $resultUsers = $response->getUsers();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("2 / 3 Users Found", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("partialdatastorage", $response->getStatusCode());

        // check resulting users
        $studentOneFound = false;
        $defaultsFound = false;

        for ($i = 0; $i < count($resultUsers); $i++) {
            if (self::$studentOne->getUserId() == $resultUsers[$i]->getuserId()) {
                $this->assertFalse($studentOneFound);
                $studentOneFound = true;

                // check user object
                $this->assertEquals("Student", $resultUsers[$i]->getFirstName());
                $this->assertEquals("One", $resultUsers[$i]->getLastName());
                $this->assertEquals("Learner", $resultUsers[$i]->getDefaultRole());
                $this->assertEquals("en_us", $resultUsers[$i]->getDefaultLanguage());
                $this->assertEquals(new TiiAssignment(), $resultUsers[$i]->getInstructorDefaults());
            } elseif (self::$instructorDefaults->getUserId() == $resultUsers[$i]->getuserId()) {
                $this->assertFalse($defaultsFound);
                $defaultsFound = true;

                // check user object
                $this->assertEquals("Instructor", $resultUsers[$i]->getFirstName());
                $this->assertEquals("Defaults", $resultUsers[$i]->getLastName());
                $this->assertEquals("Instructor", $resultUsers[$i]->getDefaultRole());
                $this->assertEquals("en_us", $resultUsers[$i]->getDefaultLanguage());
                $this->assertEquals(self::$defaultSettings, $resultUsers[$i]->getInstructorDefaults());
            } else {
                $this->fail("Unexpected user returned!");
            }
        }

        $this->assertTrue($studentOneFound && $defaultsFound);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage 0 / 4 Users Found
     */
    public function testReadUsersinvalidIds()
    {
        $usersToRead = new TiiUser();
        $usersToRead->setUserIds(array(-1, null, "SomeString", "!\"£$%^&*()"));

        self::$sdk->readUsers($usersToRead);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage email - Email address can not be updated via the API
     */
    public function testUpdateUserEmail()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setEmail("newflippingemail@vle.org.uk");

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage default_user_type - This field is required
     * @expectedExceptionMessage last_name - This field is required
     */
    public function testUpdateUserFirstname()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setFirstName("Newfirst");

        self::$sdk->updateuser($userToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage default_user_type - This field is required
     * @expectedExceptionMessage first_name - This field is required
     */
    public function testUpdateUserLastName()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setLastName("Newlast");

        self::$sdk->updateuser($userToUpdate);
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage last_name - This field is required
     * @expectedExceptionMessage first_name - This field is required
     */
    public function testUpdateUserRole()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setDefaultRole("Instructor");

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage default_user_type - This field is required
     * @expectedExceptionMessage last_name - This field is required
     * @expectedExceptionMessage first_name - This field is required
     */
    public function testUpdateUserLanguage()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setDefaultLanguage("es");

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage email - Email address can not be updated via the API
     */
    public function testUpdateUserEmailAlreadyInUse()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());
        $userToUpdate->setEmail("studentonephpsdk@vle.org.uk");

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     * @expectedExceptionMessage default_user_type - This field is required
     * @expectedExceptionMessage last_name - This field is required
     * @expectedExceptionMessage first_name - This field is required
     */
    public function testUpdateUserEmptyData()
    {
        self::resetUpdatingUser();

        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId(self::$updateUser->getUserId());

        $response = self::$sdk->updateuser($userToUpdate);

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User successfully updated.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("status", $response->getStatus());
        $this->assertEquals("fullsuccess", $response->getStatusCode());
    }

    /**
     * @expectedException Integrations\PhpSdk\TurnitinSDKException
     */
    public function testFindUserWithUnregisteredUser()
    {
        $userToFind = new TiiUser();
        $userToFind->setEmail("thisemaildoesntevenexisteverbatmanisthebest@vle.org.uk");

        $response = self::$sdk->findUser($userToFind);
        $resultUserId = $response->getUser()->getUserId();

        // check response
        $this->assertNotNull($response->getMessageId());
        $this->assertEquals("User not found.", $response->getDescription());
        $this->assertNotNull($response->getMessageRefId());
        $this->assertEquals("warning", $response->getStatus());
        $this->assertEquals("nosourcedids", $response->getStatusCode());

        $this->assertNull($resultUserId);
    }

    private static function resetUpdatingUser()
    {
        $user = new TiiUser();
        $user->setuserId(self::$updateUser->getUserId());
        $user->setFirstName("Updating");
        $user->setLastName("User");
        $user->setDefaultRole("Learner");
        $user->setDefaultLanguage("en_us");
        self::$sdk->updateUser($user);
    }
}
