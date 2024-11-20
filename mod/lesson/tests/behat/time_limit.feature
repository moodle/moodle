@mod @mod_lesson
Feature: A teacher can set a time limit for a lesson
  In order to restrict the time students have to complete a lesson
  As a teacher
  I need to set a time limit

  @javascript
  Scenario: Accessing as student to a lesson with time limit
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
      | activity | course | name        |
      | lesson   | C1     | Test lesson |
    And the following "mod_lesson > page" exist:
      | lesson      | qtype   | title            | content                     |
      | Test lesson | content | Lesson page name | Single lesson page contents |
    And the following "mod_lesson > answer" exist:
      | page             | answer        | jumpto    |
      | Lesson page name | Single button | This page |
    And I am on the "Test lesson" "lesson activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | timelimit[enabled]  | 1  |
      | timelimit[timeunit] | 1  |
      | timelimit[number]   | 10 |
    And I press "Save and display"
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    Then I should see "You have 10 secs to finish the lesson."
    And I wait "3" seconds
    And I should see "Time remaining"
    And I press "Single button"
    And I should see "0:00:"
    And I should see "Warning: You have 1 minute or less to finish the lesson."
    And I wait "10" seconds
    And I press "Single button"
    And I should see "You ran out of time for this lesson."
    And I should see "Your last answer may not have counted if it was answered after the time was up."
    And I should see "Congratulations - end of lesson reached"
    And I should not see "Single lesson page contents"
