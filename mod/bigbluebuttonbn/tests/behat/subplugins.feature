@mod @mod_bigbluebuttonbn @with_bbbext_simple
Feature: BigBlueButtonBN Subplugins test
  As a BigBlueButtonBN user
  I can list the subplugins the admin settings pages
  I can see the additional settings coming from the subplugins in the edit form
  Background:  Make sure that the BigBlueButtonBN plugin is enabled
    Given I accept dpa and enable bigbluebuttonbn plugin
    And the following "courses" exist:
      | fullname    | shortname   | category |
      | Test course | Test course | 0        |
    And the following "activities" exist:
      | activity        | course          | name                | type |
      | bigbluebuttonbn | Test course     | BBB Instance name   | 0    |

  Scenario: Add a subplugin and check that the settings are available
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > BigBlueButton > Manage BigBlueButton extension plugins" in site administration
    Then I should see "Simple"

  Scenario: I check that new fields are available and editable in the instance edit form
    Given I am on the "BBB Instance name" "bigbluebuttonbn activity editing" page logged in as "admin"
    When I expand all fieldsets
    And I should see "New field"

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
