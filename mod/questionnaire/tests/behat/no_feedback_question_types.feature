@mod @mod_questionnaire
Feature: In questionnaire, certain questions will not activate feedback options.
  In order to prevent feedback options
  As a teacher
  I must not add question types that activate feedback.

  @javascript
  Scenario: Create a questionnaire with no feedback questions types.
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
      | activity | name | description | course | idnumber | resume | navigate |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 | 1 | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Advanced settings" in current page administration
    Then I should not see "Feedback options"
    And I follow "Questions"
    Then I should see "Add questions"
    And I add a "Check Boxes" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Question Text | Select one or two choices only |
      | Possible answers | One,Two,Three,Four |
    Then I should see "[Check Boxes] (Q1)"
    And I add a "Date" question and I fill the form with:
      | Question Name | Q2 |
      | Yes | y |
      | Question Text | Enter today's date |
    Then I should see "[Date] (Q2)"
    And I add a "Essay Box" question and I fill the form with:
      | Question Name | Q4 |
      | No | n |
      | Response format | 0 |
      | Input box size | 10 lines |
      | Question Text | Enter your essay |
    Then I should see "[Essay Box] (Q4)"
    And I add a "Label" question and I fill the form with:
      | Question Text | Section header |
    Then I should see "[Label]"
    And I add a "Numeric" question and I fill the form with:
      | Question Name | Q5 |
      | Yes | y |
      | Max. digits allowed | 4 |
      | Nb of decimal digits | 1 |
      | Question Text | Enter a number with a decimal |
    Then I should see "[Numeric] (Q5)"
    And I add a "Text Box" question and I fill the form with:
      | Question Name | Q8 |
      | No | n |
      | Input box length | 10 |
      | Max. text length | 15 |
      | Question Text | Enter some text |
    Then I should see "[Text Box] (Q8)"
    And I follow "Advanced settings"
    And I should not see "Feedback options"
    And I log out