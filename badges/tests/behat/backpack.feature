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
      | imageauthorurl | http://author.example.com        |
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
      | imageauthorurl | http://author.example.com        |
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
  Scenario: Add a new site backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2"
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
  Scenario: Add a new site backpack with authentication details checkbox
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Manage backpacks" in site administration
    When I press "Add a new backpack"
    And I set the field "apiversion" to "2.1"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
    And I should not see "Backpack API URL"
    Then "Include authentication details with the backpack" "checkbox" should not be visible
    And I should not see "Badge issuer email address"
    And I should not see "Badge issuer password"
    And I set the field "apiversion" to "2"
    And "Include authentication details with the backpack" "checkbox" should be visible
    And I click on "includeauthdetails" "checkbox"
    And I should see "Badge issuer email address"
    And I should see "Badge issuer password"
    And I set the field "backpackemail" to "test@test.com"
    And I set the field "password" to "123456"
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I press "Save changes"
    And I click on "Edit" "link" in the "http://backpackweburl.cat" "table_row"
    And the field "Include authentication details with the backpack" matches value "1"
    And I click on "includeauthdetails" "checkbox"
    And I press "Save changes"
    And I click on "Edit" "link" in the "http://backpackweburl.cat" "table_row"
    And the field "Include authentication details with the backpack" matches value "0"
    And I click on "includeauthdetails" "checkbox"
    And I should not see "test@test.com"

  @javascript
  Scenario: View backpack form as a student
    Given I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    When I set the field "externalbackpackid" to "https://dc.imsglobal.org"
    Then I should not see "Log in to your backpack"
    And I should not see "Email address"
    And I should not see "Password"
    But I set the field "externalbackpackid" to "https://test.com/"
    And I should see "Log in to your backpack"
    And I should see "Email address"
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
