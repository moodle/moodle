@core @core_user
Feature: Set the site home page and dashboard as the default home page
  In order to set a page as my default home page
  As a user
  I need to choose which page I want and set it as my home page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | user1    | C1     | student        |

  Scenario: Admin sets the site page and then the dashboard as the default home page
    # This functionality does not work without the administration block.
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I add the "Administration" block if not present
    And I configure the "Administration" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I navigate to "Appearance > Navigation" in site administration
    And I set the field "Start page for users" to "User preference"
    And I press "Save changes"
    And I am on site homepage
    And I follow "Make this my home page"
    And I should not see "Make this my home page"
    And I am on "Course 1" course homepage
    And I should see "Home" in the "Navigation" "block"
    And I should not see "Site home" in the "Navigation" "block"
    And I am on site homepage
    And I follow "Dashboard"
    And I follow "Make this my home page"
    And I should not see "Make this my home page"
    And I am on "Course 1" course homepage
    Then I should not see "Home" in the "Navigation" "block"
    And I should see "Site home" in the "Navigation" "block"

  Scenario: User cannot configure their preferred default home page unless allowed by admin
    Given I log in as "user1"
    When I follow "Preferences" in the user menu
    Then I should not see "Home page"

  Scenario Outline: User can configure their preferred default home page when allowed by admin
    Given I log in as "admin"
    And I navigate to "Appearance > Navigation" in site administration
    And I set the field "Start page for users" to "User preference"
    And I press "Save changes"
    And I log out
    When I log in as "user1"
    And I follow "Preferences" in the user menu
    And I follow "Start page"
    And I set the field "Start page" to "<preference>"
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    Then I should see "<breadcrumb>" is active in navigation

    Examples:
      | preference | breadcrumb |
      | Home       | Home       |
      | Dashboard  | Dashboard  |
      | My courses | My courses |
