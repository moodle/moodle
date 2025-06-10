@block @block_activity_results
Feature: The activity results block can have administrator set defaults
  In order to be customize the activity results block
  As an admin
  I need can assign some site wide defaults

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity                      | assign          |
      | course                        | C1              |
      | idnumber                      | 0001            |
      | name                          | Test assignment |
      | assignsubmission_file_enabled | 0               |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Assign some site-wide defaults to the block.
    Given the following config values are set as admin:
      | config_showbest    | 0 | block_activity_results |
      | config_showworst   | 0 | block_activity_results |
      | config_gradeformat | 2 | block_activity_results |
      | config_nameformat  | 2 | block_activity_results |
    And I am on "Course 1" course homepage
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And the following fields match these values:
      | config_showbest    | 0 |
      | config_showworst   | 0 |
      | config_gradeformat | Fractions |
      | config_nameformat  | Display only ID numbers |
    And I press "Save changes"
    Then I should see "This block's configuration currently does not allow it to show any results." in the "Activity results" "block"

  Scenario: Assign some site-wide defaults to the block and lock them.
    Given the following config values are set as admin:
      | config_showbest         | 0 | block_activity_results |
      | config_showbest_locked  | 1 | block_activity_results |
      | config_showworst        | 0 | block_activity_results |
      | config_showworst_locked | 1 | block_activity_results |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | activity_results | Course       | C1        | course-view-*   | side-pre      |
    And I am on "Course 1" course homepage
    When I configure the "Activity results" block
    And the following fields match these values:
      | config_showbest    | 0 |
      | config_showworst   | 0 |
    And the "config_showbest" "field" should be readonly
    And the "config_showworst" "field" should be readonly
    And I press "Save changes"
    Then I should see "This block's configuration currently does not allow it to show any results." in the "Activity results" "block"
