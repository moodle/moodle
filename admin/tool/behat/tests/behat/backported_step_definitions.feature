@tool @tool_behat
Feature: Backported behat step definitions
  In order to provide feature file compatibility between multiple Moodle versions
  As a developer
  I need to be able to use backported steps from newer Moodle versiosn

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C101 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C101 | student |
      | teacher1 | C101 | teacher |

  Scenario: I am on the course homepage
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Topic 1"

  @javascript
  Scenario: I am on the course homepage
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then I should see "Topic 1"
