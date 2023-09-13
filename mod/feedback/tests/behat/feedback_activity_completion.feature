@mod @mod_feedback @core_completion
Feature: View activity completion in the feedback activity
  In order to have visibility of feedback completion requirements
  As a student
  I need to be able to view my feedback completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity         | feedback      |
      | course           | C1            |
      | idnumber         | mh1           |
      | name             | Music history |
      | section          | 1             |
      | completion       | 2             |
      | completionview   | 1             |
      | completionsubmit | 1             |
    And I am on the "Music history" "feedback activity" page logged in as teacher1
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
        | Question               | What is your favourite instrument |
        | Label                  | instrument1                       |
        | Multiple choice type   | Multiple choice - single answer   |
        | Multiple choice values | drums\guitar\hurdygurdy           |
    And I log out

  Scenario: View automatic completion items as a teacher
    When I am on the "Music history" "feedback activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Submit feedback" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "feedback activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "todo"
    And the "Submit feedback" completion condition of "Music history" is displayed as "todo"
    When I follow "Answer the questions"
    And I set the field "drums" to "1"
    And I press "Submit your answers"
    And I press "Continue"
    And I am on the "Music history" "feedback activity" page
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Submit feedback" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "feedback activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "feedback activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
