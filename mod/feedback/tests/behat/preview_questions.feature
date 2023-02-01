@mod @mod_feedback
Feature: Preview feedback questions
  In order to view the feedback questions
  As a teacher
  I need to be able to preview them

  Background:
    Given the following "users" exist:
      | username       | firstname   | lastname |
      | student        | Student     | User     |
      | editingteacher | Editing     | Teacher  |
      | teacher        | NonEditing  | Teacher  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user           | course | role           |
      | student        | C1     | student        |
      | editingteacher | C1     | editingteacher |
      | teacher        | C1     | teacher        |
    And the following "activities" exist:
      | activity   | name                | course | idnumber    |
      | feedback   | Music history | C1     | feedback0   |
    And I am on the "Music history" "feedback activity" page logged in as editingteacher
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
        | Question               | What is your favourite instrument |
        | Label                  | instrument1                       |
        | Multiple choice type   | Multiple choice - single answer   |
        | Multiple choice values | drums\guitar\hurdygurdy           |
    And I log out

  Scenario: Students cannot see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as student
    Then I should not see "Preview questions"

  Scenario: Non-editing teachers can see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as teacher
    Then I should see "Preview questions"
    And I follow "Preview questions"
    And I should see "What is your favourite instrument"

  Scenario: Editing teachers can see the Preview questions button
    When I am on the "Music history" "feedback activity" page logged in as editingteacher
    Then I should see "Preview questions"
    And I follow "Preview questions"
    And I should see "What is your favourite instrument"
