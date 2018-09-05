@block @block_blog_menu
Feature: Enable Block blog menu in a course
  In order to enable the blog menu in a course
  As a teacher
  I can add blog menu block to a course

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

  Scenario: Add the block to a the course when blogs are disabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | enableblogs | 0 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Blog menu" block
    Then I should see "Blogging is disabled!" in the "Blog menu" "block"

  Scenario: Add the block to a the course when blog associations are disabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | useblogassociations | 0 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Blog menu" block
    Then I should see "Blog entries" in the "Blog menu" "block"
    And I should see "Add a new entry" in the "Blog menu" "block"
    And I should not see "View all entries for this course" in the "Blog menu" "block"
    And I should not see "View my entries about this course" in the "Blog menu" "block"
    And I should not see "Add an entry about this course" in the "Blog menu" "block"

  Scenario: Add the block to a the course when blog associations are enabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | useblogassociations | 1 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Blog menu" block
    Then I should see "Blog entries" in the "Blog menu" "block"
    And I should see "Add a new entry" in the "Blog menu" "block"
    And I should see "View all entries for this course" in the "Blog menu" "block"
    And I should see "View my entries about this course" in the "Blog menu" "block"
    And I should see "Add an entry about this course" in the "Blog menu" "block"

  Scenario: Add the block to a the course when RSS is disabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | enablerssfeeds | 0 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Blog menu" block
    Then I should not see "Blog RSS feed" in the "Blog menu" "block"
    And I should see "Add a new entry" in the "Blog menu" "block"

  Scenario: Add the block to a the course when RSS is enabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | enablerssfeeds | 1 |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Blog menu" block
    Then I should see "Blog RSS feed" in the "Blog menu" "block"
    And I should see "Add a new entry" in the "Blog menu" "block"
