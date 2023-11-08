@gradingform @gradingform_guide @javascript
Feature: Display marking guide information to students
  In order for students to see the marking guide information
  As a teacher
  I should be able to change display settings for marking guide information

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name     | advancedgradingmethod_submissions |
      | assign   | C1     | Assign 1 | guide                             |
    And I am on the "Course 1" course page logged in as teacher1
    And I go to "Assign 1" advanced grading definition page
    # Set default grading definition and marking guide.
    # By default marking guide definition and marks per criterion are enabled.
    And I set the following fields to these values:
      | Name                                 | Assign 1 marking guide    |
      | Description                          | Marking guide description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Grade Criteria 1  | Grade 1 description for students | Grade 1 description for markers | 70            |
      | Grade Criteria 2  | Grade 2 description for students | Grade 2 description for markers | 30            |
    And I press "Save marking guide and make it ready"


  Scenario Outline: Confirm marking guide information display before student is graded
      When I am on the "Assign 1" "assign activity" page logged in as student1
      # Verify that criteria 1 and 2 name and description are displayed when student is logged in before being graded.
      Then I should see "Grade Criteria <criteriacheck>" in the "#guide-criteria .criterion.<criteriaclass> .criterionshortname" "css_element"
      And I should see "Grade <criteriacheck> description for students" in the "#guide-criteria .criterion.<criteriaclass> .criteriondescription" "css_element"

      Examples:
        |criteriacheck | criteriaclass |
        | 1            | first         |
        | 2            | last          |

    Scenario Outline: Confirm that marking guide information is not displayed after student is graded
      # Update the existing marking guide to ensure that marks per criterion is displayed.
      Given I click on "Edit the current form definition" "link"
      And I set the field "Show marks per criterion to students" to "0"
      And I press "Save"
      And I am on the "Assign 1" "assign activity" page
      And I go to "Student 1" "Assign 1" activity advanced grading page
      And I grade by filling the marking guide with:
        | Grade Criteria 1 | 50 | Excellent work! |
        | Grade Criteria 2 | 20 | Try harder      |
      And I press "Save changes"
      When I am on the "Assign 1" "assign activity" page logged in as student1
      # Confirm the marking guide information display after student is graded when marking per criterion display is disabled.
      # Confirm that overall grade is displayed.
      Then I should see "70.00 / 100.00" in the ".feedback .feedbacktable .generaltable .cell.c1.lastcol" "css_element"
      # Verify that criteria 1 and 2 name, description and remark are displayed when marking per criterion display is disabled.
      And I should see "Grade Criteria <criteriacheck>" in the "#guide0-criteria .criterion.<criteriaclass> .criterionshortname" "css_element"
      And I should see "Grade <criteriacheck> description for students" in the "#guide-criteria .criterion.<criteriaclass> .criteriondescription" "css_element"
      And I should see "<criteriaremark>" in the "#guide0-criteria .criterion.<criteriaclass> .remark" "css_element"

      Examples:
        | criteriacheck | criteriaclass | criteriaremark  |
        | 1             | first         | Excellent work! |
        | 2             | last          | Try harder      |

  Scenario Outline: Confirm that marking guide information is displayed after student is graded
    # No need to update marking guide as marking guide definition is already enabled by default
    Given I am on the "Assign 1" "assign activity" page
    And I go to "Student 1" "Assign 1" activity advanced grading page
    And I grade by filling the marking guide with:
      | Grade Criteria 1 | 50 | Excellent work! |
      | Grade Criteria 2 | 20 | Try harder      |
    And I press "Save changes"
    When I am on the "Assign 1" "assign activity" page logged in as student1
    # Confirm the marking guide information display after student is graded when marking per criterion display is enabled.
    # Confirm that overall grade is displayed.
    Then I should see "70.00 / 100.00" in the ".feedback .feedbacktable .generaltable .cell.c1.lastcol" "css_element"
    # Confirm that criteria1 name is displayed.
    # Confirm that all marking guide definition and marks per criterion are displayed.
    # Verify that criteria 1 and 2 name, description, maximum score, remark and score are all displayed.
    And I should see "Grade Criteria <criteriacheck>" in the "#guide0-criteria .criterion.<criteriaclass> .criterionshortname" "css_element"
    And I should see "Grade <criteriacheck> description for students" in the "#guide-criteria .criterion.<criteriaclass> .criteriondescription" "css_element"
    And I should see "<maxscore>" in the "#guide0-criteria .criterion.<criteriaclass> .criteriondescriptionscore" "css_element"
    And I should see "<criteriaremark>" in the "#guide0-criteria .criterion.<criteriaclass> .remark" "css_element"
    And I should see "<criteriascore>" in the "#guide0-criteria .criterion.<criteriaclass> .score" "css_element"

    Examples:
      | criteriacheck | criteriaclass | criteriaremark  | maxscore | criteriascore |
      | 1             | first         | Excellent work! | 70       | 50 / 70       |
      | 2             | last          | Try harder      | 30       | 20 / 30       |
