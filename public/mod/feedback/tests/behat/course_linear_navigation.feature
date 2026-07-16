@mod @mod_feedback
Feature: Display the course linear navigation in the feedback pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in feedback pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber  | anonymous | publish_stats | multiple_submit | completion |
      | feedback | Feedback1 | C1     | feedback1 | 1         | 1             | 1               | 1          |
    Given the following "mod_feedback > question" exists:
      | activity        | feedback1                |
      | name            | Do you like this course? |
      | questiontype    | multichoice              |
      | label           | q1                       |
      | subtype         | r                        |
      | hidenoselect    | 1                        |
      | values          | Yes\nNo                  |
    And the following "mod_feedback > responses" exist:
      | activity  | user     | Do you like this course? |
      | feedback1 | student1 | No                       |
      | feedback1 | student2 | Yes                      |

  @javascript
  Scenario: As a student I should see the course linear navigation in feedback pages that allow it
    When I am on the "Feedback1" "feedback activity" page logged in as "student1"
    Then the course linear navigation should be visible
    And I should see "Mark as done" in the "sticky-footer" "region"
    And I should not see "Mark as done" in the "page-header" "region"
    But I follow "Answer the questions"
    And I should not see "Mark as done"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Yes | 1 |
    And I press "Submit your answers"
    And the course linear navigation should be visible
    And I should see "Mark as done" in the "sticky-footer" "region"
    And I should not see "Mark as done" in the "page-header" "region"
    And I should not see "Continue"
    And I follow "Analysis"
    And the course linear navigation should be visible
    And I should see "Mark as done" in the "sticky-footer" "region"
    And I should not see "Mark as done" in the "page-header" "region"

  @javascript
  Scenario: As a teacher I should see the course linear navigation in feedback pages that allow it
    When I am on the "Feedback1" "feedback activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    # Preview questions.
    But I click on "Preview questions" "link" in the "region-main" "region"
    And the course linear navigation should not be visible
    And I press "Continue"
    # Questions.
    And I click on "Edit questions" "link" in the "region-main" "region"
    And the course linear navigation should not be visible
    And I press "Add question"
    And I choose "Information" in the open action menu
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I press "Actions"
    And I choose "Import questions" in the open action menu
    And the course linear navigation should not be visible
    And I press "Cancel"
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My template"
    And I click on "Save" "button" in the "Save as template" "dialogue"
    # Responses.
    And I navigate to "Responses" in current page administration
    And I follow "Response number: 1"
    And the course linear navigation should not be visible
    And I click on "Go to all responses" "link" in the "sticky-footer" "region"
    # Analysis.
    And I navigate to "Analysis" in current page administration
    And the course linear navigation should not be visible
    # Templates.
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My template" "table_row"
    And I choose "Preview" in the open action menu
    And the course linear navigation should not be visible

  Scenario: The Link to next activity setting is hidden when linear navigation is enabled
    When I am on the "Feedback1" "feedback activity editing" page logged in as "teacher"
    And I expand all fieldsets
    Then I should see "After submission"
    And I should not see "Link to next activity"

  Scenario: The Link to next activity setting is shown when linear navigation is disabled
    Given the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course 2 | C2        | topics | 0               |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C2     | student        |
      | teacher  | C2     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber  |
      | feedback | Feedback2 | C2     | feedback2 |
    And the following "mod_feedback > questions" exist:
      | activity  | label | name              |
      | feedback2 | q2    | Do you like this? |
    When I am on the "Feedback2" "feedback activity editing" page logged in as "teacher"
    And I expand all fieldsets
    Then I should see "Link to next activity"
    When I am on the "Feedback2" "feedback activity" page logged in as "student1"
    And I follow "Answer the questions"
    And I set the following fields to these values:
      | Do you like this? | Yes |
    And I press "Submit your answers"
    And I should see "Continue"
