@mod @mod_bigbluebuttonbn @core_form @course
Feature: The recording can be managed through the room page and as a user I can interact with the table

  Background:  Make sure that import recording is enabled and course, activities and recording exists
    Given a BigBlueButton mock server is configured
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
    And the following "activities" exist:
      | activity        | name           | intro                           | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn | name        | description   | status | starttime |
      | RoomRecordings  | Recording 1 | Description 1 | 2      | 1619666194  |
      | RoomRecordings  | Recording 2 | Description 2 | 2      | 1639668194  |
      | RoomRecordings  | Recording 3 | Description 3 | 2      | 1629666194  |
      | RoomRecordings  | Recording 4 | Description 4 | 2      | 1649666194  |

  @javascript
  Scenario: Recording should be sortable by date
    Given I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as admin
    Then I click on "th[data-yui3-col-id='date'] .yui3-datatable-sort-indicator" "css_element"
    Then "Recording 1" "text" should appear before "Recording 3" "text"
    Then "Recording 3" "text" should appear before "Recording 2" "text"
    Then "Recording 2" "text" should appear before "Recording 4" "text"
    Then I click on "th[data-yui3-col-id='date'] .yui3-datatable-sort-indicator" "css_element"
    Then "Recording 1" "text" should appear after "Recording 3" "text"
    Then "Recording 3" "text" should appear after "Recording 2" "text"
    Then "Recording 2" "text" should appear after "Recording 4" "text"
