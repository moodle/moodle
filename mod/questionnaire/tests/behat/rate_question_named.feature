@mod @mod_questionnaire @wip
Feature: Rate scale questions can use names for degrees
  In order to create questions with names for degrees
  As a teacher
  I need to enter a rate and specify specific named degrees

@javascript
  Scenario: Specify names for the degrees
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
      | Type of rate scale | Normal |
      | Question Text | What did you think of these movies? |
      | Possible answers | 1=I did not like,2=Ehhh,3=I liked,Star Wars,Casablanca,Airplane |
    Then I should see "position 1"
    And I should see "[Rate (scale 1..5)] (Q1)"
    And I should see "What did you think of these movies?"
    And I log out

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I navigate to "Answer the questions..." in current page administration
    Then I should see "Test questionnaire"
    And I should see "What did you think of these movies?"
    And I should see "I did not like"
    And I should see "Ehhh"
    And I should see "I liked"
    And I click on "Choice I liked for row Star Wars" "radio"
    And I click on "Choice I liked for row Casablanca" "radio"
    And I click on "Choice I liked for row Airplane" "radio"
    And I press "Submit questionnaire"
