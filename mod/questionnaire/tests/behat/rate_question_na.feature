@mod @mod_questionnaire
Feature: Rate scale questions have options for displaing "N/A"
  In order to display an "N/A"
  As a teacher
  I need to enter a rate question with correct options

@javascript
  Scenario: Add a "N/A" option to an existing rate scale question
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
      | Question Text | Rate these movies from 1 to 5 |
      | Possible answers | Star Wars,Casablanca,Airplane |
    Then I should see "position 1"
    And I should see "[Rate (scale 1..5)] (Q1)"
    And I should see "Rate these movies from 1 to 5"
    And I log out

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Test questionnaire"
    And I should see "Rate these movies from 1 to 5"
    And I should not see "N/A"
    And I log out

    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Questions" in current page administration
    And I click on "input[title=Edit]" "css_element"
    And I should see "Editing Rate (scale 1..5) question"
    And I set the field "id_precise" to "1"
    And I press "Save changes"
    And I log out

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Test questionnaire"
    And I should see "Rate these movies from 1 to 5"
    And I should see "N/A"
