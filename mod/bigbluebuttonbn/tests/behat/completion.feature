@mod @mod_bigbluebuttonbn
Feature: As a user I can complete a BigblueButtonBN activity by usual or custom criteria

  Background:
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname    | shortname | category | enablecompletion |
      | Test course | C1        | 0        | 1                |
    And the following "activities" exist:
      | activity        | name           | intro                           | course | idnumber         | type | recordings_imported |
      | bigbluebuttonbn | RoomRecordings | Test Room Recording description | C1     | bigbluebuttonbn1 | 0    | 0                   |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | traverst | C1     | student |

  Scenario: I set the completion to standard type of completion.
    Given I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as admin
    When I click on "Settings" "link"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Add requirements         | 1                  |
      | View the activity   | 1                                                 |
    And I press "Save and display"
    And I log out
    And I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as traverst
    Then I should see "Done: View"

  @javascript
  Scenario: I set the completion type to custom completion
    Given a BigBlueButton mock server is configured
    And the following config values are set as admin:
      | bigbluebuttonbn_meetingevents_enabled | 1 |
    And I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as admin
    And I click on "Settings" "link"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Add requirements | 1 |
      | Chats            | 1 |
    And I press "Save and display"
    # We start the meeting here so to make sure that meta_analytics-callback-url is set.
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | RoomRecordings |
    And I log out
    And I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as traverst
    When I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    And I wait until the page is ready
    And I follow "End Meeting"
    And the BigBlueButtonBN server has received the following events from user "traverst":
      | instancename   | eventtype | eventdata |
      | RoomRecordings | chats     | 1         |
    # Selenium driver does not like the click action to be done before we
    # automatically close the window so we need to make sure that the window
    # is closed before.
    And I close all opened windows
    And I switch to the main window
    And the BigBlueButtonBN activity "RoomRecordings" has sent recording all its events
    And I run all adhoc tasks
    And I reload the page
    Then I should see "Done: Participate in 1 chat(s)"

  @javascript
  Scenario: Validate completion when registering live sessions
    Given the following config values are set as admin:
      | bigbluebuttonbn_meetingevents_enabled | 1 |
    When I am on the "RoomRecordings" "bigbluebuttonbn activity" page logged in as admin
    And I click on "Validate completion" "link"
    Then I should see "Validate completion has been triggered."
