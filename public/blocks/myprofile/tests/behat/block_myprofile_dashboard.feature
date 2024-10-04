@block @block_myprofile
Feature: The logged in user block allows users to view their profile information in on the dashboard
  In order to enable the logged in user block on the dashboard
  As a user
  I can add the logged in user block to a the dashboard and view my information

  Scenario: View the logged in user block by a user on the dashboard
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And I log in as "teacher1"
    And I turn editing mode on
    When I add the "Logged in user" block
    Then I should see "Teacher One" in the "Logged in user" "block"
