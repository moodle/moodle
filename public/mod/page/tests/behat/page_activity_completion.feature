@mod @mod_page @core_completion
Feature: View activity completion information in the Page resource
  In order to have visibility of page completion requirements
  As a student
  I need to be able to view my page completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
      | Course 2 | C2        | 0        | 1                | 0                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student1 | C2     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher1 | C2     | editingteacher |

  Scenario: A teacher can view a page resource automatic completion items
    Given the following "activity" exists:
      | activity       | page                     |
      | course         | C1                       |
      | idnumber       | page1                    |
      | name           | Music history            |
      | intro          | A lesson learned in life |
      | completion     | 2                        |
      | completionview | 1                        |
    When I am on the "Music history" "page activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition

  Scenario: A student can complete a page resource by viewing it
    Given the following "activity" exists:
      | activity       | page                     |
      | course         | C1                       |
      | idnumber       | page1                    |
      | name           | Music history            |
      | intro          | A lesson learned in life |
      | completion     | 2                        |
      | completionview | 1                        |
    When I am on the "Music history" "page activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: A teacher cannot manually mark the page activity as done
    Given the following "activity" exists:
      | activity   | page                     |
      | course     | C1                       |
      | idnumber   | page1                    |
      | name       | Music history            |
      | intro      | A lesson learned in life |
      | completion | 1                        |
    # Teacher view.
    When I am on the "Music history" "page activity" page logged in as teacher1
    Then the manual completion button for "Music history" should be disabled

  @javascript
  Scenario: A student can manually mark the page activity as done
    Given the following "activity" exists:
      | activity   | page                     |
      | course     | C1                       |
      | idnumber   | page1                    |
      | name       | Music history            |
      | intro      | A lesson learned in life |
      | completion | 1                        |
    # Teacher view.
    When I am on the "Music history" "page activity" page logged in as student1
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

  Scenario Outline: Page module manual completion button hidden if Show activity completion is set to No
    Given the following "activity" exists:
      | activity   | page                     |
      | course     | C2                       |
      | idnumber   | page1                    |
      | name       | Music history            |
      | intro      | A lesson learned in life |
      | completion | 1                        |
    When I am on the "Course 2" course page logged in as <user>
    # Course 2 has 'Show activity completion conditions' set to No, so the manual completion button should not be displayed.
    Then the manual completion button for "Music history" should not exist

    Examples:
      | user     |
      | teacher1 |
      | student1 |
