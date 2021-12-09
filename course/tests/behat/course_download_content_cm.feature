@core @core_course
Feature: Activities content download can be controlled
  In order to allow or restrict access to download activity content
  As a teacher
  I can disable the content download of an activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | manager1 | C1     | manager        |
    And the following "activities" exist:
      | activity | name    | intro      | introformat | course |
      | page     | Page1  | PageDesc1   | 1           | C1     |
    And the following "activities" exist:
      | activity | name      | intro        | introformat | course | downloadcontent |
      | folder   | Folder1   | FolderDesc1  | 1           | C1     | 0               |
    And I log in as "admin"
    And the following config values are set as admin:
      | downloadcoursecontentallowed | 1 |
    And I log out

  Scenario: "Include in course content download" field default is set to "Yes" if nothing has been set
    Given I am on the Page1 "Page Activity editing" page logged in as admin
    Then the field "Include in course content download" matches value "Yes"

  Scenario: "Include in course content download" field is not visible if course content is disabled on site level
    Given I log in as "admin"
    And the following config values are set as admin:
      | downloadcoursecontentallowed | 0 |
    And I am on the Page1 "Page Activity editing" page
    Then "Include in course content download" "select" should not exist

  Scenario: "Include in course content download" field is visible even if course content is disabled on course level
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    When I set the field "Enable download course content" to "No"
    And I press "Save and display"
    And I am on the Page1 "Page Activity editing" page
    Then "Include in course content download" "select" should exist

  Scenario: "Include in course content download" field should be visible but not editable for users without configuredownloadcontent capability
    Given I log in as "manager1"
    And I am on the Folder1 "Folder Activity editing" page
    And "Include in course content download" "field" should exist
    And I log out
    And I log in as "admin"
    When I set the following system permissions of "Manager" role:
      | capability                             | permission |
      | moodle/course:configuredownloadcontent | Prohibit   |
    And I log out
    And I log in as "manager1"
    And I am on the Folder1 "Folder Activity editing" page
    Then I should see "Include in course content download"
    And I should see "No"
    And "Include in course content download" "select" should not exist
