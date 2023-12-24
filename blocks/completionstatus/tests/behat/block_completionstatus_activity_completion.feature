@block @block_completionstatus @core_completion
Feature: Enable Block Completion in a course using activity completion
  In order to view the completion block in a course
  As a teacher
  I can add completion block to a course and set up activity completion

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | course | idnumber   | name             | gradepass | completion   | completionview | completionusegrade | completionpassgrade |
      | page       | C1     | page1      | Test page name   |           | 2            | 1              | 0                  | 0                   |
      | assign     | C1     | assign1    | Test assign name | 50        | 2            | 0              | 1                  | 1                   |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | completionstatus | Course       | C1        | course-view-*   | side-pre      |

  Scenario: Completion status block when student has not started any activities
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test page name | 1 |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "0 of 1" in the "Activity completion" "table_row"

  Scenario: Completion status block when student has completed a page
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test page name | 1 |
    And I press "Save changes"
    When I am on the "Test page name" "page activity" page logged in as student1
    And I am on "Course 1" course homepage
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "1 of 1" in the "Activity completion" "table_row"
    And I follow "More details"
    And I should see "Yes" in the "Activity completion" "table_row"

  Scenario: Completion status block with items with passing grade
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test assign name | 1 |
    And I press "Save changes"
    And the following "grade grades" exist:
      | gradeitem           | user     | grade |
      | Test assign name    | student1 | 53    |
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Complete" in the "Course completion status" "block"
    And I should see "1 of 1" in the "Activity completion" "table_row"
    And I trigger cron
    And I am on "Course 1" course homepage
    And I follow "More details"
    And I should see "Achieving grade, Achieving passing grade" in the "Activity completion" "table_row"
    And I should see "Yes" in the "Activity completion" "table_row"

  Scenario: Completion status block with items with failing grade
    Given I am on the "Course 1" course page logged in as teacher1
    And the following "grade grades" exist:
      | gradeitem           | user     | grade |
      | Test assign name    | student1 | 49    |
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Test assign name | 1 |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Status: Not yet started" in the "Course completion status" "block"
    And I should see "0 of 1" in the "Activity completion" "table_row"
    And I trigger cron
    And I am on "Course 1" course homepage
    And I follow "More details"
    And I should see "Achieving grade, Achieving passing grade" in the "Activity completion" "table_row"
    And I should see "No" in the "Activity completion" "table_row"
