@block @block_activity_results
Feature: The activity results block displays student scores
  In order to be display student scores
  As a user
  I need to properly configure the activity results block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  Scenario: Try to configure the block to use an activity without grades
    Given I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
    And I follow "C1"
    And I add the "Activity results" block
    And I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 1 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Percentages |
      | id_config_nameformat | Display full names |
    And I press "Save changes"
    When I follow "Test assignment"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | id_grade_modgrade_type | None |
    And I press "Save and return to course"
    Then I should see "Error: the activity selected uses a grading method that is not supported by this block." in the "Activity results" "block"
