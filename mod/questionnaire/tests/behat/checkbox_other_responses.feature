@mod @mod_questionnaire
Feature: Checkbox questions can have other options that can be typed in.

  Background: Add a checkbox question to a questionnaire with an 'other' option.
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
      | Question Text | Select one or two choices only |
      | Possible answers | One,Two,Three,Four,!other |
    And I add a "Check Boxes" question and I fill the form with:
      | Question Name | Q2 |
      | No | n |
      | Question Text | Select one or two choices only |
      | Possible answers | Red,Blue,Yellow,Green,!other=Other colour |
    And I log out

  @javascript
  Scenario: Student must enter a valid value when "other" is selected.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Select one or two choices only"
    And I press "Submit questionnaire"
    Then I should see "Please answer required question #1"
    And I set the field "Other" to "checked"
    And I set the field "Other colour" to "checked"
    And I press "Submit questionnaire"
    Then I should see "There is something wrong with your answer to questions:"
    And I should see "#1. #2."
