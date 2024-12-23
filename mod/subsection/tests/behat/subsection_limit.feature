@mod @mod_subsection
Feature: Teacher can only add subsection when certain conditions are met
  In order to limit subsections
  As an teacher
  I need to create subsections only when possible

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 5           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: We cannot add subsections when maxsections is reached
    Given the following config values are set as admin:
      | maxsections | 10 | moodlecourse |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add content" "button" in the "Section 1" "section"
    And I click on "Subsection" "link" in the ".dropdown-menu.show" "css_element"
    When the following config values are set as admin:
      | maxsections | 4 | moodlecourse |
    And I am on "Course 1" course homepage
    And I should see "You have reached the maximum number of sections allowed for a course."
