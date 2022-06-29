@block @block_myoverview @javascript
Feature: If there is no course yet, users with capabilities have a link to create new course
  In order to create a course quickly
  As a course creator
  I can follow a link to create new course from my overview block

  Background:
    Given the following "users" exist:
      | username | firstname      | lastname | email                | idnumber |
      | creator1 | Course creator | X        | creator1@example.com | CC1      |
      | teacher1 | Teacher        | X        | teacher1@example.com | T1       |
    And the following "system role assigns" exist:
      | user     | course               | role           |
      | creator1 | Acceptance test site | coursecreator  |
      | teacher1 | Acceptance test site | editingteacher |

  Scenario: Course creators can see a link to new course form from my overview block
    Given I am on the "My courses" page logged in as "creator1"
    And I should see "No courses"
    And I should see "Create new course" in the "region-main" "region"
    And I should not see "Add a new course"
    When I click on "Create new course" "link" in the "region-main" "region"
    Then I should see "Add a new course"

  Scenario: Teachers don't see any link to create new course at my overview block
    Given I am on the "My courses" page logged in as "teacher1"
    When I should see "No courses"
    Then I should not see "Create new course"

  Scenario: Course creators on a subcategory can see a link to new course form from my overview block
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | CAT1     | CAT2     |
    And the following "role assigns" exist:
      | user     | role          | contextlevel | reference |
      | teacher1 | coursecreator | Category     | CAT2      |
    And I am on the "My courses" page logged in as "teacher1"
    And I should see "No courses"
    And I should see "Create new course" in the "region-main" "region"
    And I should not see "Add a new course"
    When I click on "Create new course" "link" in the "region-main" "region"
    Then I should see "Add a new course"
    And I should see "Cat 2" in the "page-header" "region"
