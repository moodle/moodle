@core @core_completion
Feature: Restrict sections availability through completion or grade conditions
  In order to control section's contents access through activities completion or grade condition
  As a teacher
  I need to restrict sections availability using different conditions

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | First    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | name             | intro                                                                             | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | submissiondrafts | content            |
      | assign   | C1     | 1       | Grade assignment | Grade this assignment to revoke restriction on restricted assignment              | 1                                   | 0                             | 0                |                    |
      | page     | C1     | 2       | Test page name   | Restricted section page resource, till grades in Grade assignment is at least 20% |                                     |                               |                  | Test page contents |

  @javascript
  Scenario: Show section greyed-out to student when completion condition is not satisfied
    Given the following "activities" exist:
      | activity | course | section | intro      | completion | idnumber |
      | label    | C1     | 1       | Test label | 1          | 1        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | cm | Test label |
      | Required completion status | must be marked complete |
    And I press "Save changes"
    And I am on the "Course 1" course page logged in as "student1"
    Then I should see "Not available unless: The activity Test label is marked complete"
    And I should not see "Test page name"
    And I toggle the manual completion state of "Test label"
    And I should see "Test page name"
    And I should not see "Not available unless: The activity Test label is marked complete"

  @javascript
  Scenario: Show section greyed-out to student when grade condition is not satisfied
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | id     | Grade assignment |
      | min    | 1                |
      | minval | 20               |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as "student1"
    Then I should see "Not available unless: You achieve higher than a certain score in Grade assignment"
    And "Test page name" activity should be hidden
    And I am on the "Grade assignment" "assign activity" page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I log out
    And I am on the "Grade assignment" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I go to "Student First" "Grade assignment" activity advanced grading page
    And I change window size to "medium"
    And I set the following fields to these values:
      | Grade | 21 |
    And I press "Save changes"
    And I follow "Edit settings"
    And I am on the "Course 1" Course page logged in as student1
    And "Test page name" activity should be visible
    And I should not see "Not available unless: You achieve higher than a certain score in Grade assignment"
