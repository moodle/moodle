<?php

use PHPUnit\Framework\TestCase;
use \core\event\user_created;
use \core\event\user_updated;

class UserAdditionApiObserverTest extends TestCase {
    
    public function testUserCreated() {
        $event = $this->createMock(user_created::class);
        $event->expects($this->once())
              ->method('get_record_snapshot')
              ->with('user', $this->equalTo(1))
              ->willReturn((object) ['idnumber' => '123']);

        $observer = $this->getMockBuilder(useradditionapi_observer::class)
                        ->setMethods(['senduserrequest'])
                        ->getMock();

        $observer::usercreated($event);
    }

    public function testUserUpdated() {
        $event = $this->createMock(user_updated::class);
        $event->expects($this->once())
              ->method('get_record_snapshot')
              ->with('user', $this->equalTo(1))
              ->willReturn((object) ['idnumber' => '123']);

        $observer = $this->getMockBuilder(useradditionapi_observer::class)
                        ->setMethods(['senduserrequest'])
                        ->getMock();

        $observer::userupdated($event);
    }

    public function testSendUserRequest() {
        // Assuming you have a valid PHPUnit test environment
        $this->markTestSkipped("Skipped because it involves actual cURL requests");
    }
}
