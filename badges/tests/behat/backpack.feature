@core @core_badges
Feature: Backpack badges
  Test the settings to add/update a backpack for a site and user.
  I need to verify display backpack in the my profile

  Background:
    Given the following "badge external backpacks" exist:
      | backpackapiurl                               | backpackweburl           | apiversion | sortorder |
      | https://dc.imsglobal.org/obchost/ims/ob/v2p1 | https://dc.imsglobal.org | 2.1        | 2         |
      | https://test.com/                            | https://test.com/        | 2          | 3         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the field "Badge issuer name" to "Test Badge Site"
    And I set the field "Badge issuer email address" to "testuser@example.com"
    And I log out

  @javascript
  Scenario: If external backpack connection is disabled, backpack settings should not be displayed
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 0                        |
    And I press "Save changes"
    When I navigate to "Badges" in site administration
    Then I should not see "Manage backpacks"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
    And I press "Save changes"
    And I am on homepage
    And I navigate to "Badges" in site administration
    And I should see "Manage backpacks"

  @javascript
  Scenario: Verify backback settings
    Given the following "core_badges > Badge" exists:
      | name           | Test badge verify backpack       |
      | version        | 1                                |
      | language       | en                               |
      | description    | Test badge description           |
      | image          | badges/tests/behat/badge.png     |
      | imagecaption   | Test caption image               |
    And the following "core_badges > Criteria" exists:
      | badge          | Test badge verify backpack       |
      | role           | editingteacher                   |
    And the following "core_badges > Issued badge" exists:
      | badge          | Test badge verify backpack       |
      | user           | student1                         |
    When I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    Then I should see "Choose..." in the "Backpack provider" "select"

  @javascript
  Scenario: User has been connected backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
    And I press "Save changes"
    And I navigate to "Badges > Manage backpacks" in site administration
    And I click on "Move up" "link" in the "https://dc.imsglobal.org" "table_row"
    And the following "core_badges > Badge" exists:
      | name           | Test badge verify backpack       |
      | version        | 1                                |
      | language       | en                               |
      | description    | Test badge description           |
      | image          | badges/tests/behat/badge.png     |
      | imagecaption   | Test caption image               |
    And the following "core_badges > Criteria" exists:
      | badge          | Test badge verify backpack       |
      | role           | editingteacher                   |
    And the following "core_badges > Issued badge" exists:
      | badge          | Test badge verify backpack       |
      | user           | student1                         |
    And the following "setup backpack connected" exist:
      | user     | externalbackpack         |
      | student1 | https://dc.imsglobal.org |
    When I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    Then I should see "Connected"
    And I follow "Preferences" in the user menu
    And I follow "Manage badges"
    And I should see "Test badge verify backpack"
    And "Add to backpack" "link" should exist

  @javascript
  Scenario: Add a new site OBv2.1 backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2.1"
    And I should see "Backpack URL"
    And I set the field "backpackweburlv2p1" to "http://backpackweburl.cat"
    And I should not see "Backpack API URL"
    Then "Connect to backpack provider account" "checkbox" should not be visible
    And "Connect to a Canvas Credentials issuer account" "checkbox" should not be visible
    And I should not see "Email"
    And I should not see "Password"

  @javascript
  Scenario: Add a new site OBv2.0 backpack with Canvas provider
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "provider" to "Canvas Credentials"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "region" to "Singapore"
    And I should not see "Backpack web URL"
    And I should not see "Backpack API URL"
    And I press "Save changes"
    Then I should see "https://sg.badgr.io"
    And "Delete" "icon" should exist in the "https://sg.badgr.io" "table_row"
    And "Edit settings" "icon" should exist in the "https://sg.badgr.io" "table_row"
    And "Test settings" "icon" should exist in the "https://sg.badgr.io" "table_row"
    # Check that editing the backpack shows the correct values.
    And I click on "Edit settings" "link" in the "https://sg.badgr.io" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should see "Provider"
    And the field "provider" matches value "Canvas Credentials"
    And I should see "Region"
    And the field "region" matches value "Singapore"
    And I should see "Connect to a Canvas Credentials issuer account"
    And the field "Connect to a Canvas Credentials issuer account" matches value "0"
    And I should not see "Connect to backpack provider account"

  @javascript
  Scenario: Add a new site OBv2.0 backpack with Canvas provider and issuer authentication details
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
    And I set the field "provider" to "Canvas Credentials"
    And I set the field "region" to "Canada"
    And I should see "Connect to a Canvas Credentials issuer account"
    And I should not see "Connect to backpack provider account"
    And the field "Connect to a Canvas Credentials issuer account" matches value "0"
    And I click on "includeauthdetailscanvas" "checkbox"
    And I should see "Email"
    And I should see "Password"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "backpackemailcanvas" to "test@test.com"
    And I should see "You must supply a value here"
    And I press "Save changes"
    And I set the field "backpackpasswordcanvas" to "123456"
    And I press "Save changes"
    Then I should see "https://ca.badgr.io"
    # Check that editing the backpack shows the correct values.
    And I click on "Edit settings" "link" in the "https://ca.badgr.io" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should see "Provider"
    And the field "provider" matches value "Canvas Credentials"
    And I should see "Region"
    And the field "region" matches value "Canada"
    And I should see "Connect to a Canvas Credentials issuer account"
    And the field "Connect to a Canvas Credentials issuer account" matches value "1"
    And the field "backpackemailcanvas" matches value "test@test.com"
    And the field "backpackpasswordcanvas" matches value "123456"
    # Disable authentication details and check that email and password are cleared.
    But I click on "includeauthdetailscanvas" "checkbox"
    And I press "Save changes"
    And I click on "Edit settings" "link" in the "https://ca.badgr.io" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should see "Provider"
    And the field "provider" matches value "Canvas Credentials"
    And I should see "Region"
    And the field "region" matches value "Canada"
    And I should see "Connect to a Canvas Credentials issuer account"
    And the field "Connect to a Canvas Credentials issuer account" matches value "0"
    And the field "backpackemailcanvas" matches value ""
    And the field "backpackpasswordcanvas" matches value ""

  @javascript
  Scenario: Add a new site OBv2.0 backpack with Other provider
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
    And I set the field "provider" to "Other"
    And I should not see "Region"
    And I set the field "backpackweburl" to "aaa"
    And I press "Save changes"
    And I should see "Invalid URL"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I press "Save changes"
    Then I should see "http://backpackweburl.cat"
    And "Delete" "icon" should exist in the "http://backpackweburl.cat" "table_row"
    And "Edit settings" "icon" should exist in the "http://backpackweburl.cat" "table_row"
    And "Test settings" "icon" should exist in the "http://backpackweburl.cat" "table_row"
    # Check that editing the backpack shows the correct values.
    And I click on "Edit settings" "link" in the "http://backpackweburl.cat" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should see "Provider"
    And the field "provider" matches value "Other"
    And I should not see "Region"
    And the field "backpackweburl" matches value "http://backpackweburl.cat"
    And the field "backpackapiurl" matches value "http://backpackapiurl.cat"
    And the field "Connect to backpack provider account" matches value "0"
    And I should not see "Connect to a Canvas Credentials issuer account"

  @javascript
  Scenario: Add a new site OBv2.0 backpack with Other provider and issuer authentication details
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
    And I set the field "provider" to "Other"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I should see "Connect to backpack provider account"
    And I should not see "Connect to a Canvas Credentials issuer account"
    And the field "Connect to backpack provider account" matches value "0"
    And I click on "includeauthdetails" "checkbox"
    And I should see "Email"
    And I should see "Password"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "backpackemail" to "test@test.com"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "password" to "123456"
    And I press "Save changes"
    Then I should see "http://backpackweburl.cat"
    # Check that editing the backpack shows the correct values.
    And I click on "Edit settings" "link" in the "http://backpackweburl.cat" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And the field "provider" matches value "Other"
    And the field "backpackweburl" matches value "http://backpackweburl.cat"
    And the field "backpackapiurl" matches value "http://backpackapiurl.cat"
    And the field "Connect to backpack provider account" matches value "1"
    And the field "backpackemail" matches value "test@test.com"
    And the field "password" matches value "123456"
    # Disable authentication details and check that email and password are cleared.
    But I click on "includeauthdetails" "checkbox"
    And I press "Save changes"
    And I click on "Edit settings" "link" in the "http://backpackweburl.cat" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And the field "provider" matches value "Other"
    And the field "backpackweburl" matches value "http://backpackweburl.cat"
    And the field "backpackapiurl" matches value "http://backpackapiurl.cat"
    And the field "Connect to backpack provider account" matches value "0"
    And the field "backpackemail" matches value ""
    And the field "password" matches value ""

  @javascript
  Scenario: Add a new site OBv2.0 backpack without providers
    Given the following config values are set as admin:
      | badges_canvasregions |  |
    And I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
    And I should not see "Provider"
    And I should see "Backpack URL"
    And I should see "Backpack API URL"
    And I press "Save changes"
    And I should see "You must supply a value here"
    And I set the field "backpackweburl" to "https://eu.badgr.io"
    And I set the field "backpackapiurl" to "https://api.eu.badgr.io/v2"
    And I should see "Connect to backpack provider account"
    And I should not see "Connect to a Canvas Credentials issuer account"
    And I press "Save changes"
    Then I should see "https://eu.badgr.io"
    And "Delete" "icon" should exist in the "https://eu.badgr.io" "table_row"
    And "Edit settings" "icon" should exist in the "https://eu.badgr.io" "table_row"
    And "Test settings" "icon" should exist in the "https://eu.badgr.io" "table_row"
    # Check that editing the backpack shows the correct values.
    And I click on "Edit settings" "link" in the "https://eu.badgr.io" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should not see "Provider"
    And the field "backpackweburl" matches value "https://eu.badgr.io"
    And the field "backpackapiurl" matches value "https://api.eu.badgr.io/v2"
    And I should see "Connect to backpack provider account"
    And the field "Connect to backpack provider account" matches value "0"
    And I should not see "Connect to a Canvas Credentials issuer account"
    And I press "Cancel"
    # Add Europe to the providers list and check that editing the backpack shows the correct values.
    But the following config values are set as admin:
      | badges_canvasregions | Europe\|https://eu.badgr.io\|https://api.eu.badgr.io/v2 |
    And I click on "Edit settings" "link" in the "https://eu.badgr.io" "table_row"
    And I should see "API version supported"
    And the field "apiversion" matches value "2"
    And I should see "Provider"
    And the field "provider" matches value "Canvas Credentials"
    And I should see "Region"
    And the field "region" matches value "Europe"
    And I should see "Connect to a Canvas Credentials issuer account"
    And the field "Connect to a Canvas Credentials issuer account" matches value "0"
    And I should not see "Connect to backpack provider account"
    And I should not see "Backpack URL"
    And I should not see "Backpack API URL"

  @javascript
  Scenario: Remove a site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I click on "Delete" "link" in the "https://dc.imsglobal.org" "table_row"
    And I should see "Delete site backpack 'https://dc.imsglobal.org'?"
    And I click on "Delete" "button" in the "Delete site backpack" "dialogue"
    Then I should see "The site backpack has been deleted."
    And I should not see "https://dc.imsglobal.org"
    And "Delete" "button" should not be visible

  @javascript
  Scenario: Move up and down site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    And "Move up" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    When I click on "Move up" "link" in the "https://dc.imsglobal.org" "table_row"
    Then "Move up" "icon" should not exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And I click on "Move down" "link" in the "https://dc.imsglobal.org" "table_row"
    And I click on "Move down" "link" in the "https://dc.imsglobal.org" "table_row"
    And "Move up" "icon" should exist in the "https://dc.imsglobal.org" "table_row"
    And "Move down" "icon" should not exist in the "https://dc.imsglobal.org" "table_row"

  @javascript
  Scenario: View backpack form as a student
    Given I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    When I set the field "externalbackpackid" to "https://dc.imsglobal.org"
    Then I should not see "Log in to your backpack"
    And I should not see "Email"
    And I should not see "Password"
    But I set the field "externalbackpackid" to "https://test.com/"
    And I should see "Log in to your backpack"
    And I should see "Email"
    And I should see "Password"

  @javascript
  Scenario: Check backpack form validation as a student
    Given I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    When I click on "Connect to backpack" "button"
    Then I should see "Backpack provider can't be blank"
    And I set the field "externalbackpackid" to "https://test.com/"
    And I set the field "password" to ""
    When I click on "Connect to backpack" "button"
    Then I should see "Password can't be blank"
    And I should not see "Email address can't be blank"
    And I set the field "backpackemail" to ""
    And I click on "Connect to backpack" "button"
    And I should see "Email address can't be blank"
    And I should see "Password can't be blank"
