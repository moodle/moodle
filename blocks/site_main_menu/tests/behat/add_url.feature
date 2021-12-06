@block @block_site_main_menu
Feature: Add URL to main menu block
  In order to add helpful resources for students
  As a admin
  I need to add URLs to the main menu block and check it works.

  @javascript
  Scenario: Add a URL in menu block and ensure it appears
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Main menu" block
    When I add a "URL" to section "0" and I fill the form with:
      | Name         | reference link        |
      | Description  | gooooooooogle         |
      | External URL | http://www.google.com |
      | id_display   | In pop-up             |
    Then "reference link" "link" should exist in the "Main menu" "block"
    And "Add an activity or resource" "button" should exist in the "Main menu" "block"

  @javascript
  Scenario: Add a URL in menu block can appear in the entire site
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Main menu" block
    And I configure the "Main menu" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I add a "URL" to section "0" and I fill the form with:
      | Name         | reference link        |
      | Description  | gooooooooogle         |
      | External URL | http://www.google.com |
      | id_display   | Embed                 |
    Then I click on "reference link" "link" in the "Main menu" "block"
    And "reference link" "link" should exist in the "Main menu" "block"
    And I am on the "C1" "Course" page
    And "reference link" "link" should exist in the "Main menu" "block"
    And I navigate to "Badges > Add a new badge" in site administration
    And "reference link" "link" should exist in the "Main menu" "block"

  @javascript
  Scenario: Add a URL in menu block can appear in any front page
    Given the following "course" exists:
      | fullname         | Course 1 |
      | shortname        | C1       |
      | category         | 0        |
      | enablecompletion | 1        |
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Main menu" block
    And I configure the "Main menu" block
    And I set the following fields to these values:
      | Page contexts | Display on the site home and any pages added to the site home. |
    And I press "Save changes"
    And I add a "URL" to section "0" and I fill the form with:
      | Name         | reference link        |
      | Description  | gooooooooogle         |
      | External URL | http://www.google.com |
      | id_display   | Embed                 |
    Then I click on "reference link" "link" in the "Main menu" "block"
    And "reference link" "link" should exist in the "Main menu" "block"
    And I am on the "C1" "Course" page
    And "Main menu" "block" should not exist
    And I navigate to "Badges > Add a new badge" in site administration
    And "Main menu" "block" should not exist
