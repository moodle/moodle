@core @core_completion
Feature: Set completion of other courses as criteria for completion of current course
  In order to set completion of other courses as criteria for completion of current course
  As a user
  I want to select the prerequisite courses in completion settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
      | Course 2 | C2        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |

  @javascript
  Scenario: Set completion of prerequisite course as completion criteria of current course
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Course completion" in current page administration
    And I click on "Condition: Completion of other courses" "link"
    And I set the field "Courses available" to "Course 2"
    And I press "Save changes"
    And I add the "Course completion status" block
    And I click on "View course report" "link" in the "Course completion status" "block"
    Then I should see "Course 2" in the "completion-progress" "table"
    And I should see "Student One" in the "completion-progress" "table"
