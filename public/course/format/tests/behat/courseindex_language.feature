@core @core_course @core_courseformat
Feature: The course index language should change according to user preferences.
  As a user in a course
  I should see the same language in the course index and the course content when I switch languages.

  Background:
    Given remote langimport tests are enabled
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
      | numsections      | 4        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "language pack" exists:
      | language | fr |
    And I change window size to "large"

  @javascript
  Scenario Outline: Course index is refreshed when we change language.
    Given I am on the "C1" "Course" page logged in as "<user>"
    When I follow "Language" in the user menu
    Then I should see "Language selector" user submenu
    And I follow "Français ‎(fr)‎"
    Then I should see "Généralités" in the "courseindex-content" "region"
    Examples:
      | user     |
      | student1 |
      | teacher1 |
