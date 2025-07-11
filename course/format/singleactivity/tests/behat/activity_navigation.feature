@format @format_singleactivity
Feature: Activity navigation in a single activity course
  In order to quickly switch to another activity in a single activity course with multiple (hidden) activities
  As a teacher
  I need to use the activity navigation controls in activities

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | student2  | Student    | 2         | student2@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype |
      | Course 1 | C1        | singleactivity | forum        |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | forum      | Forum 1      | Test forum description      | C1     | forum1    | 0       |
      | assign     | Assignment 1 | Test assignment description | C1     | assign1   | 0       |
      | lesson     | Lesson 1     | Test lesson description     | C1     | lesson1   | 0       |

  Scenario: Step through activities in the course as a teacher
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    # The first activity (Forum 1) won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And I should see "Assignment 1" in the "#next-activity-link" "css_element"
    And I follow "Assignment 1"
    And I should see "Forum 1" in the "#prev-activity-link" "css_element"
    And I should see "Lesson 1" in the "#next-activity-link" "css_element"
    And I follow "Lesson 1"
    And I should see "Assignment 1" in the "#prev-activity-link" "css_element"
    And "#next-activity-link" "css_element" should not exist

  Scenario: The activity navigation controls are available as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    # The first activity won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And "#next-activity-link" "css_element" should exist
    And "Jump to..." "field" should exist

  Scenario: The activity navigation asks for login to guest user
    Given I log in as "guest"
    When I am on "Course 1" course homepage
    Then I should see "Guests cannot access this course. Please log in"

  Scenario: The activity navigation asks for login to not enrolled user
    Given I log in as "student2"
    When I am on "Course 1" course homepage
    Then I should see "You cannot enrol yourself in this course"

  Scenario: The single activity course format supports multilang course names
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    When I am on the "Course 1" "course editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I set the field "Course full name" in the "General" "fieldset" to "<span lang=\"de\" class=\"multilang\">Kurs</span><span lang=\"en\" class=\"multilang\">Course</span> 1"
    And I click on "Save and display" "button"
    Then I should see "Course 1" in the ".page-header-headings" "css_element"
    And I should not see "KursCourse 1" in the ".page-header-headings" "css_element"
