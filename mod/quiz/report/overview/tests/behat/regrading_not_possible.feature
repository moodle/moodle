@mod @mod_quiz @quiz @quiz_overview @javascript
Feature: Quiz regrade when not possible
  In order avoid errors
  As a teacher
  I need the system to prevent impossible regrade scenarios

  Background:
    Given the following "users" exist:
      | username | firstname | lastname  |
      | teacher  | Mark      | Allwright |
      | student  | Student   | One       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | student  | C1     | student        |
    And the following "activities" exist:
      | activity   | name                       | course | idnumber |
      | quiz       | Quiz for testing regrading | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | template    | name  |
      | Test questions   | multichoice | one_of_four | MC    |
    And quiz "Quiz for testing regrading" contains the following questions:
      | question | page | maxmark |
      | MC       | 1    | 10.0    |
    And user "student" has attempted "Quiz for testing regrading" with responses:
      | slot | response |
      |   1  | B        |

  Scenario: Try a regrade after the question has been edited to have a different number of choices
    # Edit the question so that V2 has the fourth choice removed.
    Given I am on the "MC" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Choice 4      |  |
      | id_feedback_3 |  |
    And I press "id_submitbutton"

    # Try a regrade, and verify what happened is reported.
    When I am on the "Quiz for testing regrading" "mod_quiz > grades report" page
    And I press "Regrade all"

    Then I should see "Quiz for testing regrading"
    And I should see "The following questions could not be regraded in attempt 1 by Student One"
    And I should see "Slot 1: The number of choices in the question has changed."
    And I should see "Finished regrading (1/1)"
    And I should see "Regrade completed"
    And I press "Continue"

    # These next tests just serve to check we got back to the report.
    And I should see "Quiz for testing regrading"
    And I should see "Overall number of students achieving grade ranges"
