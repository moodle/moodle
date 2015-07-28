@block @block_tags @core_tag
Feature: Block tags displaying course tags
  In order to tag courses
  As a user
  I need to be able to use the block tags

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
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
      | student2 | c1     | student        |
    And I log in as "admin"
    And I set the following administration settings values:
      | Show course tags | 1 |
    And I log out

  Scenario: Add Tags block to tag courses in a course
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Tags" block
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should not see "Neverusedtag" in the "Tags" "block"
    And I click on "more..." "link" in the "Tags" "block"
    And I should not see "Neverusedtag"
    And I follow "c1"
    And I set the field "coursetag_new_tag" to "Dogs, Mice"
    And I press "Add"
    And I should see "Dogs" in the "Tags" "block"
    And I should see "Mice" in the "Tags" "block"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I should see "Dogs" in the "Tags" "block"
    And I set the field "coursetag_new_tag" to "Cats, Dogs"
    And I press "Add"
    And I should see "Dogs" in the "Tags" "block"
    And I should see "Cats" in the "Tags" "block"
    And I click on "more..." "link" in the "Tags" "block"
    And "Cats" "link" should appear before "Dogs" "link"
    And "Dogs" "link" should appear before "Mice" "link"
    And I follow "My tags"
    And I should see "Dogs"
    And I should see "Cats"
    And I should not see "Mice"
    And I follow "All tags"
    And I follow "Popularity"
    And "Mice" "link" should appear before "Dogs" "link"
    And I should not see "Neverusedtag"
    And I log out
