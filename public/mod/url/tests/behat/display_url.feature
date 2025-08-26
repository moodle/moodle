@mod @mod_url
Feature: Teacher can specify different display options for a url
  In order to specify different display options for a url
  As a teacher
  I need to be able to set either auto, embed, open or pop-up

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | displayoptions | 0,1,2,3,5,6 | url |

  Scenario: URL resource module with download display option to an external website shows in limited width
    Given the following "activity" exists:
      | activity    | url                             |
      | course      | C1                              |
      | idnumber    | Music history                   |
      | name        | Music history                   |
      | intro       | URL description                 |
      | externalurl | http://www.example.com/file.zip |
      | display     | 0                               |
    When I am on the "Music history" "url activity" page logged in as student1
    Then "Music history" "link" should exist
    And the "class" attribute of "body" "css_element" should contain "limitedwidth"

  Scenario: URL resource module with in pop-up display option to an external website shows in a pop-up
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | display        | 6                   |
      | popupwidth     | 800                 |
      | popupheight    | 600                 |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "class" attribute of "body" "css_element" should contain "limitedwidth"

  Scenario: URL resource module with open display option to an external website shows in the same window
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | display        | 5                   |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "class" attribute of "body" "css_element" should contain "limitedwidth"

  Scenario: URL resource module with new window display option to an external website shows in a new window
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | display        | 3                   |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "class" attribute of "body" "css_element" should contain "limitedwidth"

  Scenario: URL resource module with embed display option to an external website shows in full width
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | Music history       |
      | name           | Music history       |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | display        | 1                   |
    When I am on the "Music history" "url activity" page logged in as student1
    Then the "class" attribute of "body" "css_element" should not contain "limitedwidth"
