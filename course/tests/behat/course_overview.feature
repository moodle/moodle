@core @core_course
Feature: Users can access the course activities overview page
  In order to access the course activities overview page
  As a user
  I should be able to navigate to the course activities overview page

  Background:
    Given the following "users" exist:
      | username           | firstname           | lastname | email                          |
      | teacher1           | Teacher             | 1        | teacher1@example.com           |
      | student1           | Student             | 1        | student1@example.com           |
    And the following "courses" exist:
      | fullname | shortname | format | numsections | initsections |
      | Course 1 | C1        | topics | 1           | 1            |
    And the following "course enrolments" exist:
      | user               | course | role           |
      | teacher1           | C1     | editingteacher |
      | student1           | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | idnumber | name                 |
      | assign   | C1     | 1       | 1        | Test assignment name |

  Scenario: Teacher can access the course overview page
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I follow "Activities"
    Then I should see "Activities"
    And I should see "Go to Assignments overview"
    And I follow "Go to Assignments overview"
    And I should see "Test assignment name"
    And I should see "Needs grading: 0"

  Scenario: Student can access the course overview page
    Given I am on the "C1" "Course" page logged in as "student1"
    When I follow "Activities"
    Then I should see "Activities"
    And I should see "Go to Assignments overview"
    And I follow "Go to Assignments overview"
    And I should see "Test assignment name"
    And I should see "No submission"

  Scenario: The activities overview shows only the type of activities present in the course
    Given the following "activities" exist:
      | activity | course | section | idnumber | name             | visible |
      | forum    | C1     | 1       | 1        | Test forum name  | 1       |
      | choice   | C1     | 1       | 2        | Test choice name | 0       |
    # Teacher can see all activities.
    When I am on the "Course 1" "course > activities" page logged in as "teacher1"
    And I should see "Assignments" in the "region-main" "region"
    And I should see "Forums" in the "region-main" "region"
    And I should see "Choices" in the "region-main" "region"
    And I should not see "Databases" in the "region-main" "region"
    And I should not see "Feedback" in the "region-main" "region"
    And I should not see "Glossaries" in the "region-main" "region"
    And I should not see "Lessons" in the "region-main" "region"
    And I should not see "External tools" in the "region-main" "region"
    And I should not see "Quizzes" in the "region-main" "region"
    And I should not see "SCORM packages" in the "region-main" "region"
    And I should not see "Wikis" in the "region-main" "region"
    And I should not see "Workshops" in the "region-main" "region"
    And I should not see "Resources" in the "region-main" "region"
    # Student can see only visible activities.
    And I am on the "Course 1" "course > activities" page logged in as "student1"
    And I should see "Assignments" in the "region-main" "region"
    And I should see "Forums" in the "region-main" "region"
    And I should not see "Choices" in the "region-main" "region"
    And I should not see "Databases" in the "region-main" "region"
    And I should not see "Feedback" in the "region-main" "region"
    And I should not see "Glossaries" in the "region-main" "region"
    And I should not see "Lessons" in the "region-main" "region"
    And I should not see "External tools" in the "region-main" "region"
    And I should not see "Quizzes" in the "region-main" "region"
    And I should not see "SCORM packages" in the "region-main" "region"
    And I should not see "Wikis" in the "region-main" "region"
    And I should not see "Workshops" in the "region-main" "region"
    And I should not see "Resources" in the "region-main" "region"

  @javascript
  Scenario: Teacher can expand and collapse course overview items
    # Create another activity to test each activity type could be expanded independently.
    Given the following "activities" exist:
      | activity | course | section | idnumber | name            |
      | forum    | C1     | 1       | 1        | Test forum name |
    And I am on the "Course 1" "course > activities" page logged in as "teacher1"
    And I should see "Assignments" in the "assign_overview_collapsible" "region"
    And I should see "Forums" in the "forum_overview_collapsible" "region"
    And I should not see "Go to Assignments overview" in the "assign_overview_collapsible" "region"
    And I should not see "Go to Forums overview" in the "forum_overview_collapsible" "region"
    When I click on "Expand" "link" in the "assign_overview_collapsible" "region"
    Then I should see "Go to Assignments overview" in the "assign_overview_collapsible" "region"
    And I should not see "Go to Forums overview" in the "forum_overview_collapsible" "region"
    And I click on "Collapse" "link" in the "assign_overview_collapsible" "region"
    And I should not see "Go to Assignments overview" in the "assign_overview_collapsible" "region"
    And I should not see "Go to Forums overview" in the "forum_overview_collapsible" "region"

  Scenario: Course overview shows the course present activity types
    Given the following "activities" exist:
      | activity        | course | name        |
      | book            | C1     | Activity 2  |
      | choice          | C1     | Activity 3  |
      | data            | C1     | Activity 4  |
      | feedback        | C1     | Activity 5  |
      | folder          | C1     | Activity 6  |
      | forum           | C1     | Activity 7  |
      | glossary        | C1     | Activity 8  |
      | imscp           | C1     | Activity 10 |
      | label           | C1     | Activity 11 |
      | lesson          | C1     | Activity 12 |
      | lti             | C1     | Activity 13 |
      | page            | C1     | Activity 14 |
      | quiz            | C1     | Activity 15 |
      | resource        | C1     | Activity 16 |
      | scorm           | C1     | Activity 17 |
      | url             | C1     | Activity 18 |
      | wiki            | C1     | Activity 19 |
      | workshop        | C1     | Activity 20 |
    Given I am on the "Course 1" "course > activities" page logged in as "teacher1"
    And I should see "Assignments" in the "assign_overview_collapsible" "region"
    And I should see "Choices" in the "choice_overview_collapsible" "region"
    And I should see "Databases" in the "data_overview_collapsible" "region"
    And I should see "Feedback" in the "feedback_overview_collapsible" "region"
    And I should see "Forums" in the "forum_overview_collapsible" "region"
    And I should see "Glossaries" in the "glossary_overview_collapsible" "region"
    And I should see "Lessons" in the "lesson_overview_collapsible" "region"
    And I should see "External tools" in the "lti_overview_collapsible" "region"
    And I should see "Quizzes" in the "quiz_overview_collapsible" "region"
    And I should see "SCORM packages" in the "scorm_overview_collapsible" "region"
    And I should see "Wikis" in the "wiki_overview_collapsible" "region"
    And I should see "Workshops" in the "workshop_overview_collapsible" "region"
    # All resources are grouped.
    And I should see "Resources" in the "resource_overview_collapsible" "region"
