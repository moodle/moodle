@core @core_admin @theme_classic
Feature: Select a theme in Classic theme
  In order to choose a theme
  As an admin
  I need to preview the theme and make a selection

  Background:
    Given I log in as "admin"
    And I navigate to "Appearance > Themes" in site administration

  @javascript
  Scenario: I am able to preview a theme using a modal window
    When I click on "Preview theme 'Classic'" "button"
    Then I should see "Classic" in the "Classic" "dialogue"
    And I should see "Classic is a highly-customisable theme," in the "Classic" "dialogue"
    And I should see "Current theme" in the "Classic" "dialogue"

  @javascript
  Scenario: I am able to change the theme using the modal window select button
    Given I should see "Current theme" in the "#theme-card-classic" "css_element"
    When I click on "Preview theme 'Boost'" "button"
    And I click on "Select theme" "button" in the "Boost" "dialogue"
    Then I should see "Current theme" in the "#theme-card-boost" "css_element"

  Scenario: I am able to change the theme using the normal select button
    Given I should see "Current theme" in the "#theme-card-classic" "css_element"
    When I click on "Select theme 'Boost'" "button"
    Then I should see "Current theme" in the "#theme-card-boost" "css_element"

  Scenario: I am able to view a theme's settings page
    Given I click on "Edit theme settings 'Classic'" "link"
    Then I should see "Classic"
