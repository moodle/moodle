@block @block_xp @block_xp_plus_incompatible
Feature: A teacher can navigate through the pages of the plugin
  In order to view each page
  As a teacher
  I can follow the plugin's course navigation

  # The purpose of this scenario is to confirm that none of the page display an error.
  Scenario: A teacher visits the different pages
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Student   | One      | s1@example.com |
      | t1       | Teacher   | One      | t1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | s1       | c1     | student |
      | t1       | c1     | editingteacher |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add the "Level Up XP" block
    And I log out
    And I log in as "t1"
    When I am on "Course 1" course homepage
    Then I click on "Info" "link" in the "Level up!" "block"
    And I click on "Leaderboard" "link" in the ".block_xp" "css_element"
    And I click on "Report" "link" in the ".block_xp" "css_element"
    And I click on "Log" "link" in the ".block_xp" "css_element"
    And I click on "Levels" "link" in the ".block_xp" "css_element"
    And I click on "Appearance" "link" in the ".block_xp" "css_element"
    And I click on "Points" "link" in the ".block_xp" "css_element"
    And I click on "Settings" "link" in the ".block_xp" "css_element"
    And I click on "XP+" "link" in the ".block_xp" "css_element"
