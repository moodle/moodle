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
    When I set the field "coursesearchbox" to "Biology"
    And I press "Go"
    Then I should see "Biology Y1"
    And I should see "Biology Y2"
    And I should not see "English Y1"
    And I should not see "English Y2"

  @javascript
  Scenario: Search courses and move results in bulk
    Given I log in as "admin"
    And I go to the courses management page
    And I set the field "coursesearchbox" to "Biology"
    And I press "Go"
    When I select course "Biology Y1" in the management interface
    And I select course "Biology Y2" in the management interface
    And I set the field "menumovecoursesto" to "Science"
    And I press "Move"
    Then I should see "Successfully moved 2 courses into Science"
    And I wait to be redirected
    And I click on category "Science" in the management interface
    And I should see "Biology Y1"
    And I should see "Biology Y2"
