@block @block_main_menu
Feature: Add URL to main menu block
  In order to add helpful resources for students
  As a admin
  I need to add URLs to the main menu block and check it works.

  @javascript
  Scenario: Add a URL in menu block and ensure it appears
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    When I add a "URL" to section "0" and I fill the form with:
      | Name | google |
      | Description | gooooooooogle |
      | External URL | http://www.google.com |
      | id_display | In pop-up |
    Then "google" "link" should exist in the "Main menu" "block"
    And "Add an activity or resource" "link" should exist in the "Main menu" "block"
