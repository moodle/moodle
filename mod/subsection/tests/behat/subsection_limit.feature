@mod @mod_subsection
Feature: Teacher can only add subsection when certain conditions are met
  In order to limit subsections
  As an teacher
  I need to create subsections only when possible

  Background:
    Given I enable "subsection" "mod" plugin
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | numsections | initsections |
      | Course 1 | C1        | 0        | 5           | 1            |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: The activity chooser filter subsections when section limit is reached
    Given the following config values are set as admin:
      | maxsections | 10 | moodlecourse |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add an activity or resource" "button" in the "Section 1" "section"
    And I should see "Subsection" in the "Add an activity or resource" "dialogue"
    When the following config values are set as admin:
      | maxsections | 4 | moodlecourse |
    And I am on "Course 1" course homepage
    Then I click on "Add an activity or resource" "button" in the "Section 1" "section"
    And I should not see "Subsection" in the "Add an activity or resource" "dialogue"
