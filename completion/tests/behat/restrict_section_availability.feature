@core @core_completion
Feature: Restrict sections availability through completion or grade conditions
  In order to control section's contents access through activities completion or grade condition
  As a teacher
  I need to restrict sections availability using different conditions

  Background:
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

  @javascript
  Scenario: Show section greyed-out to student when completion condition is not satisfied
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Label" to section "1" and I fill the form with:
      | Label text | Test label |
      | Completion tracking | Students can manually mark the activity as completed |
    And I add a "Page" to section "2" and I fill the form with:
      | Name | Test page name |
      | Description | Test page description |
      | Page content | Test page contents |
    When I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | cm | Test label |
      | Required completion status | must be marked complete |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Not available unless: The activity Test label is marked complete"
    And I should not see "Test page name"
    And I click on "Not completed: Test label. Select to mark as complete." "icon"
    And I should see "Test page name"
    And I should not see "Not available unless: The activity Test label is marked complete"

  @javascript
  Scenario: Show section greyed-out to student when grade condition is not satisfied
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Grade assignment |
      | Description | Grade this assignment to revoke restriction on restricted assignment |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I add a "Page" to section "2" and I fill the form with:
      | Name | Test page name |
      | Description | Restricted section page resource, till grades in Grade assignment is at least 20% |
      | Page content | Test page contents |
    And I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | id     | Grade assignment |
      | min    | 1                |
      | minval | 20               |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Not available unless: You achieve a required score in Grade assignment"
    And "Test page name" activity should be hidden
    And I follow "Grade assignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Grade assignment"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student First" "table_row"
    And I set the following fields to these values:
      | Grade | 21 |
    And I press "Save changes"
    And I press "OK"
    And I follow "Edit settings"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "Test page name" activity should be visible
    And I should not see "Not available unless: You achieve a required score in Grade assignment"
