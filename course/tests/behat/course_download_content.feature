@core @core_course
Feature: Course content can be downloaded
  In order to retain a backup offline copy of course activity/resource data
  As a user
  I can download a course's content

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
    And I navigate to "Courses > Download course content" in site administration
    And I set the following fields to these values:
    | Download course content feature available | 1 |
    And I press "Save changes"
    And I navigate to "Courses > Course default settings" in site administration
    And I set the field "Enable download course content" to "Yes"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: A student can download course content when the feature is enabled in their course
    Given I log in as "student1"
    When I am on "Hockey 101" course homepage
    And I navigate to "Download course content" in current page administration
    Then I should see "You are about to download a zip file"
    # Without the ability to check the downloaded file, the absence of an exception being thrown here is considered a success.
    And I click on "Download" "button" in the "Download course content" "dialogue"

  @javascript
  Scenario: A teacher can download course content when the feature is enabled in their course
    Given I log in as "teacher1"
    When I am on "Hockey 101" course homepage
    And "Download course content" "link" should exist in current page administration
    And I navigate to "Download course content" in current page administration
    Then I should see "You are about to download a zip file"
    # Without the ability to check the downloaded file, the absence of an exception being thrown here is considered a success.
    And I click on "Download" "button" in the "Download course content" "dialogue"
