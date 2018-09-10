@mod @mod_questionnaire
Feature: Numeric questions can specify a maximum number of digits, and minimum number of decimal places
  In order to force a limit on digits and require decimal places
  As a teacher
  I need to specify the max digits and number of decimal places in the numeric question fields

  Background: Add a numeric question to a questionnaire with a max digits and nb decimals specified
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
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Max. digits allowed | 6 |
      | Nb of decimal digits | 2 |
      | Question Text | Enter no more than six digits including the decimal point |
    Then I should see "position 1"
    And I should see "[Numeric] (Q1)"
    And I should see "Enter no more than six digits including the decimal point"
    And I log out

@javascript
  Scenario: Student must enter no more than six digits and decimal points.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Enter no more than six digits including the decimal point"
    And I set the field "Enter no more than six digits including the decimal point" to "1.23456"
    And I press "Submit questionnaire"
    Then I should see "Thank you for completing this Questionnaire."
    And I follow "Continue"
    Then I should see "Your response"
    And I should see "Test questionnaire"
    And I should see "Enter no more than six digits including the decimal point"
    And I should see "1.2345"