@mod @mod_h5pactivity @core_h5p @core_xapi
Feature: Report different types of interactions.
  In order to let users to review attempts
  As a user
  I need to view all valid interactions in the report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    # This test is only about reporting, we don't need to specify any valid H5P file for it.
    And the following "activities" exist:
      | activity    | name        | intro                  | course | idnumber   |
      | h5pactivity | H5P package | Test H5P description   | C1     | h5ppackage |

  Scenario: General success attempt information
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "2 out of 2"
    And I should see "Pass"
    And I should see "4 seconds"
    And I should see "This attempt is completed"

  Scenario: General failed attempt statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "0 out of 2"
    And I should see "Fail"
    And I should see "4 seconds"
    And I should see "This attempt is completed"

  Scenario: View a success choice statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | choice          | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Select the correct answers"
    And "Correct answer" "icon" should exist in the "This is also a correct answer" "table_row"
    And I should see "Score: 2 out of 2"

  Scenario: View a failed choice statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | choice          | 0        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Select the correct answers"
    And "Incorrect answer" "icon" should exist in the "Another wrong answer" "table_row"
    And I should see "Score: 0 out of 2"

  Scenario: View a success matching statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | matching        | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Drag and Drop example 1"
    And "Your answer is correct" "icon" should exist in the "Drop item A" "table_row"
    And I should see "Score: 2 out of 2"

  Scenario: View a failed matching statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | matching        | 0        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Drag and Drop example 1"
    And "Your answer is incorrect" "icon" should exist in the "Drop item A" "table_row"
    And I should see "Score: 0 out of 2"

  Scenario: View a success true-false statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | true-false      | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "The correct answer is true"
    And "Correct answer" "icon" should exist in the "True" "table_row"
    And I should see "Score: 2 out of 2"

  Scenario: View a failed true-false statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | true-false      | 0        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "The correct answer is true"
    And "Incorrect answer" "icon" should exist in the "False" "table_row"
    And I should see "Score: 0 out of 2"

  Scenario: View a success fill-in statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | fill-in         | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This an example of missing word text"
    And "Your answer is correct" "icon" should exist in the "Gap #1" "table_row"
    And I should see "first" in the "Gap #1" "table_row"
    And "Your answer is correct" "icon" should exist in the "Gap #2" "table_row"
    And I should see "second" in the "Gap #2" "table_row"
    And I should see "Score: 2 out of 2"

  Scenario: View a failed fill-in statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | fill-in         | 0        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This an example of missing word text"
    And "Your answer is incorrect" "icon" should exist in the "Gap #1" "table_row"
    And I should see "first" in the "Gap #1" "table_row"
    And I should see "something" in the "Gap #1" "table_row"
    And "Your answer is incorrect" "icon" should exist in the "Gap #2" "table_row"
    And I should see "second" in the "Gap #2" "table_row"
    And I should see "else" in the "Gap #2" "table_row"
    And I should see "Score: 0 out of 2"

  Scenario: View a success long-fill-in statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | long-fill-in    | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Please describe the novel The Hobbit"
    And I should see "The Hobbit is book"
    # Fill-in does not have a partial scope indicador, we only check the general one.
    And I should see "2 out of 2"

  Scenario: View a failed long-fill-in statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | long-fill-in    | 0        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Please describe the novel The Hobbit"
    And I should see "Who cares?"
    # Fill-in does not have a partial scope indicador, we only check the general one.
    And I should see "0 out of 2"

  # The current H5P implementation does not send a complete sequencing interaction statement
  # we check only the warning and the final result.
  Scenario: View a success sequencing statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | sequencing      | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This interaction (sequencing) does not provide tracking information"
    # Sequencing does not have a partial scope indicador, we only check the general one.
    And I should see "2 out of 2"

  # The current H5P implementation does not send a complete sequencing interaction statement
  # we check only the warning and the final result.
  Scenario: View a failed sequencing statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | sequencing      | 2        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This interaction (sequencing) does not provide tracking information"
    # Sequencing does not have a partial scope indicador, we only check the general one.
    And I should see "0 out of 2"

  # The current H5P implementation does not send a complete sequencing interaction statement
  # we check only the warning and the final result.
  Scenario: View a success other statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | other           | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This interaction (other) does not provide tracking information"
    # Other does not have a partial scope indicador, we only check the general one.
    And I should see "2 out of 2"

  # The current H5P implementation does not send a complete sequencing interaction statement
  # we check only the warning and the final result.
  Scenario: View a failed other statement
    Given the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | other           | 2        | 2        | 1        | 1          | 0       |
      | student1 | H5P package | 1       | compound        | 0        | 2        | 4        | 1          | 0       |
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    And I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "This interaction (other) does not provide tracking information"
    # Other does not have a partial scope indicador, we only check the general one.
    And I should see "0 out of 2"
