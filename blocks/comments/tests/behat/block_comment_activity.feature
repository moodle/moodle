@block @block_comments
Feature: Enable Block comments on an activity page and view comments
  In order to enable the comments block on an activity page
  As a teacher
  I can add the comments block to an activity page

  Scenario: Add the comments block on an activity page and add comments
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
      | student1 | Student | First | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name           | intro                 |
      | page    | C1      | page1    | Test page name | Test page description |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I follow "Test page name"
    And I add the "Comments" block
    And I follow "Show comments"
    And I add "I'm a comment from the teacher" comment to comments block
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test page name"
    And I follow "Show comments"
    Then I should see "I'm a comment from the teacher"
