@block @block_blog_recent
Feature: Feature: Users can use the recent blog entries block to view recent blog entries.
  In order to enable the recent blog entries in a course
  As a teacher
  I can add recent blog entries block to a course

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

  Scenario: Add the recent blogs block to a course when blogs are disabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Recent blog entries" block
    And the following config values are set as admin:
      | enableblogs | 0 |
    And I reload the page
    Then I should see "Blogging is disabled!" in the "Recent blog entries" "block"

  Scenario: Add the recent blogs block to a course when there are not any blog posts
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Recent blog entries" block
    Then I should see "No recent entries" in the "Recent blog entries" "block"
