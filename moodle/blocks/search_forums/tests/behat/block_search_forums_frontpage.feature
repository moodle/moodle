@block @block_search_forums @mod_forum
Feature: The search forums block allows users to search for forum posts on frontpage
  In order to search for a forum post
  As an administrator
  I can add the search forums block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "Search forums" block
    And I log out

  Scenario: Use the search forum block on the frontpage and search for posts as a user
    Given I log in as "student1"
    And I am on site homepage
    When I set the following fields to these values:
      | searchform_search | Moodle |
    And I press "Go"
    Then I should see "No posts"

  Scenario: Use the search forum block on the frontpage and search for posts as a guest
    Given I log in as "guest"
    And I am on site homepage
    When I set the following fields to these values:
      | searchform_search | Moodle |
    And I press "Go"
    Then I should see "No posts"
