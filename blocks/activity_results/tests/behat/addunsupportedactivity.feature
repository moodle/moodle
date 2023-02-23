@block @block_activity_results
Feature: The activity results block doesn't display student scores for unsupported activity
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
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Try to configure the block to use an activity without grades
    Given the following "activities" exist:
      | activity   | name            | intro          | course | section | idnumber | assignsubmission_file_enabled |
      | assign     | Test assignment | Offline text   | C1     | 1       | assign1  | 0                             |
    And I am on "Course 1" course homepage
    And I add the "Activity results" block
    And I configure the "Activity results" block
    And I set the following fields to these values:
      | config_showbest | 1 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Display full names |
    And I press "Save changes"
    When I follow "Test assignment"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | id_grade_modgrade_type | None |
    And I press "Save and return to course"
    Then I should see "Error: the activity selected uses a grading method that is not supported by this block." in the "Activity results" "block"
