@core @core_course
Feature: Access to downloading course content can be controlled
  In order to allow or restrict access to download course content
  As a trusted user
  I can control access to the download course content feature

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname   | shortname |
      | Hockey 101 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "admin"
    And the following config values are set as admin:
    | downloadcoursecontentallowed | 1 |
    And I log out

Scenario: Site admins can remove the download course content feature
  Given I log in as "admin"
  And I am on "Hockey 101" course homepage
  And I navigate to "Edit settings" in current page administration
  And I set the field "Enable download course content" to "Yes"
  And I press "Save and display"
  And "Download course content" "link" should exist in current page administration
  When the following config values are set as admin:
    | downloadcoursecontentallowed | 0 |
  And I am on "Hockey 101" course homepage
  Then "Download course content" "link" should not exist in current page administration
  And I navigate to "Edit settings" in current page administration
  And I should not see "Enable download course content"

Scenario: Site admins can set the default value for whether download course content is enabled in courses
  Given I log in as "admin"
  And I am on "Hockey 101" course homepage
  And "Download course content" "link" should not exist in current page administration
  When I navigate to "Courses > Courses > Course default settings" in site administration
  And I set the field "Enable download course content" to "Yes"
  And I press "Save changes"
  And I am on "Hockey 101" course homepage
  Then "Download course content" "link" should exist in current page administration

Scenario: A teacher can enable and disable the download course content feature when it is available
  Given I log in as "teacher1"
  When I am on "Hockey 101" course homepage
  And "Download course content" "link" should not exist in current page administration
  And I navigate to "Edit settings" in current page administration
  And I should see "Enable download course content"
  And I set the field "Enable download course content" to "Yes"
  And I press "Save and display"
  Then "Download course content" "link" should exist in current page administration
  And I navigate to "Edit settings" in current page administration
  And I set the field "Enable download course content" to "No"
  And I press "Save and display"
  And "Download course content" "link" should not exist in current page administration

Scenario: Teachers require a capability to access the download course content feature or modify its availability in a course
  Given I log in as "admin"
  And I navigate to "Courses > Courses > Course default settings" in site administration
  And I set the field "Enable download course content" to "Yes"
  And I press "Save changes"
  And I log out
  # Check teacher can see download option and enable dropdown.
  And I log in as "teacher1"
  And I am on "Hockey 101" course homepage
  And "Download course content" "link" should exist in current page administration
  And I navigate to "Edit settings" in current page administration
  And "Enable download course content" "select" should exist
  And I log out
  # Remove teacher's capabilities for download course content.
  And I log in as "admin"
  And I set the following system permissions of "Teacher" role:
    | capability                             | permission |
    | moodle/course:downloadcoursecontent    | Prohibit   |
    | moodle/course:configuredownloadcontent | Prohibit   |
  And I log out
  # Check teacher can no longer see download option, and that enable value is visible, but dropdown no longer available.
  When I log in as "teacher1"
  And I am on "Hockey 101" course homepage
  Then "Download course content" "link" should not exist in current page administration
  And I navigate to "Edit settings" in current page administration
  And I should see "Enable download course content"
  And I should see "Site default (Yes)"
  And "Enable download course content" "select" should not exist

Scenario: Students require a capability to access the download course content feature in a course
  Given I log in as "teacher1"
  And I am on "Hockey 101" course homepage
  And I navigate to "Edit settings" in current page administration
  And I set the field "Enable download course content" to "Yes"
  And I press "Save and display"
  And I log out
  # Check student can see download button.
  And I log in as "student1"
  And I am on "Hockey 101" course homepage
  And "Download course content" "button" should exist
  And I log out
  And I log in as "admin"
  # Remove student's capability for download course content.
  When I set the following system permissions of "Student" role:
    | capability                             | permission |
    | moodle/course:downloadcoursecontent    | Prohibit   |
  And I log out
  # Check student can no longer see download button.
  And I log in as "student1"
  And I am on "Hockey 101" course homepage
  Then "Download course content" "link" should not exist in current page administration
