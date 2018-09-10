@mod @mod_questionnaire
Feature: Checkbox questions can have forced minimum and maximum numbers of boxes selected
  In order to force minimum and maximum selections
  As a teacher
  I need to specify the minimum and maximum numbers in the question fields

  Background: Add a checkbox question to a questionnaire with a min and max entered
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Check Boxes" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Min. forced responses | 1 |
      | Max. forced responses | 2 |
      | Question Text | Select one or two choices only |
      | Possible answers | One,Two,Three,Four |
    Then I should see "position 1"
    And I should see "[Check Boxes] (Q1)"
    And I should see "Select one or two choices only"
    And I log out

@javascript
  Scenario: Student must select exactly one or two boxes to submit the question.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one or two choices only"
    And I press "Submit questionnaire"
    Then I should see "Please answer Required question #1."
    And I set the field "One" to "checked"
    And I set the field "Two" to "checked"
    And I set the field "Three" to "checked"
    And I press "Submit questionnaire"
#    Then I should see "There is something wrong with your answer to question: #1." -- Need to figure out why this isn't working.
    Then I should see "For this question you must tick a maximum of 2 box(es)."