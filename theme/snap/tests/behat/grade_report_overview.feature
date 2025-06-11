@theme @theme_snap
Feature: Testing grade_report_overview in theme_snap

  Background:
    Given I am logged in as "admin"

  @javascript
  Scenario: The messages button should redirect to the message page without showing the drawer
    Given I click on ".usermenu .dropdown-toggle" "css_element"
    Then I click on "Grades" "link"
    And I wait to be redirected
    And I click on "Message" "button"
    Then ".message-app" "css_element" should be visible
