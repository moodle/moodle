@core @core_my
Feature: Reset all personalised pages to default
  In order to reset everyone's personalised pages
  As an admin
  I need to press a button on the pages to customise the default pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And I log in as "admin"
    And I set the following system permissions of "Authenticated user" role:
      | block/myprofile:addinstance | Allow |
      | moodle/block:edit | Allow |
    And I log out

    And I log in as "student1"
    And I follow "Dashboard" in the user menu
    And I press "Customise this page"
    And I add the "Comments" block
    And I press "Stop customising this page"
    And I should see "Comments"
    And I log out

    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should not see "Logged in user"
    And I press "Customise this page"
    And I add the "Logged in user" block
    And I press "Stop customising this page"
    And I should see "Logged in user"
    And I log out

    And I log in as "student3"
    And I follow "Dashboard" in the user menu
    And I should not see "Comments"
    And I follow "Profile" in the user menu
    And I should not see "Logged in user"
    And I log out

  Scenario: Reset Dashboard for all users
    Given I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I press "Blocks editing on"
    And I add the "Latest announcements" block
    And I open the "Online users" blocks action menu
    And I follow "Delete Online users"
    And I press "Yes"
    And I press "Blocks editing off"
    And I log out

    And I log in as "student1"
    And I follow "Dashboard" in the user menu
    And I should not see "Latest announcements"
    And I should see "Online users"
    And I log out

    And I log in as "student3"
    And I follow "Dashboard" in the user menu
    And I should not see "Latest announcements"
    And I should see "Online users"
    And I log out

    And I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    When I press "Reset Dashboard for all users"
    And I should see "All Dashboard pages have been reset to default."
    And I log out

    And I log in as "student1"
    And I follow "Dashboard" in the user menu
    Then I should see "Latest announcements"
    And I should not see "Comments"
    And I should not see "Online users"
    And I log out

    And I log in as "student3"
    And I follow "Dashboard" in the user menu
    And I should see "Latest announcements"
    And I should not see "Online users"
    And I log out

    # Check that this did not affect the customised profiles.
    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should see "Logged in user"
    And I should not see "Latest announcements"

  Scenario: Reset profile for all users
    Given I log in as "admin"
    And I navigate to "Appearance > Default profile page" in site administration
    And I press "Blocks editing on"
    And I add the "Latest announcements" block
    And I log out

    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should not see "Latest announcements"
    And I log out

    And I log in as "student3"
    And I follow "Profile" in the user menu
    And I should not see "Latest announcements"
    And I log out

    And I log in as "admin"
    And I navigate to "Appearance > Default profile page" in site administration
    When I press "Reset profile for all users"
    And I should see "All profile pages have been reset to default."
    And I log out

    And I log in as "student2"
    And I follow "Profile" in the user menu
    Then I should see "Latest announcements"
    And I should not see "Logged in user"
    And I log out

    And I log in as "student3"
    And I follow "Profile" in the user menu
    And I should see "Latest announcements"
    And I log out

    # Check that this did not affect the customised dashboards.
    And I log in as "student1"
    And I follow "Dashboard" in the user menu
    And I should see "Comments"
    And I should not see "Latest announcements"
