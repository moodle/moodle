@core @core_user @javascript
Feature: The admin can check student permission in moodle system.
  In order to check permission of a user in moodle
  As an admin user
  I can search student and see their permission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |

  Scenario: The search setting is saved for each user.
    Given I log in as "admin"
    And I navigate to "Users > Permissions > Check system permissions" in site administration
    And I follow "Search options"
    And the field "from start" matches value "1"
    And I click on "anywhere" "radio"
    And I click on "Keep selected users, even if they no longer match the search" "checkbox"
    And I click on "If only one user matches the search, select them automatically" "checkbox"
    And I reload the page
    Then the field "from start" matches value "0"
    And the field "anywhere" matches value "1"
    And the field "Keep selected users, even if they no longer match the search" matches value "1"
    And the field "If only one user matches the search, select them automatically" matches value "1"
