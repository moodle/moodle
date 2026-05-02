@mod @mod_quiz @core_badges @core_completion @javascript

Feature: Award badges based on course completion
  In order to ensure a student has learned the required material
  As a teacher
  I need a badge to be awarded only when the student completes the course or a set of courses.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
      | Course 2 | C2        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
      | Course       | C2        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name             | course | idnumber | attempts | gradepass | completion | completionpassgrade | completionusegrade |
      | quiz     | Test quiz name C1 | C1     | quiz1    | 2        | 5.00      | 2          | 1                   | 1                  |
      | quiz     | Test quiz name C2 | C2     | quiz1    | 2        | 5.00      | 2          | 1                   | 1                  |
    And quiz "Test quiz name C1" contains the following questions:
      | question       | page |
      | First question | 1    |
    And quiz "Test quiz name C2" contains the following questions:
      | question       | page |
      | First question | 1    |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | completionstatus | Course       | C1        | course-view-*   | side-pre      |
      | completionstatus | Course       | C2        | course-view-*   | side-pre      |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the field "Test quiz name C1" to "1"
    And I press "Save changes"
    And I am on the "Course 2" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the field "Test quiz name C2" to "1"
    And I press "Save changes"

  Scenario Outline: Badge awarded depending on the course completion by-date
    Given I am on the "Course 1" course page
    And the following "core_badges > Badge" exists:
      | name        | Course Badge 1               |
      | status      | 0                            |
      | type        | 2                            |
      | course      | C1                           |
      | description | Course badge 1 description   |
      | image       | badges/tests/behat/badge.png |
    And I navigate to "Badges" in current page administration
    And I click on "Course Badge 1" "link"
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Course completion"
    And I set the field "Enable" to "1"
    And I set the field "Day" to "1"
    And I set the field "Month" to "January"
    And I set the field "Year" to "<year>"
    And I press "Save"
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    And user "student1" has attempted "Test quiz name C1" with responses:
      | slot | response |
      | 1    | True     |
    # Completion cron won't mark the whole course completed unless the
    # individual criteria was marked completed more than a second ago. So
    # run it twice, first to mark the criteria and second for the course.
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    When I run the scheduled task "core\task\badges_cron_task"
    And I run all adhoc tasks
    And I navigate to "Badges" in current page administration
    And I click on "Course Badge 1" "link"
    Then I should see "Recipients (<count>)"
    And I select "Recipients (<count>)" from the "jump" singleselect
    And I <student1_visibility> see "Student 1"
    And I should not see "Student 2"

    Examples:
      | year                | count | student1_visibility |
      | ## +1 year ## %Y ## | 1     | should              |
      | ## -1 year ## %Y ## | 0     | should not          |

  Scenario Outline: Badge awarded depending on the courseset completion by-date
    Given I am logged in as "admin"
    And the following "core_badges > Badge" exists:
      | name        | Site Badge                   |
      | status      | 0                            |
      | description | Site badge description       |
      | image       | badges/tests/behat/badge.png |
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Edit" action in the "Site Badge" report row
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Completing a set of courses"
    And I set the field "courses" to "Course 1, Course 2"
    And I press "Add courses"
    And I expand all fieldsets
    # Set a very high grade requirement to ensure that the course completion criteria won't be marked completed just by passing the quiz.
    And I set the field with xpath "(//input[contains(@name, 'grade')])[1]" to "200"
    And I set the field with xpath "(//input[contains(@name, 'enabled')])[2]" to "1"
    And I set the field with xpath "(//select[contains(@name, 'day')])[2]" to "1"
    And I set the field with xpath "(//select[contains(@name, 'month')])[2]" to "January"
    And I set the field with xpath "(//select[contains(@name, 'year')])[2]" to "<year>"
    And I click on "Any of the selected courses is complete" "radio"
    And I press "Save"
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    And user "student1" has attempted "Test quiz name C1" with responses:
      | slot | response |
      | 1    | True     |
    And user "student1" has attempted "Test quiz name C2" with responses:
      | slot | response |
      | 1    | True     |
    # Completion cron won't mark the whole course completed unless the
    # individual criteria was marked completed more than a second ago. So
    # run it twice, first to mark the criteria and second for the course.
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    When I run the scheduled task "core\task\badges_cron_task"
    And I run all adhoc tasks
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Site Badge"
    Then I should see "Recipients (<count>)"
    And I select "Recipients (<count>)" from the "jump" singleselect
    And I <student1_visibility> see "Student 1"
    And I should not see "Student 2"

    Examples:
      | year                | count | student1_visibility |
      | ## +1 year ## %Y ## | 1     | should              |
      | ## -1 year ## %Y ## | 0     | should not          |
