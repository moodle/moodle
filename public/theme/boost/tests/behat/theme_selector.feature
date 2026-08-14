@core @core_admin @theme_boost
Feature: Select a theme in Boost theme
  In order to choose a theme
  As an admin
  I need to preview the theme and make a selection

  Background:
    Given I log in as "admin"
    And I navigate to "Appearance > Themes" in site administration

  @javascript
  Scenario: I am able to preview a theme using a modal window
    When I click on "Preview theme 'Boost'" "button"
    Then I should see "Boost" in the "Boost" "dialogue"
    And I should see "Boost is a modern highly-customisable theme." in the "Boost" "dialogue"
    And I should see "Current theme" in the "Boost" "dialogue"

  Scenario: I am able to view a theme's settings page
    Given I click on "Edit theme settings 'Boost'" "link"
    Then I should see "Boost"
