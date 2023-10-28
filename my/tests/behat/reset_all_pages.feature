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
    And the following "role capability" exists:
      | role                        | user  |
      | moodle/block:edit           | allow |
      | block/myprofile:addinstance | allow |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | comments  | User         | student1  | my-index        | side-pre      |
      | myprofile | User         | student2  | user-profile    | side-pre      |

    And I log in as "student1"
    And I should see "Comments"
    And I log out

    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should see "Logged in user"
    And I log out

    And I log in as "student3"
    And I should not see "Comments"
    And I follow "Profile" in the user menu
    And I should not see "Logged in user"
    And I log out

  Scenario: Reset Dashboard for all users
    Given I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I turn editing mode on
    And I add the "Latest announcements" block
    And I open the "Timeline" blocks action menu
    And I follow "Delete Timeline block"
    And I press "Yes"
    And I turn editing mode off
    And I log out

    And I log in as "student1"
    And I should not see "Latest announcements"
    And I should see "Timeline"
    And I log out

    And I log in as "student3"
    And I should not see "Latest announcements"
    And I should see "Timeline"
    And I log out

    And I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    When I press "Reset Dashboard for all users"
    And I should see "All Dashboard pages have been reset to default."
    And I log out

    And I log in as "student1"
    Then I should see "Latest announcements"
    And I should not see "Comments"
    And I should not see "Timeline"
    And I log out

    And I log in as "student3"
    And I should see "Latest announcements"
    And I should not see "Timeline"
    And I log out

    # Check that this did not affect the customised profiles.
    And I am on the "student2" "user > profile" page logged in as student2
    And I should see "Logged in user"
    And I should not see "Latest announcements"

  Scenario: Reset profile for all users
    Given I log in as "admin"
    And I navigate to "Appearance > Default profile page" in site administration
    And I turn editing mode on
    And I add the "Latest announcements" block
    And I log out

    And I am on the "student2" "user > profile" page logged in as student2
    And I should not see "Latest announcements"
    And I log out

    And I am on the "student3" "user > profile" page logged in as student3
    And I should not see "Latest announcements"
    And I log out

    And I log in as "admin"
    And I navigate to "Appearance > Default profile page" in site administration
    When I press "Reset profile for all users"
    And I should see "All profile pages have been reset to default."
    And I log out

    And I am on the "student2" "user > profile" page logged in as student2
    Then I should see "Latest announcements"
    And I should not see "Logged in user"
    And I log out

    And I am on the "student3" "user > profile" page logged in as student3
    And I should see "Latest announcements"
    And I log out

    # Check that this did not affect the customised dashboards.
    And I log in as "student1"
    And I should see "Comments"
    And I should not see "Latest announcements"
