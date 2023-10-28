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
    And the following config values are set as admin:
      | downloadcoursecontentallowed | 1 |

  Scenario: Site admins can remove the download course content feature
    Given I log in as "admin"
    And I am on "Hockey 101" course homepage
    And I navigate to "Settings" in current page administration
    And I set the field "Enable download course content" to "Yes"
    And I press "Save and display"
    Then "Download course content" "link" should exist in current page administration
    When the following config values are set as admin:
      | downloadcoursecontentallowed | 0 |
    And I am on "Hockey 101" course homepage
    Then "Download course content" "link" should not exist in current page administration
    And I navigate to "Settings" in current page administration
    And I should not see "Enable download course content"

  Scenario: Site admins can set the default value for whether download course content is enabled in courses
    Given I log in as "admin"
    And I am on "Hockey 101" course homepage
    And "Download course content" "link" should not exist in current page administration
    When I navigate to "Courses > Course default settings" in site administration
    And I set the field "Enable download course content" to "Yes"
    And I press "Save changes"
    And I am on "Hockey 101" course homepage
    Then "Download course content" "link" should exist in current page administration

  Scenario: A teacher can enable and disable the download course content feature when it is available
    Given I log in as "teacher1"
    When I am on "Hockey 101" course homepage
    And "Download course content" "link" should not exist in current page administration
    And I navigate to "Settings" in current page administration
    And I should see "Enable download course content"
    And I set the field "Enable download course content" to "Yes"
    And I press "Save and display"
    Then "Download course content" "link" should exist in current page administration
    And I navigate to "Settings" in current page administration
    And I set the field "Enable download course content" to "No"
    And I press "Save and display"
    Then "Download course content" "link" should not exist in current page administration

  Scenario: Teachers require a capability to access the download course content feature or modify its availability in a course
    Given the following config values are set as admin:
      | config                     | value | plugin       |
      | downloadcontentsitedefault | 1     | moodlecourse |
    # Check teacher can see download option and enable dropdown.
    And I log in as "teacher1"
    And I am on "Hockey 101" course homepage
    Then "Download course content" "link" should exist in current page administration
    And I navigate to "Settings" in current page administration
    And "Enable download course content" "select" should exist
    # Remove teacher's capabilities for download course content.
    And the following "role capability" exists:
      | role                                   | editingteacher |
      | moodle/course:downloadcoursecontent    | prohibit       |
      | moodle/course:configuredownloadcontent | prohibit       |
    # Check teacher can no longer see download option, and that enable value is visible, but dropdown no longer available.
    When I log in as "teacher1"
    And I am on "Hockey 101" course homepage
    Then "Download course content" "link" should not exist in current page administration
    And I navigate to "Settings" in current page administration
    And I should see "Enable download course content"
    And I should see "Site default (Yes)"
    And "Enable download course content" "select" should not exist

  Scenario: Students require a capability to access the download course content feature in a course
    Given I log in as "teacher1"
    And I am on "Hockey 101" course homepage
    And I navigate to "Settings" in current page administration
    And I set the field "Enable download course content" to "Yes"
    And I press "Save and display"
    And I log out
    # Check student can see the download link.
    And I log in as "student1"
    And I am on "Hockey 101" course homepage
    And "Download course content" "link" should exist in current page administration
    # Remove student's capability for download course content.
    When the following "role capability" exists:
      | role                                   | student  |
      | moodle/course:downloadcoursecontent    | prohibit |
    # Check student can no longer see the download link.
    And I log in as "student1"
    And I am on "Hockey 101" course homepage
    Then "Download course content" "link" should not exist in current page administration
