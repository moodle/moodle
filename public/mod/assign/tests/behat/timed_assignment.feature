@mod @mod_assign
Feature: In a timed assignment, students should confirm before starting the timer
  In order to submit a timed assignment
  As a student
  I need to confirm to begin the assignment before the timer starts

  Background:
    Given the following config values are set as admin:
      | config          | value | plugin |
      | enabletimelimit | 1     | assign |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "activities" exist:
      | activity | course | name               | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | teamsubmission | duedate                 | timelimit |
      | assign   | C1     | Timed assignment 1 | 1                             | 1                              | 2097152                            | 0              | ##+1 hours 30 minutes## | 60        |
      | assign   | C1     | Group assignment 2 | 1                             | 1                              | 2097152                            | 1              | ##+1 hours 30 minutes## | 60        |

  Scenario: Access a timed assignment from the course page
    Given I am on the "Timed assignment 1" Activity page logged in as student1
    And "Begin assignment" "link" should exist
    When I reload the page
    Then "Begin assignment" "link" should exist
    And "#mod_assign_timelimit_block" "css_element" should not exist

  @javascript
  Scenario: Access a timed assignment from the Dashboard
    Given I am logged in as student1
    When I click on "Timed assignment 1" "link" in the "Calendar" "block"
    And I click on "Add submission" "link" in the ".modal-footer" "css_element"
    Then "Begin assignment" "link" should exist
    And I reload the page
    And "Begin assignment" "link" should exist
    And "#mod_assign_timelimit_block" "css_element" should not exist
    # Repeat the steps to confirm timer doesn't start automatically.
    And I follow "Dashboard"
    And I click on "Timed assignment 1" "link" in the "Calendar" "block"
    And I click on "Add submission" "link" in the ".modal-footer" "css_element"
    And "Begin assignment" "link" should exist
    And "#mod_assign_timelimit_block" "css_element" should not exist
    # Now start the timer.
    And I click on "Begin assignment" "link"
    And I click on "Begin assignment" "button" in the ".modal-footer" "css_element"
    And "#mod_assign_timelimit_block" "css_element" should exist

  @javascript
  Scenario: Access a timed group assignment from the Dashboard
    Given the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | CG1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | CG1   |
      | student2 | CG1   |
    And I am logged in as student1
    When I click on "Group assignment 2" "link" in the "Calendar" "block"
    And I click on "Add submission" "link" in the ".modal-footer" "css_element"
    Then "Begin assignment" "link" should exist
    And I reload the page
    And "Begin assignment" "link" should exist
    And "#mod_assign_timelimit_block" "css_element" should not exist
    # Repeat the steps to confirm timer doesn't start automatically.
    And I follow "Dashboard"
    And I click on "Group assignment 2" "link" in the "Calendar" "block"
    And I click on "Add submission" "link" in the ".modal-footer" "css_element"
    And "Begin assignment" "link" should exist
    And "#mod_assign_timelimit_block" "css_element" should not exist
    # Now start the timer.
    And I click on "Begin assignment" "link"
    And I click on "Begin assignment" "button" in the ".modal-footer" "css_element"
    And "#mod_assign_timelimit_block" "css_element" should exist
    And I log out
    # Now check the submission has started for the other group member too.
    And I am on the "Group assignment 2" "assign activity" page logged in as student2
    And "Begin assignment" "link" should not exist
    And "Add submission" "button" should exist
