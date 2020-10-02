@core @core_badges @_file_upload
Feature: Backpack badges
  The settings to connect to backpack with OAuth2 service
  As an learner
  I need to verify display backpack in the my profile

  Background:
    Given the following "badge external backpack" exist:
      | backpackapiurl                               | backpackweburl           | apiversion |
      | https://dc.imsglobal.org/obchost/ims/ob/v2p1 | https://dc.imsglobal.org | 2.1          |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And I log in as "admin"
    And I navigate to "Badges > Badges settings" in site administration
    And I set the field "Badge issuer name" to "Test Badge Site"
    And I set the field "Badge issuer email address" to "testuser@example.com"
    And I log out

  @javascript
  Scenario: Verify backback settings
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Backpack settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
      | Active external backpack     | https://dc.imsglobal.org |
    And I press "Save changes"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name          | Test badge verify backpack |
      | Version       | v1                         |
      | Language      | English                    |
      | Description   | Test badge description     |
      | Image author  | http://author.example.com  |
      | Image caption | Test caption image         |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    And I log out
    When I am on homepage
    And I log in as "student1"
    And I follow "Preferences" in the user menu
    And I follow "Backpack settings"
    Then I should see "https://dc.imsglobal.org"
    And I should see "Not connected"

  @javascript
  Scenario: User has been connected backpack
    Given I am on homepage
    And I log in as "admin"
    And I navigate to "Badges > Backpack settings" in site administration
    And I set the following fields to these values:
      | External backpack connection | 1                        |
      | Active external backpack     | https://dc.imsglobal.org |
    And I press "Save changes"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name           | Test badge verify backpack |
      | Version        | v1                         |
      | Language       | English                    |
      | Description    | Test badge description     |
      | Image author   | http://author.example.com  |
      | Image caption  | Test caption image         |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I set the field "Manager" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    And I log out
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
    And I set the field "backpackapiurl" to "http://backpackapiurl.cat"
    And I set the field "backpackweburl" to "aaa"
    And I press "Save changes"
    And I should see "Invalid URL"
    And I set the field "backpackweburl" to "http://backpackweburl.cat"
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
    And "Delete" "button" should not exist
