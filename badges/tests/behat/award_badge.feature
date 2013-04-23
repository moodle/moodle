@core @core_badges @_only_local
Feature: Award badges
  In order to award badges to users for their achievements
  As an admin
  I need to add criteria to badges in the system

  Background:
    Given I am on homepage
    And I log in as "admin"

  @javascript
  Scenario: Add criteria
    Given I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Test Badge |
      | Description | Test badge description |
      | issuername | Test Badge Site |
      | issuercontact | testuser@test-badge-site.com |
    And I upload "badges/tests/behat/badge.png" file to "Image" filepicker
    And I press "Create badge"
    And I select "Profile completion" from "type"
    And I wait "5" seconds
    And I check "First name"
    And I check "Email address"
    When I press "Save"
    Then I should see "Profile completion"
    And I should see "First name"
    And I should see "Email address"
    And I should not see "Criteria for this badge have not been set up yet."

  @javascript
  Scenario: Earn badge
    Given I expand "Site administration" node
    And I expand "Badges" node
    And I follow "Add a new badge"
    And I fill the moodle form with:
      | Name | Profile Badge |
      | Description | Test badge description |
      | issuername | Test Badge Site |
      | issuercontact | testuser@test-badge-site.com |
    And I upload "badges/tests/behat/badge.png" file to "Image" filepicker
    And I press "Create badge"
    And I select "Profile completion" from "type"
    And I wait "5" seconds
    And I check "Phone"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I expand "My profile settings" node
    And I follow "Edit profile"
    And I expand all fieldsets
    And I fill in "Phone" with "123456789"
    And I press "Update profile"
    When I follow "My badges"
    Then I should see "Profile Badge"
    And I should not see "There are no badges available."
