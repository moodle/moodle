@mod @mod_feedback
Feature: Saving, using and deleting feedback templates
  In order to quickly create feedbacks
  As a manager
  I need to be able to create feedback templates

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | manager  | Manager   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
      | teacher  | C2     | editingteacher |
    And the following "system role assigns" exist:
      | user    | course               | role    |
      | manager | Acceptance test site | manager |
    And the following "activities" exist:
      | activity   | name                         | course | idnumber    |
      | feedback   | Learning experience course 1 | C1     | feedback1   |
      | feedback   | Another feedback in course 1 | C1     | feedback2   |
      | feedback   | Learning experience course 2 | C2     | feedback3   |
    And I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I navigate to "Questions" in current page administration
    And I add a "Multiple choice" question to the feedback with:
      | Question         | this is a multiple choice 1 |
      | Label            | multichoice1                |
      | Multiple choice type | Multiple choice - single answer |
      | Multiple choice values | option a\noption b\noption c  |
    And I log out

  @javascript
  Scenario: Teacher can save template and re-use it in the same course only
    # Go to feedback templates and make sure none exist yet
    When I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I navigate to "Templates" in current page administration
    Then I should see "No templates available yet"
    # Save as a course template
    And I navigate to "Feedback" in current page administration
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I should see "Save as template" in the ".modal-title" "css_element"
    And I should see "Name" in the ".modal-body" "css_element"
    And I set the field "Name" to "My first template"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I should see "Template saved"
    And I navigate to "Templates" in current page administration
    And I should see "My first template"
    # Create a feedback from this template in the same course
    And I am on the "Another feedback in course 1" "feedback activity" page
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My first template" "table_row"
    And I choose "Use template" in the open action menu
    And I click on "Save" "button" in the "Use template" "dialogue"
    And I should see "this is a multiple choice 1"
    # Make sure this template is not available in another course
    And I am on the "Learning experience course 2" "feedback activity" page
    And I navigate to "Templates" in current page administration
    And I should see "No templates available yet"
    # Make sure Template cannot be created without questions
    And I am on the "Learning experience course 2" "feedback activity" page
    And I navigate to "Questions" in current page administration
    And I press "Actions"
    And I should not see "Save as template"

  @javascript
  Scenario: Teacher can append template to existing questions or remove them
    # Save feedback as a course template
    When I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I navigate to "Questions" in current page administration
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I should see "Save as template" in the ".modal-title" "css_element"
    And I should see "Name" in the ".modal-body" "css_element"
    And I set the field "Name" to "My first template"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    # Add questions to another feedback
    And I am on the "Another feedback in course 1" "feedback activity" page
    And I navigate to "Questions" in current page administration
    And I add a "Multiple choice" question to the feedback with:
      | Question         | What is your favourite subject |
      | Label            | subjectchoice                  |
      | Multiple choice type | Multiple choice - single answer   |
      | Multiple choice values | Maths\bScience\nEnglish\nOther  |
    # Import template appending items
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My first template" "table_row"
    And I choose "Use template" in the open action menu
    And I set the field "Keep existing questions and add new questions at the end" to "1"
    And I click on "Save" "button" in the "Use template" "dialogue"
    Then "What is your favourite subject" "text" should appear before "this is a multiple choice 1" "text"
    # Import template replacing items (through Preview page)
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My first template" "table_row"
    And I choose "Preview" in the open action menu
    And I press "Actions"
    And I choose "Use template" in the open action menu
    And I set the field "Override existing questions" to "1"
    And I click on "Save" "button" in the "Use template" "dialogue"
    And I should see "this is a multiple choice 1"
    And I should not see "What is your favourite subject"
    And I should see "this is a multiple choice 1"

  @javascript
  Scenario: Manager can save template as public and it will be available in any course
    When I am on the "Learning experience course 1" "feedback activity" page logged in as manager
    And I navigate to "Questions" in current page administration
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My first template"
    And I set the field "Available for all courses" to "1"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I log out
    And I am on the "Learning experience course 2" "feedback activity" page logged in as teacher
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My first template" "table_row"
    And I choose "Use template" in the open action menu
    And I set the field "Override existing questions" to "1"
    And I click on "Save" "button" in the "Use template" "dialogue"
    Then I should see "this is a multiple choice 1"

  @javascript
  Scenario: Teacher can delete course templates but can not delete public templates
    # Save feedback as both public and course template
    When I am on the "Learning experience course 1" "feedback activity" page logged in as manager
    And I navigate to "Questions" in current page administration
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My public template"
    And I set the field "Available for all courses" to "1"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My course template"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I log out
    # Login as teacher and try to delete templates
    And I am on the "Another feedback in course 1" "feedback activity" page logged in as teacher
    And I navigate to "Templates" in current page administration
    And I open the action menu in "My public template" "table_row"
    Then I should not see "Delete"
    And I choose "Preview" in the open action menu
    And I press "Actions"
    And I should not see "Delete"
    And I click on "Back" "link" in the ".tertiary-navigation" "css_element"
    And "My course template" "text" should exist in the ".coursetemplates" "css_element"
    And I open the action menu in "My course template" "table_row"
    And I choose "Delete" in the open action menu
    And I click on "Yes" "button"
    And I should see "Template deleted"
    And "My course template" "text" should not exist in the ".coursetemplates" "css_element"
    And "No templates available yet" "text" should exist in the ".coursetemplates" "css_element"

  @javascript
  Scenario: Manager can delete both course and public templates
    # Save feedback as both public and course template
    When I am on the "Learning experience course 1" "feedback activity" page logged in as manager
    And I navigate to "Questions" in current page administration
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My public template"
    And I set the field "Available for all courses" to "1"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    And I press "Actions"
    And I choose "Save as template" in the open action menu
    And I set the field "Name" to "My course template"
    And I click on "Save" "button" in the ".modal-dialog" "css_element"
    # Delete course template
    And I navigate to "Templates" in current page administration
    And "My course template" "text" should exist in the ".coursetemplates" "css_element"
    And I open the action menu in "My course template" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Are you sure you want to delete this template?"
    And I press "Yes"
    Then I should see "Template deleted"
    And "My course template" "text" should not exist in the ".coursetemplates" "css_element"
    And "No templates available yet" "text" should exist in the ".coursetemplates" "css_element"
    And "My public template" "text" should exist in the ".publictemplates" "css_element"
    # Delete course template (through Preview page)
    And I open the action menu in "My public template" "table_row"
    And I choose "Preview" in the open action menu
    And I press "Actions"
    And I choose "Delete" in the open action menu
    And I should see "Are you sure you want to delete this template?"
    And I press "Yes"
    And I should see "Template deleted"
    And "My public template" "text" should not exist in the ".publictemplates" "css_element"
    And "No templates available yet" "text" should exist in the ".publictemplates" "css_element"
