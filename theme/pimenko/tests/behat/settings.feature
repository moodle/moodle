@theme_pimenko
Feature: Check all settings are effective

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
      | Course 2 | C2        | 0        | 1                | 0                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
    And the following "activity" exists:
      | activity       | page                     |
      | course         | C1                       |
      | idnumber       | page1                    |
      | name           | Music history            |
      | intro          | A lesson learned in life |
      | completion     | 2                        |
      | completionview | 1                        |

  Scenario: Check Enable display of moodle activity completion
    Given I navigate to "Appearance > Pimenko" in site administration
    And I set the field "Enable display of moodle activity completion" to "1"
    When I am on the "Music history" "page activity" page logged in as teacher1
    Then ".activity-complete #completion-block" "css_element" should exist
