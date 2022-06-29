@core @core_my
Feature: Run tests over my courses.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | 1        | user1@example.com |
    And the following "categories" exist:
      | name | category | idnumber |
      | CatA | 0        | cata     |
    And the following "roles" exist:
      | shortname | name   | archetype |
      | role1     | Role 1 |           |
    And the following "system role assigns" exist:
      | user  | role  | contextlevel | reference |
      | user1 | role1 | Category     | CatA      |

  Scenario: Admin can add new courses or manage them from my courses
    Given I am on the "My courses" page logged in as "admin"
    And I click on "Course management options" "link"
    And I click on "New course" "link"
    And I wait to be redirected
    Then I should see "Add a new course"
    And I am on the "My courses" page
    And I click on "Course management options" "link"
    And I click on "Manage courses" "link"
    And I should see "Manage course categories and courses"

  Scenario: User without creating a course and managing category permissions cannot see any link
    Given I am on the "My courses" page logged in as "user1"
    Then "Course management options" "link" should not exist

  Scenario: User without capability to browse courses cannot see any link
    Given the following "permission overrides" exist:
      | capability                     | permission | role | contextlevel | reference |
      | moodle/category:viewcourselist | Prevent    | user | System       |           |
    Given I am on the "My courses" page logged in as "user1"
    Then "Course management options" "link" should not exist

  @javascript
  Scenario: User with creating a course permission can see the Create course link only
    Given the following "permission overrides" exist:
      | capability           | permission | role  | contextlevel | reference |
      | moodle/course:create | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Course management options" "link" should exist
    And I click on "Course management options" "link"
    And I should see "New course"
    And I should not see "Manage courses"
    And I click on "New course" "link"
    And I wait to be redirected
    And I should see "Add a new course"
    And "CatA" "autocomplete_selection" should exist

  @javascript
  Scenario: User with managing a category permission can see the Manage course link only
    Given the following "permission overrides" exist:
      | capability             | permission | role  | contextlevel | reference |
      | moodle/category:manage | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Course management options" "link" should exist
    And I click on "Course management options" "link"
    And I should not see "New course"
    And I should see "Manage courses"
    And I click on "Manage courses" "link"
    And I wait to be redirected
    And I should see "Manage course categories and courses"

  @javascript
  Scenario: User with both creating a course and managing a category permission can see both links
    Given the following "permission overrides" exist:
      | capability             | permission | role  | contextlevel | reference |
      | moodle/course:create   | Allow      | role1 | Category     | cata      |
      | moodle/category:manage | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Course management options" "link" should exist
    And I click on "Course management options" "link"
    And I should see "New course"
    And I should see "Manage courses"
    And I click on "New course" "link"
    And I wait to be redirected
    And I should see "Add a new course"
    And "CatA" "autocomplete_selection" should exist
    And I am on the "My courses" page
    And I click on "Course management options" "link"
    And I click on "Manage courses" "link"
    And I wait to be redirected
    And I should see "Manage course categories and courses"

  @javascript
  Scenario: Admin can see relevant blocks but not add or move them
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I set the following fields to these values:
      | Page contexts    | Display throughout the entire site |
      | Text block title | Text on all pages                  |
      | Content          | This is visible on all pages       |
      | Default region   | Right                              |
    And I press "Save changes"
    And I should see "This is visible on all pages"
    And "Move Text on all pages block" "button" should exist in the "Text on all pages" "block"
    When I am on the "My courses" page
    # Check blocks visible but are "locked" in place.
    Then "Course overview" "text" should exist in the "region-main" "region"
    And I should not see "Add a block"
    And I should see "This is visible on all pages"
    And "Move Text on all pages block" "button" should not exist in the "Text on all pages" "block"
    And "Move Course overview block" "button" should not exist in the "Course overview" "block"
    And I click on "Actions menu" "icon" in the "Course overview" "block"
    And I should not see "Delete Course overview block"
