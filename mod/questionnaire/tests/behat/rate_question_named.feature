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
      | Possible answers | Star Wars,Casablanca,Airplane |
      | Named degrees    | 1=I did not like,2=Ehhh,3=I liked |
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
    # Check Row 2 with correct labels.
    And "Row 2, Star Wars: Column 2, Unanswered." "radio" should exist
    And "Row 2, Star Wars: Column 3, I did not like." "radio" should exist
    And "Row 2, Star Wars: Column 4, Ehhh." "radio" should exist
    And "Row 2, Star Wars: Column 5, I liked." "radio" should exist
    # Check Row 3 with correct labels.
    And "Row 3, Casablanca: Column 2, Unanswered." "radio" should exist
    And "Row 3, Casablanca: Column 3, I did not like." "radio" should exist
    And "Row 3, Casablanca: Column 4, Ehhh." "radio" should exist
    And "Row 3, Casablanca: Column 5, I liked." "radio" should exist
    # Check Row 4 with correct labels.
    And "Row 4, Airplane: Column 2, Unanswered." "radio" should exist
    And "Row 4, Airplane: Column 3, I did not like." "radio" should exist
    And "Row 4, Airplane: Column 4, Ehhh." "radio" should exist
    And "Row 4, Airplane: Column 5, I liked." "radio" should exist
    And I click on "Row 2, Star Wars: Column 5, I liked." "radio"
    And I click on "Row 3, Casablanca: Column 5, I liked." "radio"
    And I click on "Row 4, Airplane: Column 5, I liked." "radio"
    And I press "Submit questionnaire"
