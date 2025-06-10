<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Integrations\PhpSdk\TiiPeermarkAssignment;

class TiiPeermarkAssignmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TiiPeermarkAssignment
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
        $this->object = new TiiPeermarkAssignment();
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
    public function testSetDistributedReviews()
    {
        $expected = 1;
        $this->object->setDistributedReviews($expected);
    }

    /**
     *
     */
    public function testGetDistributedReviews()
    {
        $expected = 1;
        $this->object->setDistributedReviews($expected);
        $result = $this->object->getDistributedReviews();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSelectedReviews()
    {
        $expected = 1;
        $this->object->setSelectedReviews($expected);
    }

    /**
     *
     */
    public function testGetSelectedReviews()
    {
        $expected = 1;
        $this->object->setSelectedReviews($expected);
        $result = $this->object->getSelectedReviews();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSelfReviewRequired()
    {
        $expected = true;
        $this->object->setSelfReviewRequired($expected);
    }

    /**
     *
     */
    public function testGetSelfReviewRequired()
    {
        $expected = true;
        $this->object->setSelfReviewRequired($expected);
        $result = $this->object->getSelfReviewRequired();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetShowReviewerNames()
    {
        $expected = true;
        $this->object->setShowReviewerNames($expected);
    }

    /**
     *
     */
    public function testGetShowReviewerNames()
    {
        $expected = true;
        $this->object->setShowReviewerNames($expected);
        $result = $this->object->getShowReviewerNames();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetNonSubmittersToReview()
    {
        $expected = true;
        $this->object->setNonSubmittersToReview($expected);
    }

    /**
     *
     */
    public function testGetNonSubmittersToReview()
    {
        $expected = true;
        $this->object->setNonSubmittersToReview($expected);
        $result = $this->object->getNonSubmittersToReview();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetSubmittersReadAllPapers()
    {
        $expected = true;
        $this->object->setSubmittersReadAllPapers($expected);
    }

    /**
     *
     */
    public function testGetSubmittersReadAllPapers()
    {
        $expected = true;
        $this->object->setSubmittersReadAllPapers($expected);
        $result = $this->object->getSubmittersReadAllPapers();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetStudentsReadAllPapers()
    {
        $expected = true;
        $this->object->setStudentsReadAllPapers($expected);
    }

    /**
     *
     */
    public function testGetStudentsReadAllPapers()
    {
        $expected = true;
        $this->object->setStudentsReadAllPapers($expected);
        $result = $this->object->getStudentsReadAllPapers();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetFullCreditIfCompleted()
    {
        $expected = true;
        $this->object->setFullCreditIfCompleted($expected);
    }

    /**
     *
     */
    public function testGetFullCreditIfCompleted()
    {
        $expected = true;
        $this->object->setFullCreditIfCompleted($expected);
        $result = $this->object->getFullCreditIfCompleted();

        $this->assertEquals($expected,$result);
    }

    /**
     *
     */
    public function testSetToDelete()
    {
        $expected = true;
        $this->object->setToDelete($expected);
    }

    /**
     *
     */
    public function testGetToDelete()
    {
        $expected = true;
        $this->object->setToDelete($expected);
        $result = $this->object->getToDelete();

        $this->assertEquals($expected,$result);
    }

}
