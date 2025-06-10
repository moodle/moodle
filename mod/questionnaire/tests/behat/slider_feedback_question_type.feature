@mod @mod_questionnaire
Feature: In questionnaire, slider questions can be defined with scores attributed to specific answers, in order
  to provide score dependent feedback.
  In order to define a feedback question
  As a teacher
  I must add a required slider question type.

  @javascript
  Scenario: Create a questionnaire with a slider question type and verify that feedback options exist.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name | description | course | idnumber | resume | navigate |
      | questionnaire | Test questionnaire | Test questionnaire description | C1 | questionnaire0 | 1 | 1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test questionnaire"
    And I follow "Feedback"
    Then I should not see "Display Scores"
    And I navigate to "Questions" in current page administration
    Then I should see "Add questions"
    And I add a "Slider" question and I fill the form with:
      | Question Name                | Q1                     |
      | Question Text                | Slider question test 1 |
      | Left label                   | Left                   |
      | Right label                  | Right                  |
      | Centre label                 | Center                 |
      | Minimum slider range (left)  | -5                     |
      | Maximum slider range (right) | 5                      |
      | Slider starting value        | 0                      |
      | Slider increment value       | 1                      |
    Then I should see " [Slider] (Q1)"
    And I should see "Slider question test"
    And I follow "Feedback"
    Then I should not see "Display Scores"
    And I navigate to "Questions" in current page administration
    Then I should see "Add questions"
    And I add a "Slider" question and I fill the form with:
      | Question Name                | Q2                     |
      | Question Text                | Slider question test 2 |
      | Left label                   | Left                   |
      | Right label                  | Right                  |
      | Centre label                 | Center                 |
      | Minimum slider range (left)  | 0                      |
      | Maximum slider range (right) | 5                      |
      | Slider starting value        | 0                      |
      | Slider increment value       | 1                      |
    Then I should see " [Slider] (Q2)"
    And I add a "Slider" question and I fill the form with:
      | Question Name                | Q3                     |
      | Question Text                | Slider question test 3 |
      | Left label                   | Left                   |
      | Right label                  | Right                  |
      | Centre label                 | Center                 |
      | Minimum slider range (left)  | 0                      |
      | Maximum slider range (right) | 2                      |
      | Slider starting value        | 0                      |
      | Slider increment value       | 1                      |
    Then I should see " [Slider] (Q3)"
    And I should see "Slider question test"
    And I follow "Feedback"
    And I should see "Feedback options"
    And I should see "Display Scores"
    And I set the field "id_feedbacksections" to "Feedback sections"
    And I set the field "id_feedbackscores" to "Yes"
    And I set the field "id_feedbacknotes" to "These are the main Feedback notes"
    And I press "Save settings and edit Feedback Sections"
    Then I should see "[New section] section questions"
    And I follow "[New section] section questions"
    Then I should see "Add question to section"
    And I should not see "Q1"
    And I should see "Q2"
    And I log out
