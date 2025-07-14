@gradepenalty @gradepenalty_duedate @penalty_rule
Feature: As an administrator
  I need to add new penalty rule
  I need to edit penalty rule
  I need to delete penalty rule

  Background:
    Given I log in as "admin"
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And I navigate to "Grades > Grade penalties > Manage penalty plugins" in site administration
    And I click on "Enable Late submission penalties" "checkbox"
    And I reload the page

  @javascript
  Scenario: Edit, add, and delete and insert penalty rules
    When I navigate to "Grades > Grade penalties > Late submission penalties > Penalty rules" in site administration
    # Add 5 penalty rules.
    And I click on "Edit" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I set the following fields to these values:
      | overdueby[0][number]      |  1   |
      | penalty[0]                |  10  |
      | overdueby[1][number]      |  1   |
      | penalty[1]                |  10  |
    And I click on "Save changes" "button"
    Then I should see "The overdue amount must be greater than the amount for the rule above (1 day). The penalty must be greater than the penalty for the rule above (10.0%)."
    And I set the following fields to these values:
      | overdueby[1][number]      |  2   |
      | penalty[1]                |  20  |
      | overdueby[2][number]      |  3   |
      | penalty[2]                |  101 |
    And I click on "Save changes" "button"
    Then I should see "The penalty cannot be greater than 100.0%."
    And I set the following fields to these values:
      | overdueby[2][number]      |  3   |
      | penalty[2]                |  30  |
      | overdueby[3][number]      |  4   |
      | penalty[3]                |  40  |
      | overdueby[4][number]      |  5   |
      | penalty[4]                |  50  |
      | Final penalty rule        |  100 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    Then I should see "10%" in the "≤ 1 day" "table_row"
    Then I should see "20%" in the "≤ 2 days" "table_row"
    Then I should see "30%" in the "≤ 3 days" "table_row"
    Then I should see "40%" in the "≤ 4 days" "table_row"
    Then I should see "50%" in the "≤ 5 days" "table_row"
    Then I should see "100%" in the "> 5 days" "table_row"

  @javascript
  Scenario: Override penalty rules at a course context
    When I navigate to "Grades > Grade penalties > Late submission penalties > Penalty rules" in site administration
    And I click on "Edit" "button"
    # Add 5 penalty rules.
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I click on "Add rule" "button"
    And I set the following fields to these values:
      | overdueby[0][number]      |  1   |
      | penalty[0]                |  10  |
      | overdueby[1][number]      |  2   |
      | penalty[1]                |  20  |
      | overdueby[2][number]      |  3   |
      | penalty[2]                |  30  |
      | overdueby[3][number]      |  4   |
      | penalty[3]                |  40  |
      | overdueby[4][number]      |  5   |
      | penalty[4]                |  50  |
      | Final penalty rule        |  100 |
    And I click on "Save changes" "button"
    # Override penalty rules at a course context.
    When I am on "Course 1" course homepage
    And I navigate to "Grade penalties > Penalty rules" in current page administration
    Then I should see "10%" in the "≤ 1 day" "table_row"
    Then I should see "20%" in the "≤ 2 days" "table_row"
    Then I should see "30%" in the "≤ 3 days" "table_row"
    Then I should see "40%" in the "≤ 4 days" "table_row"
    Then I should see "50%" in the "≤ 5 days" "table_row"
    Then I should see "100%" in the "> 5 days" "table_row"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | overdueby[0][number]      |  7   |
      | penalty[0]                |  20  |
      | overdueby[1][number]      |  8   |
      | penalty[1]                |  30  |
      | overdueby[2][number]      |  9   |
      | penalty[2]                |  40  |
      | overdueby[3][number]      |  10  |
      | penalty[3]                |  50  |
      | overdueby[4][number]      |  11  |
      | penalty[4]                |  60  |
      | Final penalty rule        |  100 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    Then I should see "20%" in the "≤ 7 days" "table_row"
    Then I should see "30%" in the "≤ 8 days" "table_row"
    Then I should see "40%" in the "≤ 9 days" "table_row"
    Then I should see "50%" in the "≤ 10 days" "table_row"
    Then I should see "60%" in the "≤ 11 days" "table_row"
    Then I should see "100%" in the "> 11 days" "table_row"
    # Reset.
    When I click on "Reset" "button"
    Then I click on "Continue" "button"
    Then I should see "10%" in the "≤ 1 day" "table_row"
    Then I should see "20%" in the "≤ 2 days" "table_row"
    Then I should see "30%" in the "≤ 3 days" "table_row"
    Then I should see "40%" in the "≤ 4 days" "table_row"
    Then I should see "50%" in the "≤ 5 days" "table_row"
    Then I should see "100%" in the "> 5 days" "table_row"
