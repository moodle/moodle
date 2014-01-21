@core @core_badges @_only_local @_file_upload
Feature: Add badges to the system
  In order to give badges to users for their achievements
  As an admin
  I need to manage badges in the system

  Background:
    Given I am on homepage
    And I log in as "admin"

  @javascript
  Scenario: Setting badges settings
    Given I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Badges settings"
    And I fill in "Default badge issuer name" with "Test Badge Site"
    And I fill in "Default badge issuer contact details" with "testuser@test-badge-site.com"
    And I press "Save changes"
    When I follow "Add a new badge"
    Then the "issuercontact" field should match "testuser@test-badge-site.com" value
    And the "issuername" field should match "Test Badge Site" value

  @javascript
  Scenario: Accessing the badges
    Given I expand "Site pages" node
    And I follow "Site badges"
    Then I should see "There are no badges available."

  @javascript
  Scenario: Add a badge
    Given I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Test Badge |
      | Description | Test badge description |
      | issuername | Test Badge Site |
      | issuercontact | testuser@test-badge-site.com |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    When I press "Create badge"
    Then I should see "Edit details"
    And I should see "Test Badge"
    And I should not see "Create badge"
    And I follow "Manage badges"
    And I should see "Number of badges available: 1"
    And I should not see "There are no badges available."
