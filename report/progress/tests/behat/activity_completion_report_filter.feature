@report @report_progress
Feature: Teacher can view and filter activity completion data by group, activity type and section.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | groupmode |
      | Course 1 | C1        | topics | 1                | 1         |
    And the following "activities" exist:
      | activity | name          | intro   | course | idnumber | section | completion | completionview |
      | quiz     | My quiz B     | A3 desc | C1     | quizb    | 0       | 2          | 1              |
      | quiz     | My quiz A     | A3 desc | C1     | quiza    | 1       | 2          | 1              |
      | page     | My page       | A4 desc | C1     | page1    | 2       | 2          | 1              |
      | assign   | My assignment | A1 desc | C1     | assign1  | 2       | 2          | 1              |
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
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then "My quiz B" "link" should appear before "My quiz A" "link" in the "completion-progress" "table"
    And I should see "My assignment" in the "completion-progress" "table"
    And I should see "My page" in the "completion-progress" "table"
    And I should see "Student One" in the "completion-progress" "table"
    And I should see "Student Two" in the "completion-progress" "table"
    And I set the field "Separate groups" to "Group 1"
    And I set the field "Include" to "Quizzes"
    And I set the field "Activity order" to "Alphabetical"
    And "My quiz A" "link" should appear before "My quiz B" "link" in the "completion-progress" "table"
    And I should not see "My assignment" in the "completion-progress" "table"
    And I should not see "My page" in the "completion-progress" "table"
    And I should see "Student One" in the "completion-progress" "table"
    And I should not see "Student Two" in the "completion-progress" "table"
    And I set the field "Separate groups" to "All participants"
    And I set the field "Include" to "All activities and resources"
    And I set the field "Section" to "Topic 1"
    And I should not see "My assignment" in the "completion-progress" "table"
    And I should not see "My page" in the "completion-progress" "table"
    And I should not see "My assignment" in the "completion-progress" "table"
    And I should not see "My quiz B" in the "completion-progress" "table"
    And I should see "My quiz A" in the "completion-progress" "table"
    And I should see "Quiz" in the "activityinclude" "select"
    And I should not see "Page" in the "activityinclude" "select"
    And I set the field "Section" to "Topic 2"
    And I should not see "Quiz" in the "activityinclude" "select"
    And I should see "Page" in the "activityinclude" "select"
    And I should see "Assignment" in the "activityinclude" "select"
    And I set the field "Include" to "Page"
    And I set the field "Section" to "Topic 1"
    And I should see "Page" in the "activityinclude" "select"
