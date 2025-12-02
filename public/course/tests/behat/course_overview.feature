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
      | fullname | shortname | format | numsections | initsections | enablecompletion |
      | Course 1 | C1        | topics | 2           | 1            | 1                |
    And the following "course enrolments" exist:
      | user               | course | role           |
      | teacher1           | C1     | editingteacher |
      | student1           | C1     | student        |
    And the following "activities" exist:
      | activity | course | section | idnumber | name                 | duedate           |
      | assign   | C1     | 1       | 1        | Test assignment name | ##tomorrow noon## |

  Scenario: Teacher can navigate to the course overview page
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I follow "Activities"
    Then I should see "Activities"
    And I should see "An overview of all activities in the course" in the "region-main" "region"
    And I should see "Assignments" in the "region-main" "region"

  Scenario: Student can navigate to the course overview page
    Given I am on the "C1" "Course" page logged in as "student1"
    When I follow "Activities"
    Then I should see "Activities"
    And I should see "An overview of all activities in the course" in the "region-main" "region"
    And I should see "Assignments" in the "region-main" "region"

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
    And I should not see "Test assignment name" in the "assign_overview_collapsible" "region"
    And I should not see "Forum overview" in the "forum_overview_collapsible" "region"
    When I click on "Expand" "link" in the "assign_overview_collapsible" "region"
    Then I should see "Test assignment name" in the "assign_overview_collapsible" "region"
    And I should not see "Forum overview" in the "forum_overview_collapsible" "region"
    And I click on "Collapse" "link" in the "assign_overview_collapsible" "region"
    And I should not see "Test assignment name" in the "assign_overview_collapsible" "region"
    And I should not see "Forum overview" in the "forum_overview_collapsible" "region"

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
      | qbank           | C1     | Activity 21 |
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
    # Qbanks and labels are not shown.
    And I should not see "Labels" in the "course-overview-page" "region"
    And I should not see "Question banks" in the "course-overview-page" "region"

  @javascript
  Scenario: The resources overview is loaded at the moment the section is expanded via Ajax
    Given the following "activities" exist:
      | activity | course | name       | completion | completionview |
      | book     | C1     | Activity 1 | 0          | 0              |
      | folder   | C1     | Activity 2 | 0          | 0              |
      | imscp    | C1     | Activity 3 | 0          | 0              |
    And I am on the "Course 1" "course > activities" page logged in as "teacher1"
    And "Activity 1" "link" should not exist in the "resource_overview_collapsible" "region"
    And "Activity 2" "link" should not exist in the "resource_overview_collapsible" "region"
    And "Activity 3" "link" should not exist in the "resource_overview_collapsible" "region"
    When I click on "Expand" "link" in the "resource_overview_collapsible" "region"
    Then "Activity 1" "link" should exist in the "resource_overview_collapsible" "region"
    And "Activity 2" "link" should exist in the "resource_overview_collapsible" "region"
    And "Activity 3" "link" should exist in the "resource_overview_collapsible" "region"

  @javascript
  Scenario: Students should see completion status in the overview when some activity has completion
    Given the following "activities" exist:
      | activity        | course | name        | completion | completionview |
      | book            | C1     | Activity 1  | 0          | 0              |
      | folder          | C1     | Activity 2  | 1          | 1              |
      | imscp           | C1     | Activity 3  | 1          | 1              |
    And I am on the "Course 1" "course" page logged in as "student1"
    And I toggle the manual completion state of "Activity 2"
    When I am on the "Course 1" "course > activities > resource" page
    Then I should see "-" in the "Activity 1" "table_row"
    And I should see "Done" in the "Activity 2" "table_row"
    And I should see "Mark as done" in the "Activity 3" "table_row"

  Scenario: Students should not see completion status in the overview if no activity has completion
    Given the following "activities" exist:
      | activity        | course | name        | completion | completionview |
      | book            | C1     | Activity 1  | 0          | 0              |
      | folder          | C1     | Activity 2  | 0          | 0              |
      | imscp           | C1     | Activity 3  | 0          | 0              |
    When I am on the "Course 1" "course > activities > resource" page logged in as "student1"
    Then I should not see "Status" in the "resource_overview_collapsible" "region"

  Scenario: The course overview name column informs about the activity and section
    Given the following "activities" exist:
      | activity | course | section | name       | content                    |
      | page     | C1     | 1       | Activity 1 | This is the page 1 content |
      | page     | C1     | 2       | Activity 2 | This is the page 2 content |
    And I am on the "Course 1" "course > activities > resource" page logged in as "teacher1"
    When I click on "Expand" "link" in the "resource_overview_collapsible" "region"
    Then I should see "Section 1" in the "Activity 1" "table_row"
    And I should see "Section 2" in the "Activity 2" "table_row"
    And I click on "Activity 1" "link" in the "Activity 1" "table_row"
    And I should see "This is the page 1 content"

  @javascript
  Scenario: Students can manage manual completions from the course overview
    Given the following "activities" exist:
      | activity | course | name       | completion | completionview |
      | folder   | C1     | Activity 1 | 1          | 1              |
    And I am on the "Course 1" "course > activities > resource" page logged in as "student1"
    And I should see "Mark as done" in the "Activity 1" "table_row"
    When I click on "Mark as done" "button" in the "Activity 1" "table_row"
    Then I should see "Done" in the "Activity 1" "table_row"
    And I click on "Done" "button" in the "Activity 1" "table_row"
    And I should see "Mark as done" in the "Activity 1" "table_row"

  Scenario: Students can see the automatic completion criterias in the course overview
    Given the following "activity" exists:
      | activity       | folder     |
      | name           | Activity 1 |
      | course         | C1         |
      | completion     | 2          |
      | completionview | 1          |
    When I am on the "Course 1" "course > activities > resource" page logged in as "student1"
    Then I should see "To do" in the "Activity 1" "table_row"
    And I should see "View" in the "Activity 1" "table_row"

  Scenario: The course overview page should log a page event and a resource list event
    Given the following "activity" exists:
      | activity       | folder     |
      | name           | Activity 1 |
      | course         | C1         |
    And I am on the "Course 1" "course > activities" page logged in as "teacher1"
    And I am on the "Course 1" "course > activities > resource" page logged in as "student1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    Then I set the field "Select a user" to "Teacher 1"
    And I click on "Get these logs" "button"
    And I should see "Course activities overview page viewed"
    And I should not see "viewed the list of resources"
    And I set the field "Select a user" to "Student 1"
    And I click on "Get these logs" "button"
    And I should see "Course activities overview page viewed"
    And I should see "viewed the list of resources"

  Scenario: Users can see a link to the old index when the activity does not provide overview information
    Given the following "activities" exist:
      | activity | course | name       |
      | lti     | C1     | Activity 1 |
      | lti     | C1     | Activity 2 |
      | assign   | C1     | Activity 3 |
    When I am on the "Course 1" "course > activities > lti" page logged in as "student1"
    And I should see "External tool"
    And I follow "External tool overview"
    And I should see "Activity 1"
    And I should see "Activity 2"
    # Check activities with integration do not show the link.
    And I am on the "Course 1" "course > activities > assign" page
    And I should not see "Assignment overview"

  Scenario: Activities overview provide completion information to the student
    Given the following "activities" exist:
      | activity | course | name       | completion | completionview |
      | choice   | C1     | Activity 1 | 2          | 1              |
      | choice   | C1     | Activity 2 | 2          | 1              |
      | choice   | C1     | Activity 3 | 1          | 0              |
      | choice   | C1     | Activity 4 | 0          | 0              |
    And I am on the "Activity 1" "activity" page logged in as "student1"
    When I am on the "Course 1" "course > activities > choice" page logged in as "student1"
    Then I should see "Status" in the "choice_overview_collapsible" "region"
    And I should see "Done" in the "Activity 1" "table_row"
    And I should see "To do" in the "Activity 2" "table_row"
    And I should see "Mark as done" in the "Activity 3" "table_row"
    And I should see "-" in the "Activity 4" "table_row"
    And I am on the "Course 1" "course > activities > choice" page logged in as "teacher1"
    And I should not see "Status" in the "choice_overview_collapsible" "region"
    And I should not see "To do" in the "Activity 2" "table_row"
    And I should not see "Mark as done" in the "Activity 3" "table_row"
    And I should not see "-" in the "Activity 4" "table_row"

  Scenario: Activities overview provide grade information to the student
    Given the following "activities" exist:
      | activity | course | name       |
      | lesson   | C1     | Activity 1 |
      | lesson   | C1     | Activity 2 |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I give the grade "42" to the user "Student 1" for the grade item "Activity 1"
    And I press "Save changes"
    When I am on the "Course 1" "course > activities > lesson" page logged in as "student1"
    Then I should see "Grade" in the "lesson_overview_collapsible" "region"
    And I should see "42.00" in the "Activity 1" "table_row"
    And I should see "-" in the "Activity 2" "table_row"
    When I am on the "Course 1" "course > activities > lesson" page logged in as "teacher1"
    And I should not see "Grade" in the "lesson_overview_collapsible" "region"

  Scenario: Activities name is properly filtered and rendered
    Given the following config values are set as admin:
      | formatstringstriptags | 0 |
    And the following "activity" exists:
      | activity  | assign                                                                                                             |
      | course    | C1                                                                                                                 |
      | section   | 1                                                                                                                  |
      | idnumber  | mathjax                                                                                                            |
      | name      | <span class="filter_mathjaxloader_equation">Announcements$$(a+b)=2$$<span class="nolink">$$(a+b)=2$$</span></span> |
    When I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then I should not see "span" in the "assign_overview_collapsible" "region"

  Scenario: Section name is properly filtered and rendered
    Given the following config values are set as admin:
      | formatstringstriptags | 0 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Edit settings" "link" in the "Section 1" "core_courseformat > Section actions menu"
    And I set the field "Section name" to "<span class='filter_mathjaxloader_equation'>Announcements$$(a+b)=2$$<span class='nolink'>$$(a+b)=2$$</span></span>"
    And I press "Save changes"
    When I am on the "Course 1" "course > activities > assign" page
    Then I should not see "span" in the "assign_overview_collapsible" "region"

  @javascript
  Scenario: Users in no group that cannot view all groups see an error on 'Separate groups' activities
    Given the following "users" exist:
      | username   | firstname   | lastname | email                   |
      | nonediting | Non-editing | Teacher  | nonediting1@example.com |
    And the following "course enrolments" exist:
      | user       | course | role    |
      | nonediting | C1     | teacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "activity" exists:
      | activity  | assign          |
      | course    | C1              |
      | section   | 1               |
      | idnumber  | separate        |
      | name      | Separate groups |
      | groupmode | 1               |
    When I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then I should not see "You are not a member of any group" in the "assign_overview_collapsible" "region"
    And I log out
    And I am on the "Course 1" "course > activities > assign" page logged in as "nonediting"
    And I should see "You are not a member of any group" in the "assign_overview_collapsible" "region"
    And I log out
    And I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    And I should see "You are not a member of any group" in the "assign_overview_collapsible" "region"

  Scenario: Not gradable activities don't show any Grade colum in the activity overview
#   Create an activity non-gradable from the creation.
    Given the following "activities" exist:
      | activity | name         | course | idnumber | grade |
      | assign   | Not gradable | C1     | assign2  | 0     |
    And I am on the "Test assignment name" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
#   Edit an activity to make it non-gradable.
    And I set the following fields to these values:
      | Feedback comments | 0    |
      | Annotate PDF      | 0    |
      | Grade > Type      | None |
    And I press "Save and return to course"
    And I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    And I should not see "Grade" in the "assign_overview_collapsible" "region"
#   Edit an activity to add non-gradable feedback grading.
    And I am on the "Not gradable" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I set the field "Feedback comments" to "1"
    And I press "Save and return to course"
    When I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    Then I should not see "Grade" in the "assign_overview_collapsible" "region"
#   Edit an activity to add gradable grading.
    And I am on the "Not gradable" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I set the field "Grade > Type" to "Point"
    And I press "Save and return to course"
    And I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    And I should not see "-" in the "Test assignment name" "table_row"
    And I should see "-" in the "Not gradable" "table_row"

  @javascript
  Scenario: Hidden section name is not shown in the activity overview for students
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I hide section "1"
    # Make stealth the activity to guarantee the students can see it.
    And I open "Test assignment name" actions menu
    And I choose "Availability > Make available but don't show on course page" in the open action menu
    # Teacher should see the section name.
    When I am on the "Course 1" "course > activities > assign" page
    Then I should see "Section 1" in the "Test assignment name" "table_row"
    # Student should not see the section name.
    But I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    And I should not see "Section 1" in the "Test assignment name" "table_row"

  @javascript
  Scenario: Unavailable activities are shown or hidden in the overview based on user capability
    # Add a date restriction to an activity, visible to students.
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I click on "Add restriction..." "button" in the "root" "core_availability > Availability Button Area"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "Direction" in the "1" "availability_date > Date Restriction" to "until"
    And I press "Save and return to course"
    # Teacher can see the activity and the link in the overview.
    When I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then I should see "Test assignment name" in the "assign_overview_collapsible" "region"
    And "Test assignment name" "link" should exist in the "assign_overview_collapsible" "region"
    And I should see "Name" in the "assign_overview_collapsible" "region"
    And I should see "Due date" in the "assign_overview_collapsible" "region"
    And I should see "Submissions" in the "assign_overview_collapsible" "region"
    And I should see "Actions" in the "assign_overview_collapsible" "region"
    # Student can see the activity but not the link in the overview.
    But I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    And I should see "Test assignment name" in the "assign_overview_collapsible" "region"
    And "Test assignment name" "link" should not exist in the "assign_overview_collapsible" "region"
    And I should see "Name" in the "assign_overview_collapsible" "region"
    And I should see "Due date" in the "assign_overview_collapsible" "region"
    And I should not see "Submission status" in the "assign_overview_collapsible" "region"
    And I should not see "Grade" in the "assign_overview_collapsible" "region"
