@mod @mod_url @core_completion
Feature: View activity completion information in the URL resource
  In order to have visibility of URL completion requirements
  As a student
  I need to be able to view my URL completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | displayoptions | 0,1,2,3,4,5,6 | url |

  Scenario: URL resource module displays completion conditions to teachers
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 0                   |
    When I am on the "Music history" "url activity" page logged in as teacher1
    Then "Music history" "link" should exist
    And I should see "Click on Music history to open the resource."
    And "Music history" should have the "View" completion condition

  Scenario: A student can complete a URL activity by viewing it
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 0                   |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: A teacher can view a URL activity completion conditions in embed display mode
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 1                   |
    When I am on the "Music history" "url activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition

  Scenario: A student can complete a url resource by viewing it in embed display mode
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 1                   |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: A teacher can view url resource automatic completion items in open display mode as teacher
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 5                   |
    When I am on the "Music history" "url activity" page logged in as teacher1
    And I am on the "Course 1" course page
    Then "Music history" should have the "View" completion condition

  @javascript
  Scenario: A student can view url resource automatic completion items in open display mode
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 5                   |
    When I am on the "Music history" "url activity" page logged in as student1
    And I am on the "Course 1" course page
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: An URL resource shows automatic completion conditions in pop-up display mode as teacher
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 6                   |
      | popupwidth     | 620                 |
      | popupheight    | 450                 |
    When I am on the "Music history" "url activity" page logged in as student1
    Then "Music history" should have the "View" completion condition

  Scenario: View url resource automatic completion conditions in pop-up display mode as student
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 2                   |
      | completionview | 1                   |
      | display        | 6                   |
      | popupwidth     | 620                 |
      | popupheight    | 450                 |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: A teacher cannot manually mark the url activity as done
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 1                   |
      | completionview | 1                   |
      | display        | 0                   |
    When I am on the "Music history" "url activity" page logged in as teacher1
    Then the manual completion button for "Music history" should be disabled

  @javascript
  Scenario: A student can manually mark the url activity as done
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 1                   |
      | completionview | 1                   |
      | display        | 0                   |
    When I am on the "Course 1" course page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

  @javascript
  Scenario Outline: The Mark as done completion condition will be shown on the course page for Open, In pop-up and New window display mode if the Show activity completion conditions is set to No as teacher
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 1                   |
      | completionview | 1                   |
      | display        | <display>           |
      | popupwidth     | 620                 |
      | popupheight    | 450                 |
    When I am on the "Course 1" course page logged in as teacher1
    Then "Music history" should have the "Mark as done" completion condition

    Examples:
      | display | description |
      | 0       | Auto        |
      | 6       | Popup       |
      | 3       | New         |

  @javascript
  Scenario Outline: The manual completion button will be shown on the course page for Open, In pop-up and New window display mode if the Show activity completion conditions is set to No as student
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | completion     | 1                   |
      | completionview | 1                   |
      | display        | <display>           |
      | popupwidth     | 620                 |
      | popupheight    | 450                 |
    When I am on the "Course 1" course page logged in as student1
    Then the manual completion button for "Music history" should exist
    And the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

    Examples:
      | display | description |
      | 0       | Auto        |
      | 6       | Popup       |
      | 3       | New         |
