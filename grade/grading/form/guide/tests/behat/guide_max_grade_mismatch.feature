@gradingform @gradingform_guide
Feature: Marking guide can handle maximum grade mismatches
  In order to handle maximum grade mismatches
  As a teacher
  I should be able to set the maximum grade

  Background:
    Given the following "user" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario Outline: Marking guide maximum grade handling
    Given the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    And I set the following fields to these values:
      | Name        | Assign 1 marking guide    |
      | Description | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade Criteria 1  | Grade 1 description for students | Grade 1 description for markers | <maxscore>    |
      | Grade Criteria 2  | Grade 2 description for students | Grade 2 description for markers | 30            |
    When I press "Save marking guide and make it ready"
    Then I should see "Assign 1 marking guide Ready for use"
    # Please note: We need to add the no-break space unicode character to the warning message otherwise it will fail.
    And I should see "WARNING: Your marking guide has a maximum grade of <totalmaxscore> points but the maximum grade set in your activity is 100  The maximum score set in your marking guide will be scaled to the maximum grade in the module."
    And I should see "Intermediate scores will be converted respectively and rounded to the nearest available grade."

    Examples:
      # <totalmaxscore> value is derived from <maxscore> + maximum score assigned to criteria 2 (30).
      # Case 1: total > max score of 100.
      # Case 2: total < max score of 100.
      | maxscore | totalmaxscore |
      | 90       | 120           |
      | 50       | 80            |
