@block @block_site_main_menu @addablocklink
Feature: Additional activities block also supported in courses
  In order to use Additional activities block in a course
  As a teacher
  I need to add it to a course and check it works.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format         |
      | Course 1 | C1        | singleactivity |
      | Course 2 | C2        | topics         |
    And the following "activities" exist:
      | activity | course | name       |
      | forum    | C1     | My forum 1 |
      | forum    | C2     | My forum 2 |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | One      |
      | student1 | Student   | One      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |

  @javascript
  Scenario: Additional activities block only can be added to courses without view page
    Given I am on the "Course 1" "course" page logged in as "teacher1"
    And I turn editing mode on
    When I click on "Add a block" "link"
    Then I should see "Additional activities"
    But I am on the "Course 2" "course" page
    And I click on "Add a block" "link"
    And I should not see "Additional activities"
