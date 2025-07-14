@core @core_my @block_myoverview
Feature: Run tests over my courses page

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
    And "Create course" "button" should not exist in the "page-header" "region"
    And "Manage courses" "button" should not exist in the "page-header" "region"
    When I click on "Create course" "button" in the "page-content" "region"
    Then I should see "Add a new course"
    And I am on the "My courses" page
    And I click on "Manage course categories" "button" in the "page-content" "region"
    And I should see "Manage course categories and courses"
    # Check that the expected buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | admin   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should exist in the "page-header" "region"
    And "Manage courses" "button" should exist in the "page-header" "region"
    And "Create course" "button" should not exist in the "page-content" "region"
    And "Manage courses" "button" should not exist in the "page-content" "region"
    And "Manage course categories" "button" should not exist in the "page-content" "region"

  Scenario: User without creating a course and managing category permissions cannot see any link
    When I am on the "My courses" page logged in as "user1"
    Then "Create course" "button" should not exist
    And "Manage courses" "button" should not exist
    And "Manage course categories" "button" should not exist
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should not exist
    And "Manage courses" "button" should not exist
    And "Manage course categories" "button" should not exist

  Scenario: User without capability to browse courses cannot see any link
    Given the following "permission overrides" exist:
      | capability                     | permission | role | contextlevel | reference |
      | moodle/category:viewcourselist | Prevent    | user | System       |           |
    When I am on the "My courses" page logged in as "user1"
    Then "Create course" "button" should not exist
    And "Manage courses" "button" should not exist
    And "Manage course categories" "button" should not exist
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should not exist
    And "Manage courses" "button" should not exist
    And "Manage course categories" "button" should not exist

  @javascript
  Scenario: User with creating a course permission can see the Create course link only
    Given the following "permission overrides" exist:
      | capability           | permission | role  | contextlevel | reference |
      | moodle/course:create | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Create course" "button" should exist in the "page-content" "region"
    But "Manage course categories" "button" should not exist
    And "Create course" "button" should not exist in the "page-header" "region"
    And I click on "Create course" "button"
    And I should see "Add a new course"
    And "CatA" "autocomplete_selection" should exist
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should exist in the "page-header" "region"
    And "Manage courses" "button" should not exist
    And "Create course" "button" should not exist in the "page-content" "region"

  Scenario: User with managing a category permission can see the Manage course link only
    Given the following "permission overrides" exist:
      | capability             | permission | role  | contextlevel | reference |
      | moodle/category:manage | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Manage course categories" "button" should exist in the "page-content" "region"
    And "Create course" "button" should not exist
    And I click on "Manage course categories" "button" in the "page-content" "region"
    And I should see "Manage course categories and courses"
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Manage courses" "button" should exist in the "page-header" "region"
    And "Create course" "button" should not exist
    And "Manage courses" "button" should not exist in the "page-content" "region"

  @javascript
  Scenario: User with both creating a course and managing a category permission can see both links
    Given the following "permission overrides" exist:
      | capability             | permission | role  | contextlevel | reference |
      | moodle/course:create   | Allow      | role1 | Category     | cata      |
      | moodle/category:manage | Allow      | role1 | Category     | cata      |
    When I am on the "My courses" page logged in as "user1"
    Then "Create course" "button" should exist in the "page-content" "region"
    And "Manage course categories" "button" should exist in the "page-content" "region"
    And "Create course" "button" should not exist in the "page-header" "region"
    And "Manage courses" "button" should not exist in the "page-header" "region"
    And I click on "Create course" "button"
    And I should see "Add a new course"
    And "CatA" "autocomplete_selection" should exist
    And I am on the "My courses" page
    And I click on "Manage course categories" "button"
    And I should see "Manage course categories and courses"
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should exist in the "page-header" "region"
    And "Manage courses" "button" should exist in the "page-header" "region"
    And "Create course" "button" should not exist in the "page-content" "region"
    And "Manage courses" "button" should not exist in the "page-content" "region"

  @javascript
  Scenario: Admin can see relevant blocks but not add or move them
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Text" block to the default region with:
      | Text block title | Text on all pages                  |
      | Content          | This is visible on all pages       |
    And I configure the "Text on all pages" block
    And I set the following fields to these values:
      | Page contexts    | Display throughout the entire site |
      | Default region   | Right                              |
    And I click on "Save changes" "button" in the "Configure Text on all pages block" "dialogue"
    And I should see "This is visible on all pages"
    And "Move Text on all pages block" "menuitem" should exist in the "Text on all pages" "block"
    When I am on the "My courses" page
    # Check blocks visible but are "locked" in place.
    Then "Course overview" "text" should exist in the "region-main" "region"
    And I should not see "Add a block"
    And I should see "This is visible on all pages"
    And "Move Text on all pages block" "menuitem" should not exist in the "Text on all pages" "block"
    And "Move Course overview block" "menuitem" should not exist in the "Course overview" "block"
    And "Actions menu" "icon" in the "Course overview" "block" should not be visible

  Scenario: User with creating a course permission can't see the Request course link
    Given the following "permission overrides" exist:
      | capability            | permission | role  | contextlevel | reference |
      | moodle/course:request | Allow      | user  | System       |           |
    When I am on the "My courses" page logged in as "admin"
    Then "Create course" "button" should exist in the "page-content" "region"
    And "Request a course" "button" should not exist
    And "Create course" "button" should not exist in the "page-header" "region"
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | admin   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should exist in the "page-header" "region"
    And "Request a course" "button" should not exist
    And "Create course" "button" should not exist in the "page-content" "region"

  Scenario: User without creating a course but with course request permission could see the Request course link
    Given the following "permission overrides" exist:
      | capability            | permission | role  | contextlevel | reference |
      | moodle/course:request | Allow      | user  | System       |           |
    When I am on the "My courses" page logged in as "user1"
    Then "Request a course" "button" should exist in the "page-content" "region"
    And "Create course" "button" should not exist in the "page-content" "region"
    And "Create course" "button" should not exist in the "page-header" "region"
    And "Request a course" "button" should not exist in the "page-header" "region"
    # Check the request a course button is not displayed when this feature is disabled.
    And the following config values are set as admin:
      | enablecourserequests | 0 |
    And I am on the "My courses" page logged in as "user1"
    And "Request a course" "button" should not exist
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And the following config values are set as admin:
      | enablecourserequests | 1 |
    And I am on the "My courses" page
    And "Request a course" "button" should exist in the "page-header" "region"
    And "Create course" "button" should not exist
    And "Request a course" "button" should not exist in the "page-content" "region"

  Scenario: User without creating nor course request permission shouldn't see any Request course link
    Given I am on the "My courses" page logged in as "user1"
    Then "Request a course" "button" should not exist in the "page-content" "region"
    And "Create course" "button" should not exist in the "page-content" "region"
    And "Manage courses" "button" should not exist in the "page-content" "region"
    # Check that the same buttons are displayed in the header when the user is enrolled in a course.
    But the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
      | format    | topics   |
    And the following "course enrolment" exists:
      | user   | user1   |
      | course | C1      |
      | role   | student |
    And I am on the "My courses" page
    And "Create course" "button" should not exist
    And "Request a course" "button" should not exist
    And "Manage courses" "button" should not exist
