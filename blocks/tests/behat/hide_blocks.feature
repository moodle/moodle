@core @core_block
Feature: Block visibility
  In order to configure blocks visibility
  As a teacher
  I need to show and hide blocks on a page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: Hiding all blocks on the page should remove the column they're in
    Given I add the "Search forums" block
    And I open the "Search forums" blocks action menu
    And I click on "Configure Search forums block" "link" in the "Search forums" "block"
    And I set the field "Region" to "Right"
    And I press "Save changes"
    And I turn editing mode off
    And ".empty-region-side-post" "css_element" should not exist in the "body" "css_element"
    And I turn editing mode on
    And I open the "Search forums" blocks action menu
    And I click on "Hide Search forums block" "link" in the "Search forums" "block"
    And I follow "Turn editing off"
    And ".empty-region-side-post" "css_element" should exist in the "body" "css_element"
