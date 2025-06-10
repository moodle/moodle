@gradingform @gradingform_rubric @javascript
Feature: Converting rubric score to grades
  In order to use and refine rubrics to grade students
  As a teacher
  I need to be able to use different grade settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "scales" exist:
      | name         | scale                                     |
      | Test scale 1 | Disappointing, Good, Very good, Excellent |

  Scenario Outline: Convert assignment rubric scores to grades.
    Given the following "activities" exist:
    | activity   | name              | intro | course | idnumber    | grade   | advancedgradingmethod_submissions |
    | assign     | Test assignment 1 | Test  | C1     | assign1     | <grade> | rubric                            |
    When I log in as "teacher1"
    And I change window size to "large"
    And I am on "Course 1" course homepage with editing mode on
    And I go to "Test assignment 1" advanced grading definition page
    And I set the following fields to these values:
      | Name | Assignment 1 rubric |
      | Description | Rubric test description |
      | Calculate grade having a minimum score of the minimum achievable grade for the rubric | <lockzeropoints> |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 20 | Level 12 | 25 | Level 13 | 40 | Level 14  | 50  |
      | Criterion 2 | Level 21 | 20 | Level 22 | 25 | Level 23 | 30 |           |     |
      | Criterion 3 | Level 31 | 10 | Level 32 | 20 |          |    |           |     |
    And I press "Save rubric and make it ready"
    And I navigate to "Assignment" in current page administration
    # Grading a student.
    And I go to "Student 1" "Test assignment 1" activity advanced grading page
    And I grade by filling the rubric with:
      | Criterion 1 | 25 |  |
      | Criterion 2 | 20 |  |
      | Criterion 3 | 10 |  |
    And I save the advanced grading form
    # Checking that the user grade is correct.
    And I should see "<studentgrade>" in the "student1@example.com" "table_row"
    And I log out

    Examples:
      | grade        | lockzeropoints | studentgrade   |
      | 100          |              1 | 55.00          |
      | 70           |              1 | 38.50          |
      | Test scale 1 |              1 | Good           |
      | 100          |                | 10.00          |
      | 70           |                | 7.00           |
      | Test scale 1 |                | Disappointing  |

  Scenario Outline: Convert forum rubric scores with a scale to grades.
    Given the following "activities" exist:
      | activity | name         | intro | course | idnumber | grade | advancedgradingmethod_submissions |
      | forum    | Test forum 1 | Test  | C1     | forum1   | 100   | rubric                            |
    And I change window size to "large"
    And I am on the "Test forum 1" "forum activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Whole forum grading > Type   | Scale                     |
      | Whole forum grading > Scale  | Default competence scale  |
      | Grading method               | Rubric                    |
    And I press "Save and display"
    And I go to "Test forum 1" advanced grading definition page
    # Defining a rubric.
    And I set the following fields to these values:
      | Name        | Forum 1 rubric          |
      | Description | Rubric test description |
    And I define the following rubric:
      | Criterion 1 | Level 11 | 1  | Level 12 | 20 | Level 13 | 40 | Level 14  | 50  |
      | Criterion 2 | Level 21 | 10 | Level 22 | 20 | Level 23 | 30 |           |     |
      | Criterion 3 | Level 31 | 5  | Level 32 | 20 |          |    |           |     |
    And I press "Save rubric and make it ready"
    When I am on the "Test forum 1" "forum activity" page logged in as teacher1
    And I press "Grade users"
    And I click on "<criterion1> points" "radio"
    And I click on "<criterion2> points" "radio"
    And I click on "<criterion3> points" "radio"
    And I press "Save"
    And I am on the "Course 1" "grades > Grader report > View" page
    Then I should see "<studentgrade>" in the "student1@example.com" "table_row"

    Examples:
      | studentgrade      | criterion1  | criterion2  | criterion3  |
      | Competent         | Level 14 50 | Level 23 30 | Level 32 20 |
      | Not yet competent | Level 11 1  | Level 21 10 | Level 31 5  |
