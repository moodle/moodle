@theme @theme_snap
Feature: A user can see a link to their settings in their profile page
  In order to change my preferences
  As a user
  I need to click on the link in the profile page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |

  @javascript
  Scenario: a user sees the link in their profile page
    Given I log in as "student1"
    And I open the user menu
    And I follow "Profile"
    Then I should see "User details"
    And I should see "Preferences"
