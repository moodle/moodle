@core @core_block
Feature: Block removal via modal
  In order to remove blocks
  As a teacher
  I need to use a modal to confirm the block to delete

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | showinsubcontexts | defaultregion |
      | search_forums | Course       | C1        | course-view-*   | 0                 | side-pre      |
      | myprofile     | System       |           | *               | 1                 | side-pre      |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Removing a block via modal should remove the block on the page
    Given I open the "Search forums" blocks action menu
    When I click on "Delete Search forums block" "link" in the "Search forums" "block"
    And I should see "This will delete the block Search forums." in the "Delete block?" "dialogue"
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    And I wait to be redirected
    Then "Search forums" "block" should not exist
    And I open the "Logged in user" blocks action menu
    And I click on "Delete Logged in user block" "link" in the "Logged in user" "block"
    And I should see "You are about to delete a block that appears elsewhere" in the "Delete block?" "dialogue"
    And I should see "Original block location: System" in the "Delete block?" "dialogue"
    And I should see "Display on page types: Any page" in the "Delete block?" "dialogue"
    And I should see "Are you sure you want to continue?" in the "Delete block?" "dialogue"
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    And I wait to be redirected
    And "Logged in user" "block" should not exist

  @javascript
  Scenario: Cancel removing a block via modal should retain the block on the page
    Given I open the "Search forums" blocks action menu
    When I click on "Delete Search forums block" "link" in the "Search forums" "block"
    And I click on "Cancel" "button" in the "Delete block?" "dialogue"
    And I should not see "Delete block?"
    Then "Search forums" "block" should exist
