@mod @mod_bigbluebuttonbn @core_form @course
Feature: Manage and list recordings
  As a user I am able to import existing recording into another bigbluebutton activity

  Background:  Make sure that import recording is enabled and course, activities and recording exists
    Given a BigBlueButton mock server is configured
    And the following config values are set as admin:
      | bigbluebuttonbn_importrecordings_enabled | 1 |
      | bigbluebuttonbn_importrecordings_from_deleted_enabled | 1 |
    And I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname      | shortname | category |
      | Test Course 1 | C1        | 0        |
      | Test Course 2 | C2        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
    And the following "activities" exist:
      | activity        | name            | intro                              | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings  | Test Room Recording description    | C1     | bigbluebuttonbn1 | 0    | 0                   |
      | bigbluebuttonbn | RoomRecordings1 | Test Recordings description 1      | C2     | bigbluebuttonbn2 | 0    | 1                   |
      | bigbluebuttonbn | RecordingOnly   | Test Recordings only description 1 | C2     | bigbluebuttonbn3 | 2    | 1                   |
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings |
    And the following "mod_bigbluebuttonbn > recordings" exist:
      | bigbluebuttonbn | name        | status |
      | RoomRecordings  | Recording 1 | 3      |
      | RoomRecordings  | Recording 2 | 3      |

  @javascript
  Scenario: I check we display the right information (Recording Name as name and Description)
    When I am on the "RoomRecordings1" "bigbluebuttonbn activity" page logged in as "admin"
    # We check column names regarding changes made in CONTRIB-7703.
    Then I should not see "Recording" in the ".mod_bigbluebuttonbn_recordings_table thead" "css_element"
    And I should not see "Meeting" in the ".mod_bigbluebuttonbn_recordings_table thead" "css_element"
    And I should see "Name" in the ".mod_bigbluebuttonbn_recordings_table thead" "css_element"

  @javascript
  Scenario Outline: I check that I can import recordings into the Recording activity from other activities
    When I am on the "<instancename>" "bigbluebuttonbn activity" page logged in as "admin"
    And I click on "Import recording links" "button"
    And I select "Test Course 1" from the "sourcecourseid" singleselect
    And I select "RoomRecordings" from the "sourcebn" singleselect
    # add the first recording
    And I click on "a.action-icon" "css_element" in the "Recording 1" "table_row"
    # add the second recording
    And I click on "a.action-icon" "css_element" in the "Recording 2" "table_row"
    And I click on "Go back" "button"
    Then "Recording 1" "table_row" <existence>
    And "Recording 2" "table_row" <existence>
    Examples:
      | instancename    | existence    |
      | RecordingOnly   | should exist |
      | RoomRecordings1 | should exist |

  @javascript
  Scenario: I check that I can import recordings into the Recording activity and then if I delete them
  they are back into the pool to be imported again
    When I am on the "RoomRecordings1" "bigbluebuttonbn activity" page logged in as "admin"
    And I change window size to "large"
    And I click on "Import recording links" "button"
    And I select "Test Course 1" from the "sourcecourseid" singleselect
    And I select "RoomRecordings" from the "sourcebn" singleselect
    # add the first recording
    And I click on "a.action-icon" "css_element" in the "Recording 1" "table_row"
    # add the second recording
    And I click on "a.action-icon" "css_element" in the "Recording 2" "table_row"
    And I wait until the page is ready
    And I click on "Go back" "button"
    # This should be refactored with the right classes for the table element
    # We use javascript here to create the table so we don't get the same structure.
    Then "Recording 1" "table_row" should exist
    And I click on "a[data-action='delete']" "css_element" in the "Recording 1" "table_row"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    # There is no confirmation dialog when deleting an imported record.
    And I wait until the page is ready
    But I should not see "Recording 1"
    And I click on "Import recording links" "button"
    And I select "Test Course 1" from the "sourcecourseid" singleselect
    And I select "RoomRecordings" from the "sourcebn" singleselect
    And I should see "Recording 1"

  @javascript  @runonly
  Scenario: I check that I can import recordings from a deleted instance into the Recording activity and then if I delete them
  they are back into the pool to be imported again
    Given I log in as "admin"
    When I am on "Test Course 1" course homepage with editing mode on
    And I delete "RoomRecordings" activity
    # The activity is deleted asynchroneously.
    And I run all adhoc tasks
    And I am on the "RoomRecordings1" "bigbluebuttonbn activity" page logged in as "admin"
    And I click on "Import recording links" "button"
    And I select "Recordings from deleted activities" from the "sourcecourseid" singleselect
    Then I should see "Recording 1"
    And I should see "Recording 2"
    # add the first recording
    And I click on "a.action-icon" "css_element" in the "Recording 1" "table_row"
    # add the second recording
    And I click on "a.action-icon" "css_element" in the "Recording 2" "table_row"
    And I wait until the page is ready
    And I click on "Go back" "button"
    # This should be refactored with the right classes for the table element
    # We use javascript here to create the table so we don't get the same structure.
    And "Recording 1" "table_row" should exist
    And I click on "a[data-action='delete']" "css_element" in the "Recording 1" "table_row"
    And I click on "OK" "button" in the "Confirm" "dialogue"
    # There is no confirmation dialog when deleting an imported record.
    And I wait until the page is ready
    But I should not see "Recording 1"
    # Change window size to large to avoid the "Import recording links" button being hidden (random failure).
    And I change window size to "large"
    And I click on "Import recording links" "button"
    And I select "Recordings from deleted activities" from the "sourcecourseid" singleselect
    And I should see "Recording 1"
    But I should not see "Recording 2"

  Scenario: I check that when I disable Import recording feature the import recording link button should not be shown
    Given I log in as "admin"
    And the following config values are set as admin:
      | bigbluebuttonbn_importrecordings_enabled | 0 |
    When I am on the "RoomRecordings1" "bigbluebuttonbn activity" page logged in as "admin"
    Then I should not see "Import recording links"
