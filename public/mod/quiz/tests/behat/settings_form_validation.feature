@mod @mod_quiz
Feature: Settings form fields are validated
  To help me avoid mistakes
  As a teacher
  I need the quiz settings to be validated

  Background:
    Given the following "users" exist:
      | username | firstname |
      | teacher  | Teach     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | section | name        |
      | quiz     | C1     | 1       | Test quiz 1 |

  Scenario: Negative time limits are not allowed
    When I am on the "Test quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_timelimit_enabled | 1   |
      | id_timelimit_number  | -10 |
    And I press "Save and display"
    Then I should see "This duration cannot be negative"
  Scenario: Due date before open date
    When I am on the "Test quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | Open the quiz       | ##5 Jan 2020 08:00## |
      | Due date            | ##1 Jan 2020 08:00## |
    And I press "Save and display"
    Then I should see "The due date must be after the open date."
  Scenario: Due date same as open date
    When I am on the "Test quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | Open the quiz       | ##5 Jan 2020 08:00## |
      | Due date            | ##5 Jan 2020 08:00## |
    And I press "Save and display"
    Then I should see "The due date must be after the open date."
  Scenario: Due date after close date
    When I am on the "Test quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | Close the quiz       | ##1 Jan 2020 08:00## |
      | Due date             | ##5 Jan 2020 08:00## |
    And I press "Save and display"
    Then I should see "The due date must be before the close date."
  Scenario: Due date same close date
    When I am on the "Test quiz 1" "quiz activity editing" page logged in as teacher
    And I expand all fieldsets
    And I set the following fields to these values:
      | Close the quiz       | ##5 Jan 2020 08:00## |
      | Due date             | ##5 Jan 2020 08:00## |
    And I press "Save and display"
    Then I should not see "The due date must be before the close date."
