@core @core_admin
Feature: Purge caches
  In order to see changes to cached data
  As a Moodle administrator
  I want manually purge different data and file caches

  Background:
    Given I log in as "admin"
    And I navigate to "Development > Purge all caches" in site administration

  Scenario: Purge all caches
    Given I should not see "All caches were purged"
    When I press "Purge all caches"
    Then I should see "All caches were purged"

  Scenario: Purge selected caches
    Given I should not see "Selected caches were purged"
    When I set the field "Themes" to "1"
    And I press "Purge selected caches"
    Then I should see "The selected caches were purged"

  Scenario: Purge selected caches without selecting any caches
    Given I should not see "Select one or more caches to purge"
    When I press "Purge selected caches"
    Then I should not see "The selected caches were purged"
    And I should see "Select one or more caches to purge"

  Scenario: Redirect back to the original page after following a Purge all caches link
    Given I am on site homepage
    And I should see "Available courses"
    And I should not see "All caches were purged"
    When I follow "Purge all caches"
    Then I should see "All caches were purged"
    And I should see "Available courses"
