@mod @mod_assign
Feature: In an assignment, students can add and edit text online
  In order to complete my submissions online
  As a student
  I need to submit my assignment editing an online form

  @javascript
  Scenario: Submit a text online and edit the submission
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_onlinetext_wordlimit_enabled | 1 |
      | assignsubmission_onlinetext_wordlimit | 10 |
      | assignsubmission_file_enabled | 0 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is more than 10 words. 1 2 3 4 5 6 7 8 9 10. |
    And I press "Save changes"
    Then I should see "Please review your submission and try again."
    And I set the following fields to these values:
      | Online text | I'm the student first submission |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "I'm the student first submission"
    And I should see "Not graded"
    And I press "Edit submission"
    And I set the following fields to these values:
      | Online text | I'm the student second submission |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "I'm the student second submission"
    And I should not see "I'm the student first submission"

  @javascript
  Scenario: Auto-draft save online text submission
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | autosavefrequency | 1 | editor_atto |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | text submission |
    # Wait for the draft auto save.
    And I wait "2" seconds
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    # Confirm draft was restored.
    Then I should see "text submission" in the "#id_onlinetext_editoreditable" "css_element"
