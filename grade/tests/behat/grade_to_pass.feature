@core @core_grades @javascript
Feature: We can set the grade to pass value
  In order to set the grade to pass value
  As a teacher
  I assign a grade to pass to an activity while editing the activity.
  I need to ensure that the grade to pass is visible in the gradebook.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | weeks  | 5           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "scales" exist:
      | name         | scale                                     |
      | Test Scale 1 | Disappointing, Good, Very good, Excellent |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | section                             | 1                       |
      | idnumber                            | assign1                 |
      | name                                | Test Assignment 1       |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
    And I am on the "Course 1" course page logged in as teacher1

  Scenario: Validate that switching the type of grading used correctly disables grade to pass
    Given I turn editing mode on
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    Then the "Grade to pass" "field" should be enabled
    And I set the field "grade[modgrade_type]" to "None"
    And the "Grade to pass" "field" should be disabled
    And I press "Save and return to course"

  Scenario: Create an activity with a Grade to pass value greater than the maximum grade
    When I turn editing mode on
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "Point"
    And I set the field "grade[modgrade_point]" to "50"
    And I set the field "Grade to pass" to "100"
    And I press "Save and return to course"
    Then I should see "The grade to pass can not be greater than the maximum possible grade 50"
    And I press "Cancel"

  Scenario: Set a valid grade to pass for an assignment activity using points
    When I turn editing mode on
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | assignsubmission_onlinetext_enabled | 1 |
      | grade[modgrade_type] | Point |
      | grade[modgrade_point] | 50 |
      | Grade to pass | 25 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Assignment 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    Then the field "Grade to pass" matches value "25"
    And I am on "Course 1" course homepage
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the field "Grade to pass" to "30"
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Assignment 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And the field "Grade to pass" matches value "30"

  Scenario: Set a valid grade to pass for an assignment activity using scales
    When I turn editing mode on
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | Test Scale 1 |
      | Grade to pass | 3 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Assignment 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "3"
    And I set the field "Grade to pass" to "4"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And the field "Grade to pass" matches value "4"

  Scenario: Set a invalid grade to pass for an assignment activity using scales
    When I turn editing mode on
    And I am on the "Test Assignment 1" "assign activity" page
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | grade[modgrade_type] | Scale |
      | grade[modgrade_scale] | Test Scale 1 |
      | Grade to pass | 10 |
    And I press "Save and return to course"
    Then I should see "The grade to pass can not be greater than the maximum possible grade 4"

  Scenario: Set a valid grade to pass for workshop activity
    Given the following "activities" exist:
      | activity   | name              | course | idnumber  |
      | workshop   | Test Workshop 1   | C1     | workshop1 |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Workshop 1" "workshop activity editing" page
    And I set the following fields to these values:
      | grade | 80 |
      | Submission grade to pass | 40 |
      | gradinggrade | 20 |
      | Assessment grade to pass | 10 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Workshop 1 (submission)" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "40"
    And I set the field "Grade to pass" to "45"
    And I press "Save changes"
    And I click on grade item menu "Test Workshop 1 (assessment)" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    And the field "Grade to pass" matches value "10"
    And I set the field "Grade to pass" to "15"
    And I press "Save changes"
    And I am on the "Test Workshop 1" "workshop activity editing" page
    And the field "Submission grade to pass" matches value "45"
    And the field "Assessment grade to pass" matches value "15"

  Scenario: Set an invalid grade to pass for workshop activity
    Given the following "activities" exist:
      | activity   | name              | course | idnumber  |
      | workshop   | Test Workshop 1   | C1     | workshop1 |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Workshop 1" "workshop activity editing" page
    And I set the following fields to these values:
      | grade | 80 |
      | Submission grade to pass | 90 |
      | gradinggrade | 20 |
      | Assessment grade to pass | 30 |
    And I press "Save and return to course"
    Then "The grade to pass can not be greater than the maximum possible grade 80" "text" should exist in the "Submission grade to pass" "form_row"
    Then "The grade to pass can not be greater than the maximum possible grade 20" "text" should exist in the "Assessment grade to pass" "form_row"

  Scenario: Set a valid grade to pass for quiz activity
    Given the following "activities" exist:
      | activity   | name          | course | section | idnumber  |
      | quiz       | Test Quiz 1   | C1     | 1       | quiz1     |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Quiz 1" "quiz activity" page
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Grade to pass | 9.5 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Quiz 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "9.5"
    And I set the field "Grade to pass" to "8"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I am on the "Test Quiz 1" "quiz activity" page
    And I follow "Settings"
    And the field "Grade to pass" matches value "8.00"

  Scenario: Set a valid grade to pass for lesson activity
    Given the following "activities" exist:
      | activity   | name          | course | idnumber  |
      | lesson     | Test Lesson 1 | C1     | lesson1   |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Lesson 1" "lesson activity editing" page
    And I set the following fields to these values:
      | Grade to pass | 90            |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Lesson 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on the "Test Lesson 1" "lesson activity editing" page
    And the field "Grade to pass" matches value "80"

  Scenario: Set a valid grade to pass for lesson activity with custom decimal separator
    Given the following "activities" exist:
      | activity   | name          | course | idnumber  |
      | lesson     | Test Lesson 1 | C1     | lesson1   |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Lesson 1" "lesson activity editing" page
    And I set the following fields to these values:
      | Grade to pass | 90#50 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Lesson 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90#50"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on the "Test Lesson 1" "lesson activity editing" page
    And the field "Grade to pass" matches value "80#00"

  Scenario: Set a valid grade to pass for database activity
    Given the following "activities" exist:
      | activity   | name            | intro       | course | section | idnumber  |
      | data       | Test Database 1 | Test        | C1     | 1       | data1     |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Database 1" "data activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Ratings > Aggregate type | Average of ratings |
      | id_scale_modgrade_type   | Point              |
      | Ratings > Grade to pass  | 90                 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Database 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I am on the "Test Database 1" "data activity" page
    And I follow "Settings"
    And the field "Grade to pass" matches value "80"

  Scenario: Set an invalid grade to pass for forum activity
    Given the following "activities" exist:
      | activity    | name         | course | idnumber  |
      | forum       | Test Forum 1 | C1     | forum1    |
    And I am on the "Test Forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | Ratings > Aggregate type        | Average of ratings |
      | id_scale_modgrade_type          | Point              |
      | Ratings > Grade to pass         | 90                 |
      | Ratings > scale[modgrade_point] | 60                 |
    And I press "Save and return to course"
    Then I should see "The grade to pass can not be greater than the maximum possible grade 60"

  Scenario: Set a valid grade to pass for forum activity
    Given the following "activities" exist:
      | activity    | name         | course | idnumber  |
      | forum       | Test Forum 1 | C1     | forum1    |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | Ratings > Aggregate type | Average of ratings |
      | id_scale_modgrade_type   | Point              |
      | Ratings > Grade to pass  | 90                 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Forum 1 rating" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on the "Test Forum 1" "forum activity editing" page
    And the field "Ratings > Grade to pass" matches value "80"

  Scenario: Set a valid grade to pass for glossary activity
    Given the following "activities" exist:
      | activity    | name            | intro       | course | section | idnumber  |
      | glossary    | Test Glossary 1 | Test        | C1     | 1       | glossary1 |
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "Test Glossary 1" "glossary activity" page
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Ratings > Aggregate type | Average of ratings |
      | id_scale_modgrade_type   | Point              |
      | Ratings > Grade to pass  | 90                 |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I click on grade item menu "Test Glossary 1" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    And I expand all fieldsets
    Then the field "Grade to pass" matches value "90"
    And I set the field "Grade to pass" to "80"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I am on the "Test Glossary 1" "glossary activity" page
    And I follow "Settings"
    And the field "Grade to pass" matches value "80"
