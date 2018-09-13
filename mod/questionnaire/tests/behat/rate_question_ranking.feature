@mod @mod_questionnaire
Feature: Rate scale questions can be used to uniquely rank options
  In order to create a ranking question
  As a teacher
  I need to enter a rate question with with "no duplicate choices" selected

@javascript
  Scenario: Add a rank the top three choices question
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
    And I add a "Rate (scale 1..5)" question and I fill the form with:
      | Question Name | Q1 |
      | Yes | y |
      | Nb of scale items | 3 |
      | Type of rate scale | No duplicate choices |
      | Question Text | What are your top three movies? |
      | Possible answers | Star Wars,Casablanca,Airplane,Citizen Kane,Anchorman |
    Then I should see "position 1"
    And I should see "[Rate (scale 1..5)] (Q1)"
    And I should see "What are your top three movies?"
    And I log out

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Test questionnaire"
    And I should see "What are your top three movies?"
    And I click on "Choice 1 for row Star Wars" "radio"
    And I click on "Choice 2 for row Airplane" "radio"
    And I click on "Choice 3 for row Casablanca" "radio"
    And I press "Submit questionnaire"
