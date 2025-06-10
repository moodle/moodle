<?php

use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TurnitinAPI;
use Integrations\PhpSdk\TiiUser;
use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiAssignment;
use Integrations\PhpSdk\TiiSubmission;
use Integrations\PhpSdk\TurnitinSDKException;

class UtilMethods extends PHPUnit_Framework_TestCase
{
    public static function getUser($email)
    {
        // fwrite(STDOUT, __METHOD__ . " ".$email."\n");
        $sdk = new TurnitinAPI(TII_ACCOUNT, TII_APIBASEURL, TII_SECRET, TII_APIPRODUCT);
        $user = new TiiUser();
        $user->setEmail($email);

        try {
            $response = $sdk->findUser($user);
            $newUser = $response->getUser();
        } catch (TurnitinSDKException $e) {
            // fwrite(STDOUT, __METHOD__ . " creating user for ".$email."\n");
            $newUser = new TiiUser();
            $newUser->setEmail($email);

            $users = array(
                array(
                    'email'      => 'studentonephpsdk@vle.org.uk',
                    'last_name'  => 'One',
                    'first_name' => 'Student',
                    'role'       => 'Learner',
                ),
                array(
                    'email'      => 'studenttwophpsdk@vle.org.uk',
                    'last_name'  => 'Two',
                    'first_name' => 'Student',
                    'role'       => 'Learner',
                ),
                array(
                    'email'      => 'studentthreephpsdk@vle.org.uk',
                    'last_name'  => 'three',
                    'first_name' => 'Student',
                    'role'       => 'Learner',
                ),
                array(
                    'email'      => 'studentnothingphpsdk@vle.org.uk',
                    'last_name'  => 'Nothing',
                    'first_name' => 'Student',
                    'role'       => 'Learner',
                ),
                array(
                    'email'      => 'instructoronephpsdk@vle.org.uk',
                    'last_name'  => 'One',
                    'first_name' => 'Instructor',
                    'role'       => 'Instructor',
                ),
                array(
                    'email'      => 'instructortwophpsdk@vle.org.uk',
                    'last_name'  => 'Two',
                    'first_name' => 'Instructor',
                    'role'       => 'Instructor',
                ),
                array(
                    'email'      => 'instructorthreephpsdk@vle.org.uk',
                    'last_name'  => 'Three',
                    'first_name' => 'Instructor',
                    'role'       => 'Instructor',
                ),
                array(
                    'email'      => 'updatinguserphpsdk@vle.org.uk',
                    'last_name'  => 'User',
                    'first_name' => 'Updating',
                    'role'       => 'Learner',
                ),
                array(
                    'email'      => 'instructordefaultsphpsdk@vle.org.uk',
                    'last_name'  => 'Defaults',
                    'first_name' => 'Instructor',
                    'role'       => 'Instructor',
                ),
                array(
                    'email'      => 'brandnewuserphpsdk@vle.org.uk',
                    'last_name'  => 'New',
                    'first_name' => 'Brand',
                    'role'       => 'Instructor',
                ),
            );

            $createuser = false;
            foreach ($users as $user) {
                if ($user['email'] == $newUser->getEmail()) {
                    $newUser->setFirstName($user['first_name']);
                    $newUser->setLastName($user['last_name']);
                    $newUser->setDefaultRole($user['role']);
                    $createuser = true;
                }
            }

            if ($createuser) {
                $newUser = $sdk->createUser($newUser)->getUser();
            }
        }

        try {
            $response = $sdk->readUser($newUser);
            $newUser = $response->getUser();
        } catch (TurnitinSDKException $e) {
            if ($e->getCode() == 'unknownobject') {
                // This will join the user to the account in the event of them being dropped by the cron
                $class_title = 'TempClass';
                $class_id = self::findOrCreateClass($sdk, $class_title);
                self::findOrCreateMembership($sdk, $class_id, $newUser->getUserId(), 'Learner');
                self::clearClasses($sdk, $class_title);
            }
        }


        if ($newUser->getEmail() == "instructordefaultsphpsdk@vle.org.uk") {
            self::setInstructorDefaults($sdk, $newUser);
        }

        return $newUser;
    }

    public static function setUserDefaultRole($sdk, $updateUser, $role)
    {
        $userToUpdate = new TiiUser();
        $userToUpdate->setUserId($updateUser->getUserId());
        // Read user to grab first/last name
        $response = $sdk->readUser($userToUpdate);
        $currentUser = $response->getUser();

        $userToUpdate->setFirstName($currentUser->getFirstName());
        $userToUpdate->setLastName($currentUser->getLastName());
        $userToUpdate->setDefaultRole($role);

        $response = $sdk->updateuser($userToUpdate);

        // check response
        self::assertNotNull($response->getMessageId());
        self::assertEquals("User successfully updated.", $response->getDescription());
    }

    public static function findOrCreateClass($sdk, $title)
    {
        $class = new TiiClass();
        $class->setTitle($title);
        try {
            $response = $sdk->findClasses($class);
            $classids = $response->getClass()->getClassIds();
            if (is_array($classids) && isset($classids[0])) {
                $classid = $classids[0];
            } else {
                $class->setEndDate(date("Y-m-d\TH:i:s\Z", strtotime('+30 days')));
                $response = $sdk->createClass($class);
                $classid = $response->getClass()->getClassId();
            }
        } catch (TurnitinSDKException $e) {
            self::fail("Unexpected findClass error!");
            return null;
        }
        return $classid;
    }

    public static function findOrCreateMembership($sdk, $classid, $userid, $role)
    {
        $membershipid = null;
        try {
            $membership = new TiiMembership();
            $membership->setClassId($classid);

            $class = new TiiClass();
            $class->setTitle('');
            $class->setUserId($userid);
            $class->setUserRole($role);

            $response = $sdk->findClasses($class);
            $classids = $response->getClass()->getClassIds();
            if (in_array($classid, $classids)) {
                $response = $sdk->findMemberships($membership);
                $membershipids = $response->getMembership()->getMembershipIds();
                $membership->setMembershipIds($membershipids);
                $response = $sdk->readMemberships($membership);
                foreach ($response->getMemberships() as $membership) {
                    if ($membership->getUserId($userid) && $membership->getRole($role)) {
                        $membershipid = $membership->getMembershipId();
                    }
                }
            } else {
                $membership->setUserId($userid);
                $membership->setRole($role);
                $response = $sdk->createMembership($membership);
                $membershipid = $response->getMembership()->getMembershipId();
            }
        } catch (TurnitinSDKException $e) {
            self::fail("Unexpected createMembership error!");
        }
        return $membershipid;
    }

    public static function setInstructorDefaults($sdk, $user)
    {
        $classid = self::findOrCreateClass($sdk, 'Defaults Class');
        self::findOrCreateMembership($sdk, $classid, $user->getUserId(), 'Instructor');

        try {
            $assignment = new TiiAssignment();
            $assignment->setClassId($classid);
            $response = $sdk->findAssignments($assignment);
            $assignmentids = $response->getAssignment()->getAssignmentIds();
            if (is_array($assignmentids) && isset($assignmentids[0])) {
                $assignmentid = $assignmentids[0];
            } else {
                $assignment->setTitle('Defaults Assignment');
                $assignment->setStartDate(date("Y-m-d\TH:i:s\Z", strtotime('now')));
                $assignment->setFeedbackReleaseDate(date("Y-m-d\TH:i:s\Z", strtotime('tomorrow')));
                $assignment->setDueDate(date("Y-m-d\TH:i:s\Z", strtotime('tomorrow')));
                $response = $sdk->createAssignment($assignment);
                $assignmentid = $response->getAssignment()->getAssignmentId();
            }
            $assignment->setAssignmentId($assignmentid);

            $assignment->setInstructions('Instructor Defaults');
            $assignment->setAuthorOriginalityAccess(true);
            $assignment->setSubmittedDocumentsCheck(true);
            $assignment->setInternetCheck(true);
            $assignment->setPublicationsCheck(false);
            $assignment->setInstitutionCheck(false);
            $assignment->setMaxGrade(100);
            $assignment->setLateSubmissionsAllowed(true);
            $assignment->setSubmitPapersTo(0);
            $assignment->setResubmissionRule(0);
            $assignment->setBibliographyExcluded(true);
            $assignment->setQuotedExcluded(true);
            $assignment->setSmallMatchExclusionType(1);
            $assignment->setSmallMatchExclusionThreshold(10);
            $assignment->setAnonymousMarking(false);
            $assignment->setInstructorDefaultsSave($user->getUserId());
            $assignment->setTranslatedMatching(false);
            $assignment->setAllowNonOrSubmissions(false);

            $sdk->updateAssignment($assignment);

        } catch (TurnitinSDKException $e) {
            self::fail("Unexpected set defaults error!");
        }

        // Clear out non default settings
        $assignment->setClassId(null);
        $assignment->setAssignmentId(null);
        $assignment->setInstructions(null);
        $assignment->setMaxGrade(null);
        $assignment->setInstructorDefaultsSave(null);

        return $assignment;
    }

    public static function readSubmissionRetry($sdk, $submissionToRead)
    {
        $readAttempts = 1;
        $maxReadAttempts = 10;
        $resultSubmission = new TiiSubmission();
        while (true) {
            if ($readAttempts == $maxReadAttempts) {
                echo("Read failed ".$maxReadAttempts." times! Giving up.\n");
                break;
            }
            $readAttempts++;
            try {
                sleep(1);
                $resultSubmission = $sdk->readSubmission($submissionToRead)->getSubmission();
                if ($resultSubmission->getTitle() != null) {
                    break;
                }
            } catch (Exception $ex) {
                echo("Read attempt ".$readAttempts.", exception was: ".$ex->getMessage()."\n");
            }
        }
        return $resultSubmission;
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     */
    public static function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @param $sdk
     * @param $classtitle
     */
    public static function clearClasses($sdk, $classtitle)
    {
        $class = new TiiClass();
        $class->setTitle($classtitle);
        $findclass = $sdk->findClasses($class)->getClass();
        foreach ($findclass->getClassIds() as $classid) {
            $class->setClassId($classid);

            $membership = new TiiMembership();
            $membership->setClassId($classid);
            $memberships = $sdk->findMemberships($membership);
            foreach ($memberships as $membership) {
                fwrite(STDOUT, 'Delete membership ' . $membership->getMembershipId() . PHP_EOL);
                $sdk->deleteMembership($membership);
            }
            $sdk->deleteClass($class);
        }
    }
}
