@core @core_course
Feature: Managers can create courses
  In order to group users and contents
  As a manager
  I need to create courses and set default values on them

  @javascript
  Scenario: Courses are created with the default announcements forum
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And I log in as "admin"
    And I create a course with:
      | Course full name | Course 1 |
      | Course short name | C1 |
    And I enrol "Teacher 1" user as "Teacher"
    And I enrol "Student 1" user as "Student"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Latest announcements" block
    And I am on the Announcements "forum activity" page
    And "Add discussion topic" "link" should exist
    And I navigate to "Subscriptions" in current page administration
    And I should see "Forced subscription"
    And I log out
    And I am on the Announcements "forum activity" page logged in as student1
    And "Add a new topic" "link" should not exist

  Scenario: Create a course from the management interface and return to it
    Given the following "courses" exist:
      | fullname | shortname | idnumber | startdate | enddate   |
      | Course 1 | Course 1  | C1       | 957139200 | 960163200 |
    And I log in as "admin"
    And I go to the courses management page
    And I should see the "Categories" management page
    And I click on category "Category 1" in the management interface
    And I should see the "Course categories and courses" management page
    And I click on "Create new course" "link" in the "#course-listing" "css_element"
    When I set the following fields to these values:
      | Course full name | Course 2 |
      | Course short name | Course 2 |
      | Course summary | Course 2 summary |
      | id_startdate_day | 24 |
      | id_startdate_month | October |
      | id_startdate_year | 2015 |
      | id_enddate_day | 24 |
      | id_enddate_month | October |
      | id_enddate_year | 2016 |
    And I press "Save and return"
    Then I should see the "Course categories and courses" management page
    And I click on "Sort by Course time created ascending" "link" in the ".course-listing-actions" "css_element"
    And I should see course listing "Course 1" before "Course 2"
    And I click on "Course 2" "link" in the "region-main" "region"
    And I click on "Edit" "link" in the ".course-detail" "css_element"
    And the following fields match these values:
      | Course full name | Course 2 |
      | Course short name | Course 2 |
      | Course summary | Course 2 summary |
      | id_startdate_day | 24 |
      | id_startdate_month | October |
      | id_startdate_year | 2015 |
      | id_enddate_day | 24 |
      | id_enddate_month | October |
      | id_enddate_year | 2016 |

  Scenario: Create a course as a custom course creator
    Given the following "users" exist:
      | username  | firstname | lastname | email          |
      | kevin  | Kevin   | the        | kevin@example.com |
    And the following "roles" exist:
      | shortname | name    | archetype |
      | creator   | Creator |           |
    And the following "system role assigns" exist:
      | user   | role    | contextlevel |
      | kevin  | creator | System       |
    And I log in as "admin"
    And I set the following system permissions of "Creator" role:
      | capability | permission |
      | moodle/course:create | Allow |
      | moodle/course:manageactivities | Allow |
      | moodle/course:viewparticipants | Allow |
    And I log out
    And I log in as "kevin"
    And I am on site homepage
    When I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | myfirstcourse |
    And I press "Save and display"
    And I follow "Participants"
    Then I should see "My first course"
    And I should see "Participants"

  Scenario: Creators' role in new courses setting behavior
    Given the following "users" exist:
      | username  | firstname | lastname | email          |
      | kevin  | Kevin   | the        | kevin@example.com |
    And the following "system role assigns" exist:
      | user   | role    | contextlevel |
      | kevin  | coursecreator | System       |
    And I log in as "admin"
    And I set the following administration settings values:
      | Creators' role in new courses | Non-editing teacher |
    And I log out
    And I log in as "kevin"
    And I am on site homepage
    When I press "Add a new course"
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | myfirstcourse |
    And I press "Save and display"
    And I click on "Participants" "link"
    Then I should see "Non-editing teacher" in the "Kevin the" "table_row"

  @javascript
  Scenario: Create a course as admin
    Given I log in as "admin"
    And the following config values are set as admin:
      | enroladminnewcourse | 0 |
    And I navigate to "Courses > Add a new course" in site administration
    And I set the following fields to these values:
      | Course full name  | My first course |
      | Course short name | myfirstcourse |
    And I press "Save and display"
    And I navigate to course participants
    Then I should not see "Teacher"
    And I should see "Nothing to display"
    And the following config values are set as admin:
      | enroladminnewcourse | 1 |
    And I navigate to "Courses > Add a new course" in site administration
    And I set the following fields to these values:
      | Course full name  | My second course |
      | Course short name | mysecondcourse |
    And I press "Save and display"
    And I navigate to course participants
    And I should see "Teacher"
    And I should not see "Nothing to display"
