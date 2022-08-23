@mod @mod_forum
Feature: A teacher can move discussions between forums
  In order to move a discussion
  As a teacher
  I need to use the move discussion selector

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: A teacher can move discussions
    Given the following "activities" exist:
      | activity   | name                   | course | idnumber     | groupmode |
      | forum      | Test forum 1           | C1     | forum        | 0         |
      | forum      | Test forum 2           | C1     | forum        | 0         |
    And the following forum discussions exist in course "Course 1":
      | user     | forum        | name         | message           |
      | student1 | Test forum 1 | Discussion 1 | Test post message |
    And I am on the "Test forum 1" "forum activity" page logged in as teacher1
    And I follow "Discussion 1"
    When I set the field "jump" to "Test forum 2"
    And I press "Move"
    Then I should see "This discussion has been moved to 'Test forum 2'."
    And I press "Move"
    And I should see "Discussion 1"
