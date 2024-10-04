@mod @mod_forum
Feature: Forum posts display word count
  In order to display forum word count
  As a teacher
  I need to be able to update forum and set "Display word count" to Yes

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Forum posts display word count for single simple discussion forum
    Given the following "activities" exist:
      | activity | course | name    | type   | displaywordcount | intro             |
      | forum    | C1     | Forum 1 | single | 1                | Single forum post |
    When I am on the "Forum 1" "forum activity" page logged in as teacher1
    Then I should see "3 words"
    And I am on the "Forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the field "Display word count" to "No"
    And I press "Save and display"
    And I am on the "Forum 1" "forum activity" page
    And I should not see "3 words"

  Scenario: Forum posts display word count for blog-like forum
    Given the following "activities" exist:
      | activity | course | name    | type | displaywordcount |
      | forum    | C1     | Forum 1 | blog | 1                |
    And the following "mod_forum > discussions" exist:
      | forum   | name       | subject    | message                   |
      | Forum 1 | Blog Forum | Blog Forum | This is a blog forum post |
    When I am on the "Forum 1" "forum activity" page logged in as teacher1
    Then I should see "6 words"
    And I am on the "Forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the field "Display word count" to "No"
    And I press "Save and display"
    And I am on the "Forum 1" "forum activity" page
    And I should not see "6 words"

  Scenario Outline: Forum posts display word count for other forum types
    Given the following "activities" exist:
      | activity | course | name    | type   | displaywordcount |
      | forum    | C1     | Forum 1 | <type> | 1                |
    And the following "mod_forum > discussions" exist:
      | forum   | name      | subject   | message   |
      | Forum 1 | <typeext> | <typeext> | <message> |
    When I am on the "Forum 1" "forum activity" page logged in as teacher1
    And I follow "<typeext>"
    Then I should see "<count> words"
    And I am on the "Forum 1" "forum activity editing" page
    And I expand all fieldsets
    And I set the field "Display word count" to "No"
    And I press "Save and display"
    And I am on the "Forum 1" "forum activity" page
    And I follow "<typeext>"
    Then I should not see "<count> words"

    Examples:
      | type     | typeext         | message                         | count |
      | general  | General Forum   | General discussion in forum     | 4     |
      | eachuser | Each User Forum | This is an each user forum post | 7     |
      | qanda    | Q and A Forum   | This is a Q and A type forum    | 8     |
