@core @core_course @javascript
Feature: Drag and drop a file onto a course section
  In order to add resources quickly
  As a teacher
  I need to drag a file from my computer onto a section of the course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | numsections |
      | Course 1 | C1        | 0        | topics | 2           |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | One      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: A teacher drops a file onto a course section
    Given I am on the "Course 1" course page logged in as "teacher1"
    And I turn editing mode on
    When I drop file "lib/tests/fixtures/empty.txt" on "#section-1" "css_element"
    Then I should see "empty" in the "#section-1" "css_element"
