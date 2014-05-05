@block @block_comments
Feature: Delete comment block messages
  In order to refine comment block's contents
  As a teacher
  In need to delete comments from courses

  @javascript
  Scenario: Delete comments with Javascript enabled
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | First | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Comments" block
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I add "Comment from student1" comment to comments block
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add "Comment from teacher1" comment to comments block
    When I delete "Comment from student1" comment from comments block
    Then I should not see "Comment from student1"
    And I delete "Comment from teacher1" comment from comments block
    And I should not see "Comment from teacher1"
