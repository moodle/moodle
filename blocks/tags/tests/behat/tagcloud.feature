@block @block_tags @core_tag
Feature: Block tags displaying tag cloud
  In order to view system tags
  As a user
  I need to be able to use the block tags

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "tags" exist:
      | name         | tagtype  |
      | Neverusedtag | official |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | student1 | c1     | student        |
    And I log in as "teacher1"
    And I follow "Preferences" in the user menu
    And I follow "Edit profile"
    And I expand all fieldsets
    And I set the field "Enter tags separated by commas" to "Dogs, Cats"
    And I press "Update profile"
    And I log out

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
    And I should see "Log in to the site" in the ".breadcrumb" "css_element"

  Scenario: Add Tags block in a course
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Tags" block
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "Dogs" in the "Tags" "block"
    And I should see "Cats" in the "Tags" "block"
    And I should not see "Neverusedtag" in the "Tags" "block"
    And I click on "Dogs" "link" in the "Tags" "block"
    And I should see "Users tagged with \"Dogs\": 1"
    And I should see "Teacher 1"
    And I log out
