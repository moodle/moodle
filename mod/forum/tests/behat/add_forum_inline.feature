@mod @mod_forum @javascript
Feature: Add forum activities and discussions utilizing the inline add discussion form

  Background: Add a forum and a discussion attaching files
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity    | forum                  |
      | course      | C1                     |
      | section     | 1                      |
      | idnumber    | 0001                   |
      | name        | Test forum name        |
      | description | Test forum description |
      | type        | general                |
    And the following "mod_forum > discussion" exists:
      | forum   | 0001             |
      | course  | C1               |
      | user    | teacher1         |
      | name    | Forum post 1     |
      | message | This is the body |

  Scenario: Student can add a discussion via the inline form
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    Then I add a new discussion to "Test forum name" forum inline with:
      | Subject | Post with attachment |
      | Message | This is the body     |
