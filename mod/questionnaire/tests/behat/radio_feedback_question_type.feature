@mod @mod_questionnaire
Feature: In questionnaire, radio questions can be defined with scores attributed to specific answers, in order
  to provide score dependent feedback.
  In order to define a feedback question
  As a teacher
  I must add a required radio question type.

  @javascript
  Scenario: Create a questionnaire with a radio question type and verify that feedback options exist.
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
    And I navigate to "Advanced settings" in current page administration
    Then I should not see "Feedback options"
    And I follow "Questions"
    Then I should see "Add questions"
    And I add a "Radio Buttons" question and I fill the form with:
      | Question Name | Q6 |
      | Yes | y |
      | Horizontal | Checked |
      | Question Text | Select one choice |
      | Possible answers | 1=One,2=Two,3=Three,4=Four |
    Then I should see "[Radio Buttons] (Q6)"
    And I follow "Feedback"
    And I should see "Feedback options"
    And I should see "Display Scores"
    And I log out
