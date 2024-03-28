@mod @mod_bigbluebuttonbn @with_bbbext_simple
Feature: BigBlueButtonBN Subplugins test
  As a BigBlueButtonBN user
  I can list the subplugins the admin settings pages
  I can see the additional settings coming from the subplugins in the edit form

  Background:  Make sure that the BigBlueButtonBN plugin is enabled
    Given I enable "bigbluebuttonbn" "mod" plugin
    And the following "courses" exist:
      | fullname    | shortname   | category | enablecompletion |
      | Test course | Test course | 0        | 1                |
    And the following "activities" exist:
      | activity        | course      | name              | type |
      | bigbluebuttonbn | Test course | BBB Instance name | 0    |
    And the following config values are set as admin:
      | enableasyncbackup | 0 |

  Scenario: Add a subplugin and check that the settings are available
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > BigBlueButton > Manage BigBlueButton extension plugins" in site administration
    Then I should see "Simple"

  Scenario: I check that new fields are available and editable in the instance edit form
    Given I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page logged in as "admin"
    When I expand all fieldsets
    Then I should see "New field"

  @javascript
  Scenario: I check that new fields are available and when I edit the field the value is saved
    Given I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I expand all fieldsets
    And I press "Save and display"
    And I expand all fieldsets
    And I should see "New field cannot be empty"
    And I set the field "New field" to "50"
    And I press "Save and display"
    And I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page
    When I expand all fieldsets
    Then the following fields match these values:
      | New field | 50 |

  Scenario: I check that new fields are not available when subplugin is disabled
    Given I log in as "admin"
    And I navigate to "Plugins > Activity modules > BigBlueButton > Manage BigBlueButton extension plugins" in site administration
    And I click on "Disable" "link"
    And I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page
    When I expand all fieldsets
    Then I should not see "New field"

  @javascript
  Scenario: I check that custom completion with subplugin works
    Given a BigBlueButton mock server is configured
    And the following config values are set as admin:
      | bigbluebuttonbn_meetingevents_enabled | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                 |
      | traverst | Terry     | Travers  | t.travers@example.com |
    And the following "course enrolments" exist:
      | user     | course      | role    |
      | traverst | Test course | student |
    And I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page logged in as "admin"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Add requirements | 1 |
      | Raise hand twice | 1 |
    And I set the field "New field" to "50"
    And I press "Save and display"
    # We start the meeting here so to make sure that meta_analytics-callback-url is set.
    And the following "mod_bigbluebuttonbn > meeting" exists:
      | activity | BBB Instance name |
    And I log out
    And I am on the "BBB Instance name" "bigbluebuttonbn activity" page logged in as "traverst"
    And I click on "Join session" "link"
    And I switch to "bigbluebutton_conference" window
    And I wait until the page is ready
    And I follow "End Meeting"
    And the BigBlueButtonBN server has received the following events from user "traverst":
      | instancename      | eventtype | eventdata |
      | BBB Instance name | raisehand | 1         |
      | BBB Instance name | raisehand | 1         |
    # Selenium driver does not like the click action to be done before we
    # automatically close the window so we need to make sure that the window
    # is closed before.
    And I close all opened windows
    And I switch to the main window
    And the BigBlueButtonBN activity "BBB Instance name" has sent recording all its events
    And I run all adhoc tasks
    When I reload the page
    Then I should see "Done: Raise hand twice in a meeting."
