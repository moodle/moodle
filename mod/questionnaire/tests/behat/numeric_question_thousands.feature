@mod @mod_questionnaire
Feature: Numeric questions can specify a maximum number of digits
  If three or less, the "don't use thousands separator message" should not be displayed.
  As a teacher
  I need to specify 3 or less in the max digits in the numeric question fields

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
      | activity      | name                | description                    | course | idnumber       |
      | questionnaire | Test questionnaire  | Test questionnaire description | C1     | questionnaire0 |
      | questionnaire | Test questionnaire2 | Test questionnaire description | C1     | questionnaire1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Max. digits allowed | 3 |
      | Question Text | Enter no more than three digits |
    Then I should see "position 1"
    And I should see "[Numeric] (Q1)"
    And I should see "Enter no more than three digits"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire2"
    And I navigate to "Questions" in current page administration
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Max. digits allowed | 4 |
      | Question Text | Enter no more than four digits |
    Then I should see "position 1"
    And I should see "[Numeric] (Q1)"
    And I should see "Enter no more than four digits"
    And I log out

  @javascript
  Scenario: Student must enter no more than six digits and decimal points.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Enter no more than three digits"
    And I should not see "Do not use thousands separators."
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire2"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Enter no more than four digits"
    And I should see "Do not use thousands separators."
