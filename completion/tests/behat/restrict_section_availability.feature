@core @core_completion
Feature: Restrict sections availability through completion or grade conditions
  In order to control section's contents access through activities completion or grade condition
  As a teacher
  I need to restrict sections availability using different conditions

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable completion tracking | 1 |
      | Enable conditional access | 1 |
    And I log out

  @javascript
  Scenario: Show section greyed-out to student when completion condition is not satisfied
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I follow "Edit settings"
    And I fill the moodle form with:
      | Enable completion tracking | Yes |
    And I press "Save changes"
    And I add a "Label" to section "1" and I fill the form with:
      | Label text | Test label |
      | Completion tracking | Students can manually mark the activity as completed |
    And I add a "Page" to section "2" and I fill the form with:
      | Name | Test page name |
      | Description | Test page description |
      | Page content | Test page contents |
    When I edit the section "2" and I fill the form with:
      | id_conditioncompletiongroup_0_conditionsourcecmid | Test label |
      | id_conditioncompletiongroup_0_conditionrequiredcompletion | must be marked complete |
      | Before section can be accessed | Show section greyed-out, with restriction information |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Not available until the activity Test label is marked complete."
    And I should not see "Test page name"
    And I press "Not completed: Test label. Select to mark as complete."
    And I should see "Test page name"
    And I should not see "Not available until the activity Test label is marked complete."

  @javascript
  Scenario: Show section greyed-out to student when grade condition is not satisfied
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Grade assignment |
      | Description | Grade this assignment to revoke restriction on restricted assignment |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I add a "Page" to section "2" and I fill the form with:
      | Name | Test page name |
      | Description | Restricted section page resource, till grades in Grade assignment is at least 20% |
      | Page content | Test page contents |
    And I edit the section "2" and I fill the form with:
      | id_conditiongradegroup_0_conditiongradeitemid | 2 |
      | id_conditiongradegroup_0_conditiongrademin | 20 |
      | Before section can be accessed | 1 |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    Then I should see "Not available until you achieve a required score in Grade assignment"
    And "Test page name" activity should be hidden
    And I follow "Grade assignment"
    And I press "Add submission"
    And I fill the moodle form with:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grade assignment"
    And I follow "View/grade all submissions"
    And I click on "Grade Student First" "link" in the "Student First" "table_row"
    And I fill the moodle form with:
      | Grade | 21 |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And "Test page name" activity should be visible
    And I should not see "Not available until you achieve a required score in Grade assignment"