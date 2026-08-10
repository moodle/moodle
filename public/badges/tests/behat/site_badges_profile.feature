@core @core_badges
Feature: Site badges link on user profile
  In order to access site-level badges
  As a user
  I need to see a Site badges link on my profile when site badges exist

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | First     | User     | user1@example.com |
      | user2    | Second    | User     | user2@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |

  @javascript
  Scenario: Badges section is hidden on user profile when no site badges are defined
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Badges section is hidden for a non-manager when only inactive site badges exist
    Given the following "core_badges > Badge" exists:
      | name        | Inactive site badge          |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 0                            |
      | type        | 1                            |
    And I log in as "user1"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Badges section is hidden for a non-manager when only archived site badges exist
    Given the following "core_badges > Badge" exists:
      | name        | Archived site badge          |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 4                            |
      | type        | 1                            |
    And I log in as "user1"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Badges section shows Site badges link to a manager when only an inactive site badge exists
    Given the following "core_badges > Badge" exists:
      | name        | Inactive site badge          |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 0                            |
      | type        | 1                            |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should see "Site badges"
    When I click on "Site badges" "link"
    Then I should see "Site administration"

  @javascript
  Scenario: Badges section shows Site badges link when an active site badge exists
    Given the following "core_badges > Badge" exists:
      | name        | Test site badge              |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 1                            |
      | type        | 1                            |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should see "Site badges"
    When I click on "Site badges" "link"
    Then I should see "Test site badge"
    And I should see "Site administration"

  @javascript
  Scenario: Non-manager user can see and use the Site badges link when an active site badge exists
    Given the following "core_badges > Badge" exists:
      | name        | Test site badge              |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 1                            |
      | type        | 1                            |
    And I log in as "user1"
    And I follow "Profile" in the user menu
    Then I should see "Site badges"
    When I click on "Site badges" "link"
    Then I should see "Test site badge"
    And I should not see "Add a new badge"
    And I should not see "Site administration"

  @javascript
  Scenario: Site badges link is hidden when the user lacks the viewbadges capability
    Given the following "permission overrides" exist:
      | capability                | permission | role | contextlevel | reference |
      | moodle/badges:viewbadges  | Prevent    | user | System       |           |
    And the following "core_badges > Badge" exists:
      | name        | Test site badge              |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 1                            |
      | type        | 1                            |
    And I log in as "user1"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Badges section is hidden on user profile when badges are disabled
    Given the following "core_badges > Badge" exists:
      | name        | Test site badge              |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 1                            |
      | type        | 1                            |
    And the following config values are set as admin:
      | enablebadges | 0 |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Site badges link visibility depends on the viewer, not the profile owner
    Given the following "core_badges > Badge" exists:
      | name        | Test site badge              |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 1                            |
      | type        | 1                            |
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I should see "Site badges"
    When I am on the "user2" "user > profile" page
    Then I should see "Site badges"
