@block @block_tags @core_tag
Feature: Block tags displaying tag cloud
  In order to view system tags
  As a user
  I need to be able to use the block tags

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | interests |
      | teacher1 | Teacher | 1 | teacher1@example.com | Dogs, Cats |
      | student1 | Student | 1 | student1@example.com | |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "tags" exist:
      | name         | isstandard  |
      | Neverusedtag | 1           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | student1 | c1     | student        |

  Scenario: Add Tags block on a front page
    When I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Tags" block
    And I log out
    And I am on site homepage
    Then I should see "Dogs" in the "Tags" "block"
    And I should see "Cats" in the "Tags" "block"
    And I should not see "Neverusedtag" in the "Tags" "block"
    And I click on "Dogs" "link" in the "Tags" "block"
    And I should see "You are not logged in"

  Scenario: Add Tags block in a course
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Tags" block
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    Then I should see "Dogs" in the "Tags" "block"
    And I should see "Cats" in the "Tags" "block"
    And I should not see "Neverusedtag" in the "Tags" "block"
    And I click on "Dogs" "link" in the "Tags" "block"
    And I should see "User interests" in the ".tag-index-items h3" "css_element"
    And I should see "Teacher 1"
    And I log out
