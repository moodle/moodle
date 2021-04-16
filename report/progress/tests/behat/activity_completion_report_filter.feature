@report @report_progress
Feature: Teacher can view and filter activity completion data by group and activity type.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | groupmode |
      | Course 1 | C1        | topics | 1                | 1         |
    And the following "activities" exist:
      | activity | name          | intro   | course | idnumber | section | completion | completionview |
      | quiz     | My quiz B     | A3 desc | C1     | quizb    | 0       | 2          | 1              |
      | quiz     | My quiz A     | A3 desc | C1     | quiza    | 0       | 2          | 1              |
      | page     | My page       | A4 desc | C1     | page1    | 0       | 2          | 1              |
      | assign   | My assignment | A1 desc | C1     | assign1  | 0       | 2          | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
      | teacher1 | G1    |
      | teacher1 | G2    |

  @javascript
  Scenario: Teacher can view the activity completion report using filtering and sorting options.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    Then "My quiz B" "link" should appear before "My quiz A" "link"
    And I should see "My assignment"
    And I should see "My page"
    And I should see "Student One"
    And I should see "Student Two"
    And I set the field "Separate groups" to "Group 1"
    And I set the field "Include" to "Quizzes"
    And I set the field "Activity order" to "Alphabetical"
    And "My quiz A" "link" should appear before "My quiz B" "link"
    And I should not see "My assignment"
    And I should not see "My page"
    And I should see "Student One"
    And I should not see "Student Two"
