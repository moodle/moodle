@block @block_comments
Feature: Add a comment to the comments block
  In order to comment on a conversation or a topic
  As a user
  In need to add comments to courses

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
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

  @javascript
  Scenario: Add a comment with Javascript enabled
    When I add "I'm a comment from student1" comment to comments block
    Then I should see "I'm a comment from student1"

  Scenario: Add a comment with Javascript disabled
    When I follow "Show comments"
    And I add "I'm a comment from student1" comment to comments block
    Then I should see "I'm a comment from student1"
