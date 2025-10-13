@mod @mod_feedback
Feature: Managing feedback questions
  In order to manage feedback questions
  As a teacher
  I need to be able to create, edit and delete feedback questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name                         | course | idnumber    |
      | feedback   | Learning experience course 1 | C1     | feedback1   |
    And the following "mod_feedback > question" exists:
      | activity        | feedback1                     |
      | name            | Is it me you're looking for?  |
      | label           | q1                            |

  Scenario: Teacher can create a new feedback question
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I add a "Short text answer" question to the feedback with:
      | Question         | I can see it in your eyes |
      | Label            | q2                           |
    Then I should see "(q2) I can see it in your eyes"

  @javascript
  Scenario: Teacher can edit feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And I set the field "Question" to "Can you see it in my eyes?"
    And I press "Save changes to question"
    Then I should see "(q1) Can you see it in my eyes?"
    And I should not see "(q1) Is it me you're looking for?"

  @javascript
  Scenario: Teacher can edit and save as new feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And I set the field "Question" to "You can se it in my eyes?"
    And I press "Save as new question"
    Then I should see "(q1) Is it me you're looking for?"
    And I should see "(q1) You can se it in my eyes?"

  @javascript
  Scenario: Teacher can delete feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Delete question" in the open action menu
    And I click on "Yes" "button" in the "Confirmation" "dialogue"
    Then I should not see "(q1) Is it me you're looking for?"

  @javascript
  Scenario: Teacher can mark as required feedback questions
    Given I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Set as required" in the open action menu
    And I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    Then the field "Required" matches value "1"
    And I press "Cancel"
    And I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Set as not required" in the open action menu
    And I click on "Edit" "link" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I choose "Edit question" in the open action menu
    And the field "Required" matches value "0"

  @javascript
  Scenario: Teacher can move questions
    Given the following "mod_feedback > questions" exist:
      | activity  | label        | name                               |
      | feedback1 | q2           | I can see it in your eyes          |
      | feedback1 | q3           | I can see it in your smile         |
    And I am on the "Learning experience course 1" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "region-main" "region"
    When I click on "Move this question" "button" in the "Is it me you're looking for?" "mod_feedback > Question"
    Then I should see "After \"(q2) I can see it in your eyes\"" in the "Move this question" "dialogue"
    And I should not see "To the top of the list" in the "Move this question" "dialogue"
    And I click on "After \"(q3) I can see it in your smile\"" "link" in the "Move this question" "dialogue"
    And I click on "Move this question" "button" in the "Is it me you're looking for?" "mod_feedback > Question"
    And I click on "To the top of the list" "link" in the "Move this question" "dialogue"

  Scenario: Admin cannot answer questions if not enrolled as student
    When I am on the "Learning experience course 1" "feedback activity" page logged in as admin
    Then I should not see "Answer the questions"
    But the following "course enrolments" exist:
      | user     | course | role    |
      | admin    | C1     | student |
    And I am on the "Learning experience course 1" "feedback activity" page logged in as admin
    And I should see "Answer the questions"
