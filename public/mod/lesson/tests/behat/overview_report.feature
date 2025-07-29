@mod @mod_lesson
Feature: Testing overview_report in mod_lesson
  In order to list all lessons in a course
  As a user
  I need to be able to see the lesson overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Username  | 1        |
      | student2 | Username  | 2        |
      | student3 | Username  | 3        |
      | teacher1 | Teacher   | T        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity  | name      | course | idnumber | retake  | deadline     |
      | lesson    | Lesson 1  | C1     | lesson1  | 1       | ##tomorrow## |
      | lesson    | Lesson 2  | C1     | lesson2  | 0       | 0            |
    And the following "mod_lesson > pages" exist:
      | lesson       | qtype     | title      | content                                |
      | lesson1      | truefalse | Question 1 | The number 10 is greater than 5        |
    And the following "mod_lesson > answers" exist:
      | page       | answer    | jumpto         | score   |
      | Question 1 | True      | End of lesson  |  1      |
      | Question 1 | False     | End of lesson  |  0      |
    And the following "mod_lesson > submissions" exist:
      | lesson    | user     | grade  |
      | Lesson 1  | student1 | 50     |
      | Lesson 1  | student1 | 60     |
      | Lesson 1  | student1 | 100    |
      | Lesson 1  | student2 | 90     |

  @javascript
  Scenario: Teacher can see the lesson relevant information in the lesson overview
    When I am on the "Course 1" "course > activities > lesson" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Lesson activities" table:
      | Name     | Students who attempted | Total attempts      | Due date        |
      | Lesson 1 | 2 of 3                 | 4                   | Tomorrow        |
      | Lesson 2 | 0 of 3                 | 0                   | -               |
    And I click on "4" "button" in the "Lesson 1" "table_row"
    And I should see "This lesson allows students to attempt it more than once."
    And I should see "Average attempts per student: 2"
    And I press the escape key
    And "0" "button" should not exist in the "Lesson 2" "table_row"
    And I click on "View" "link" in the "Lesson 1" "table_row"
    And I should see "Reports" in the "page-header" "region"

  Scenario: Teacher can see the lesson overview with all lessons with retakes disabled
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C2     | student        |
      | teacher1 | C2     | editingteacher |
    And the following "activities" exist:
      | activity  | name      | course | idnumber | retake  |
      | lesson    | Lesson 1  | C2     | lesson1  | 0       |
      | lesson    | Lesson 2  | C2     | lesson2  | 0       |
    When I am on the "Course 2" "course > activities > lesson" page logged in as "teacher1"
    Then I should not see "Total attempts" in the "lesson_overview_collapsible" "region"
    And the following should exist in the "Table listing all Lesson activities" table:
      | Name     | Students who attempted | Due date        |
      | Lesson 1 | 0 of 1                 | -               |
      | Lesson 2 | 0 of 1                 | -               |

  Scenario: Students can see the lesson relevant information in the lesson overview
    When I am on the "Course 1" "course > activities > lesson" page logged in as "student1"
    Then I should not see "Actions" in the "lesson_overview_collapsible" "region"
    And I should not see "Students who attempted" in the "lesson_overview_collapsible" "region"
    And I should not see "Total attempts" in the "lesson_overview_collapsible" "region"
    And the following should exist in the "Table listing all Lesson activities" table:
      | Name     | Due date        |
      | Lesson 1 | Tomorrow        |
      | Lesson 2 | -               |

  Scenario: The lesson index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Lessons" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course, with dates and other information."
    And I should see "Name" in the "lesson_overview_collapsible" "region"
    And I should see "Students who attempted" in the "lesson_overview_collapsible" "region"
    And I should see "Total attempts" in the "lesson_overview_collapsible" "region"
    And I should see "Actions" in the "lesson_overview_collapsible" "region"

  Scenario: The lesson overview report should generate log events
    Given I am on the "Course 1" "course > activities > lesson" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'lesson'"
