@mod @mod_questionnaire
Feature: Rate scale questions have Other option
  In order to display an Other choice in rate question
  As a teacher
  the 'Other' option should display with textbox next to it in the question view

  Background: Create a rate question type with an 'Other' choice
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity      | name               | description                    | course | idnumber       |
      | questionnaire | Test questionnaire | Test questionnaire description | C1     | questionnaire0 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I add a "Rate (scale 1..5)" question and I fill the form with:
      | Question Name      | Q1                                                          |
      | Yes                | y                                                           |
      | Nb of scale items  | 3                                                           |
      | Type of rate scale | No duplicate choices                                        |
      | Question Text      | What are your top three movies?                             |
      | Possible answers   | Star Wars,Casablanca,Airplane,Citizen Kane,Anchorman,!other |
    Then I should see "position 1"
    And I should see "[Rate (scale 1..5)] (Q1)"
    And I should see "What are your top three movies?"
    And I log out

  @javascript
  Scenario: The student inputs text in "Other" and verifies its existence.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Test questionnaire"
    And I should see "What are your top three movies?"
    And I click on "Row 2, Star Wars: Column 2, 1." "radio"
    And I click on "Row 4, Airplane: Column 3, 2." "radio"
    And I click on "Row 3, Casablanca: Column 4, 3." "radio"
    And I click on "Row 7, !other: Column 4, 3." "radio"
    And I set the field with xpath "//input[contains(@name,'qother')]" to "Once Upon a Time"
    And I press "Submit questionnaire"
    And I press "Continue"
    Then I should see "Once Upon a Time"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "View all responses" in current page administration
    Then I should see "Other: Once Upon a Time"
