@block @block_myprofile
Feature: The logged in user block allows users to view their profile information on the front page
  In order to enable the logged in user block on the frontpage
  As an admin
  I can add the logged in user block to the frontpage and view my information

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | One      | teacher1@example.com | T1       |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | myprofile | System       | 1         | site-index      | side-pre      |

  Scenario: Try to view the logged in user block as a guest
    Given I log in as "guest"
    When I am on site homepage
    Then I should not see "Logged in user"

  Scenario: View the logged in user block by a logged in user
    Given I log in as "teacher1"
    When I am on site homepage
    Then I should see "Teacher One" in the "Logged in user" "block"
