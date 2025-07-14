@core @core_course
Feature: Courses can be searched for and moved in bulk.
  In order to manage a large number of courses
  As a Moodle Administrator
  I need to be able to search courses in bulk and move them around

  Background:
    Given the following "categories" exist:
      | name | category | idnumber |
      | Science | 0 | SCI |
      | English | 0 | ENG |
      | Miscellaneous | 0 | MISC |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Biology Y1 | BIO1 | MISC |
      | Biology Y2 | BIO2 | MISC |
      | English Y1 | ENG1 | ENG |
      | English Y2 | ENG2 | MISC |

  Scenario: Search courses finds correct results
    Given I log in as "admin"
    And I go to the courses management page
    When I set the field "Search" to "Biology"
    And I press "Search"
    Then I should see "Biology Y1"
    And I should see "Biology Y2"
    And I should not see "English Y1"
    And I should not see "English Y2"

  Scenario: Search courses displays contact names
    Given the following "users" exist:
      | username  | firstname | lastname | email          |
      | teacher1  | Teacher   | 1        | t1@example.com |
      | teacher2  | Teacher   | 2        | t2@example.com |
      | teacher3  | Teacher   | 3        | t3@example.com |
      | teacher4  | Teacher   | 4        | t3@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | BIO1 | editingteacher |
      | teacher2 | ENG2 | editingteacher |
      | teacher3 | BIO1 | teacher |
      | teacher4 | BIO1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Appearance > Courses" in site administration
    And I set the following fields to these values:
      | Teacher | 1 |
      | Non-editing teacher | 1 |
    And I press "Save changes"
    When I go to the courses management page
    And I set the field "Search" to "BIO1"
    And I press "Search"
    Then I should see "Biology Y1"
    When I follow "Biology Y1"
    Then I should see "Course contacts"
    And I should see "Teacher: Teacher 1, Teacher 4"
    And I should see "Non-editing teacher: Teacher 3"
    And I should not see "Teacher: Teacher 2"

  @javascript
  Scenario: Search courses and move results in bulk
    Given I log in as "admin"
    And I go to the courses management page
    And I set the field "Search" to "Biology"
    And I press "Search"
    When I select course "Biology Y1" in the management interface
    And I select course "Biology Y2" in the management interface
    And I set the field "menumovecoursesto" to "Science"
    And I press "Move"
    Then I should see "Successfully moved 2 courses into Science"
    And I wait to be redirected
    And I click on category "Science" in the management interface
    And I should see "Biology Y1"
    And I should see "Biology Y2"
