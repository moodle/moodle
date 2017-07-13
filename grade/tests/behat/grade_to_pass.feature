@core @core_grades
Feature: We can set the grade to pass value
  In order to set the grade to pass value
  As a teacher
  I assign a grade to pass to an activity while editing the activity.
  I need to ensure that the grade to pass is visible in the gradebook.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | weeks | 5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "scales" exist:
      | name | scale |
      | Test Scale 1 | Disappointing, Good, Very good, Excellent |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Validate that switching the type of grading used correctly disables grade to pass
    When I turn editing mode on
    And I add a "Assignment" to section "1"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    Then the "Grade to pass" "field" should be enabled
    And I set the field "grade[modgrade_type]" to "None"
    And the "Grade to pass" "field" should be disabled
    And I press "Save and return to course"

  @javascript
  Scenario: Create an activity with a Grade to pass value greater than the maximum grade
    When I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test Assignment 1 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | grade[modgrade_type] | Point |
      | grade[modgrade_point] | 50 |
      | Grade to pass | 100 |
    Then I should see "The grade to pass can not be greater than the maximum possible grade 50"
    And I press "Cancel"

  Scenario: Set a valid grade to pass for an assignment activity using points
    When I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test Assignment 1 |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | grade[modgrade_type] | Point |
      | grade[modgrade_point] | 50 |
      | Grade to pass | 25 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  assign Test Assignment 1" "link"
    Then the field "Grade to pass" matches value "25"
    And I am on "Course 1" course homepage
    And I follow "Test Assignment 1"
    And I follow "Edit settings"
    And I expand all fieldsets
    And I set the field "Grade to pass" to "30"
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on "Edit  assign Test Assignment 1" "link"
    And the field "Grade to pass" matches value "30"

  Scenario: Set a valid grade to pass for an assignment activity using scales
    When I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test Assignment 1 |
      | Description | Submit your online text |
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | Test Scale 1 |
      | Grade to pass | 3 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  assign Test Assignment 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "3"
    And I set the field "Grade to pass" to "4"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Assignment 1"
    And I follow "Edit settings"
    And the field "Grade to pass" matches value "4"

  Scenario: Set a invalid grade to pass for an assignment activity using scales
    When I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test Assignment 1 |
      | Description | Submit your online text |
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | Test Scale 1 |
      | Grade to pass | 10 |
    Then I should see "The grade to pass can not be greater than the maximum possible grade 4"

  Scenario: Set a valid grade to pass for workshop activity
    When I turn editing mode on
    And I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Test Workshop 1 |
      | Description | Test workshop |
      | grade | 80 |
      | Submission grade to pass | 40 |
      | gradinggrade | 20 |
      | Assessment grade to pass | 10 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  workshop Test Workshop 1 (submission)" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "40"
    And I set the field "Grade to pass" to "45"
    And I press "Save changes"
    And I click on "Edit  workshop Test Workshop 1 (assessment)" "link"
    And I expand all fieldsets
    And the field "Grade to pass" matches value "10"
    And I set the field "Grade to pass" to "15"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Workshop 1"
    And I follow "Edit settings"
    And the field "Submission grade to pass" matches value "45"
    And the field "Assessment grade to pass" matches value "15"

  Scenario: Set an invalid grade to pass for workshop activity
    When I turn editing mode on
    And I add a "Workshop" to section "1" and I fill the form with:
      | Workshop name | Test Workshop 1 |
      | Description | Test workshop |
      | grade | 80 |
      | Submission grade to pass | 90 |
      | gradinggrade | 20 |
      | Assessment grade to pass | 30 |
    Then "The grade to pass can not be greater than the maximum possible grade 80" "text" should exist in the "Submission grade to pass" "form_row"
    Then "The grade to pass can not be greater than the maximum possible grade 20" "text" should exist in the "Assessment grade to pass" "form_row"

  Scenario: Set a valid grade to pass for quiz activity
    When I turn editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name | Test Quiz 1 |
      | Grade to pass | 9.5 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  quiz Test Quiz 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "9.5"
    And I set the field "Grade to pass" to "8"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Quiz 1"
    And I follow "Edit settings"
    And the field "Grade to pass" matches value "8.00"

  Scenario: Set a valid grade to pass for lesson activity
    When I turn editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name          | Test Lesson 1 |
      | Description   | Test          |
      | Grade to pass | 90            |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  lesson Test Lesson 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Lesson 1"
    And I follow "Edit settings"
    And the field "Grade to pass" matches value "80"

  Scenario: Set a valid grade to pass for database activity
    When I turn editing mode on
    And I add a "Database" to section "1" and I fill the form with:
      | Name           | Test Database 1    |
      | Description    | Test               |
      | Aggregate type | Average of ratings |
      | Grade to pass  | 90                 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  data Test Database 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I click on "Edit settings" "link" in the "Test Database 1" activity
    And the field "Grade to pass" matches value "80"

  Scenario: Set an invalid grade to pass for forum activity
    When I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name     | Test Forum 1    |
      | Description    | Test               |
      | Aggregate type | Average of ratings |
      | Grade to pass  | 90                 |
      | scale[modgrade_point] | 60 |
    Then I should see "The grade to pass can not be greater than the maximum possible grade 60"

  Scenario: Set a valid grade to pass for forum activity
    When I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name     | Test Forum 1    |
      | Description    | Test               |
      | Aggregate type | Average of ratings |
      | Grade to pass  | 90                 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  forum Test Forum 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Forum 1"
    And I follow "Edit settings"
    And the field "Grade to pass" matches value "80"

  Scenario: Set a valid grade to pass for glossary activity
    When I turn editing mode on
    And I add a "Glossary" to section "1" and I fill the form with:
      | Name           | Test Glossary 1    |
      | Description    | Test               |
      | Aggregate type | Average of ratings |
      | Grade to pass  | 90                 |
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I click on "Edit  glossary Test Glossary 1" "link"
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Test Glossary 1"
    And I follow "Edit settings"
    And the field "Grade to pass" matches value "80"
