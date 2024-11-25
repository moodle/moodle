@mod @mod_qbank @javascript
Feature: Switching question bank when adding questions to a quiz
  In order to re-use questions
  As a teacher
  I want to be able to switch to other banks I have access to.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
      | Course 2 | C2 | weeks |
      | Course 3 | C3 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following "activities" exist:
      | activity   | name    | intro                                    | course | idnumber |
      | quiz       | Quiz 1  | Quiz 1 for testing the Add menu          | C1     | quiz1    |
      | qbank      | Qbank 1 | Question bank 1 for testing the Add menu | C1     | qbank1   |
      | qbank      | Qbank 2 | Question bank 2 for testing the Add menu | C1     | qbank2   |
      | qbank      | Qbank 3 | Question bank 3 for testing the Add menu | C2     | qbank3   |
      | qbank      | Qbank 4 | Question bank 4 for testing the Add menu | C3     | qbank4   |
    And the following "question categories" exist:
      | contextlevel    | reference  | name             |
      | Activity module | qbank1     | Test questions 1 |
      | Activity module | qbank2     | Test questions 2 |
      | Activity module | qbank3     | Test questions 3 |
      | Activity module | qbank4     | Test questions 4 |
      | Activity module | quiz1      | Test questions 5 |
    And I log in as "teacher1"
    And I am on the "Quiz 1" "mod_quiz > Edit" page

  Scenario: Switching to another bank shows the expected banks
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    When I click on "Switch bank" "button"
    Then I should see "Quiz 1"
    And I should see "Qbank 1"
    And I should see "Qbank 2"
    But I should not see "Qbank 3"

  Scenario: Searching for another shared bank shows the expected bank
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    When I click on "Switch bank" "button"
    And I open the autocomplete suggestions list
    Then "Qbank 3" "autocomplete_suggestions" should exist
    But "Qbank 4" "autocomplete_suggestions" should not exist
    And I click on "C2 - Qbank 3" item in the autocomplete list
    And I should see "Current bank: Qbank 3"
    And I should see "Test questions 3"

  Scenario: Viewing question banks not in the current course show as recently accessed
    Given I am on the "qbank1" "Activity" page
    And I am on the "qbank2" "Activity" page
    And I am on the "qbank3" "Activity" page
    And I am on the "Quiz 1" "mod_quiz > Edit" page
    When I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "Switch bank" "button"
    Then I should see "Qbank 3"
    But I should not see "Qbank 4"
