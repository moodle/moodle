@mod @mod_quiz @quiz @quiz_overview @javascript
Feature: Re-opening Never submitted quiz attempts
    In order to cut some slack to students who forgot to submit their quiz attempt
    As a teacher
    I need to be able to re-open selected attempts.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  |
      | teacher  | Mark      | Allwright |
      | student  | Freddy    | Forgetful |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student  | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name |
      | Test questions   | truefalse   | TF   |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | question | page |
      | TF       | 1    |
    And user "student" has started an attempt at quiz "Test quiz"
    And the attempt at "Test quiz" by "student" was never submitted

  Scenario: Attempt can be re-opened
    Given I am on the "Test quiz" "mod_quiz > Grades report" page logged in as teacher
    When I press "Re-open"
    And I should see "Are you sure you wish to re-open quiz attempt 1 by Freddy Forgetful?" in the "Confirm re-open" "dialogue"
    And I should see "The re-opened attempt will remain open so that it can be continued." in the "Confirm re-open" "dialogue"
    And I click on "Re-open" "button" in the "Confirm re-open" "dialogue"
    Then I should see "In progress" in the "Freddy Forgetful" "table_row"
    And "Re-open" "button" should not exist

  Scenario: Re-opening an attempt can be cancelled and then nothing happens
    Given I am on the "Test quiz" "mod_quiz > Grades report" page logged in as teacher
    And I start watching to see if a new page loads
    When I press "Re-open"
    And I click on "Cancel" "button" in the "Confirm re-open" "dialogue"
    Then a new page should not have loaded since I started watching
    And I should see "Never submitted" in the "Freddy Forgetful" "table_row"
