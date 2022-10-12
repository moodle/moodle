@mod @mod_survey @javascript
Feature: A teacher navigates to response reports of students
  If survey activity is configured for critical students
  Only questions and particiats pages should be visible under response reports

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 1 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1 | 0 | 1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | name             | course | idnumber  | template |
      | survey   | Test survey name | C1     | survey1   | 5        |

  Scenario: Only questions and participants page should be available under response reports as teacher
    Given I am on the "Test survey name" "survey activity" page logged in as student1
    And I set the field "At what moment in class were you most engaged as a learner?" to "most engaged as student1"
    And I set the field "At what moment in class were you most distanced as a learner?" to "most distanced as student1"
    And I set the field "What action from anyone in the forums did you find most affirming or helpful?" to "most helpful student1"
    And I set the field "What action from anyone in the forums did you find most puzzling or confusing?" to "most confusing student1"
    And I set the field "What event surprised you most?" to "most surprised student1"
    And I press "Submit"
    And I press "Continue"
    And I am on the "Test survey name" "survey activity" page logged in as student2
    And I set the field "At what moment in class were you most engaged as a learner?" to "most engaged as student2"
    And I set the field "At what moment in class were you most distanced as a learner?" to "most distanced as student2"
    And I set the field "What action from anyone in the forums did you find most affirming or helpful?" to "most helpful student2"
    And I set the field "What action from anyone in the forums did you find most puzzling or confusing?" to "most confusing student2"
    And I set the field "What event surprised you most?" to "most surprised student1"
    And I press "Submit"
    And I press "Continue"
    When I am on the "Test survey name" "survey activity" page logged in as teacher1
    And I navigate to "Response reports" in current page administration
    Then I should not see "Summary"
    And I should not see "Scales"
    And I should see "Questions"
    And I should see "Participants"
