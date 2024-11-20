@block @block_comments
Feature: Enable Block comments on the frontpage and view comments
  In order to enable the comments block on the frontpage
  As a admin
  I can add the comments block to the frontpage

  Scenario: Add the comments block on the frontpage and add comments
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | comments  | System       | 1         | site-index      | side-pre      |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Show comments"
    And I add "I'm a comment from admin" comment to comments block
    And I log out
    When I log in as "teacher1"
    And I am on site homepage
    And I follow "Show comments"
    Then I should see "I'm a comment from admin"
