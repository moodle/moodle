@block @block_badges
Feature: Enable Block Badges in a course without badges
  In order to view the badges block in a course
  As a teacher
  I can add badges block to a course and view the contents

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Add the block to a the course when badges are disabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Latest badges" block
    And the following config values are set as admin:
      | enablebadges | 0 |
    And I reload the page
    Then I should see "Badges are not enabled on this site." in the "Latest badges" "block"

  Scenario: Add the block to a the course when badges are enabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Latest badges" block
    Then I should see "You have no badges to display" in the "Latest badges" "block"
