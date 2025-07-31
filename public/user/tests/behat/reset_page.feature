@core @core_user
Feature: Reset my profile page to default
  In order to remove customisations from my profile page
  As a user
  I need to reset my profile page

  @javascript
  Scenario: Add blocks to page and reset
    When I log in as "admin"
    And I follow "Profile" in the user menu
    And I turn editing mode on
    And I add the "Latest announcements" block
    And I press "Reset page to default"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    Then "Latest announcements" "block" should not exist
