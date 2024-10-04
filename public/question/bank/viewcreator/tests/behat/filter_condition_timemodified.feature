@qbank @qbank_viewcreator @javascript
Feature: Time modified filter condition
  As a teacher
  In order to organise my questions
  I want to filter the list of questions by the time and date of last modification

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name    | intro              | course | idnumber |
      | qbank      | Qbank 1 | Question bank 1    | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name            | questiontext               |
      | Test questions   | truefalse | First question  | Answer the first question  |
      | Test questions   | truefalse | Second question | Answer the second question |
      | Test questions   | truefalse | Third question  | Answer the third question  |
    And the following "core_question > updated questions" exist:
      | questioncategory | question        | name            | timemodified           |
      | Test questions   | First question  | First question  | ## 2024-01-10 10:00 ## |
      | Test questions   | Second question | Second question | ## 2024-01-10 11:00 ## |
      | Test questions   | Third question  | Third question  | ## 2024-01-10 12:00 ## |
    Given I am on the "Qbank 1" "core_question > question bank" page logged in as "admin"
    And I should see "First question"
    And I should see "Second question"
    And I should see "Third question"

  Scenario: Filter by questions modified before time
    When I add question bank filter "Time modified"
    And the field "Time modified before" matches value "## now ##%FT%R##"
    And I set the field "Time modified before" to "2024-01-10T10:59"
    And I press "Apply filters"
    Then I should see "First question"
    And I should not see "Second question"
    And I should not see "Third question"

  Scenario: Filter by questions modified after time
    When I add question bank filter "Time modified"
    And I set the field "Select dates" to "After"
    And the field "Time modified after" matches value "## midnight 1 week ago ##%FT%R##"
    And I set the field "Time modified after" to "2024-01-10T10:59"
    And I press "Apply filters"
    Then I should not see "First question"
    And I should see "Second question"
    And I should see "Third question"

  Scenario: Filter by questions modified between times
    When I add question bank filter "Time modified"
    And I set the field "Select dates" to "Between"
    And I set the field "Time modified after" to "2024-01-10T10:59"
    And I set the field "Time modified before" to "2024-01-10T11:01"
    And I press "Apply filters"
    Then I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"

  Scenario: Apply filter between invalid times
    When I add question bank filter "Time modified"
    And I set the field "Select dates" to "Between"
    And I set the field "Time modified after" to "2024-01-10T10:59"
    And I set the field "Time modified before" to "2024-01-10T11:01"
    And I press "Apply filters"
    And I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
    And I set the field "Time modified after" to "2024-01-10T11:01"
    And I set the field "Time modified before" to "2024-01-10T10:59"
    And I press "Apply filters"
    # Invalid filters should not be applied.
    And I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
    # Invalid values should not be set in the URL.
    And I reload the page
    And the field "Time modified after" matches value "2024-01-10T10:59"
    And the field "Time modified before" matches value "2024-01-10T11:01"
    And I should not see "First question"
    And I should see "Second question"
    And I should not see "Third question"
