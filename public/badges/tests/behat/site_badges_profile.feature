@core @core_badges
Feature: Site badges link on user profile
  In order to access site-level badges
  As a user
  I need to see a Site badges link on my profile when site badges exist

  @javascript
  Scenario: Badges section is hidden on user profile when no site badges are defined
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"

  @javascript
  Scenario: Badges section shows Site badges link when a site badge exists
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

  @javascript
  Scenario: Non-admin user can see and use the Site badges link when a site badge exists
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | First     | User     | user1@example.com |
    And the following "core_badges > Badge" exists:
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
  Scenario: Badges section is hidden on user profile when only archived site badges exist
    Given the following "core_badges > Badge" exists:
      | name        | Archived site badge          |
      | description | Test badge description       |
      | image       | badges/tests/behat/badge.png |
      | status      | 4                            |
      | type        | 1                            |
    And I log in as "admin"
    And I follow "Profile" in the user menu
    Then I should not see "Site badges"
