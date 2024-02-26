@core @core_course
Feature: Restrict activities availability
  In order to prevent the use of some activities
  As an admin
  I need to control which activities can be used in courses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1 | 0 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | name             |
      | assign   | C1     | Test assign name |

  Scenario: Activities can be added with the default permissions
    Given I log in as "teacher1"
    When I add a glossary activity to course "Course 1" section "1" and I fill the form with:
      | Name | Test glossary name |
      | Description | Test glossary description |
    Then I should see "Test glossary name"
    And I should see "Test assign name"

  @javascript @skip_chrome_zerosize
  Scenario: Activities can not be added when the admin restricts the permissions
    Given the following "role capability" exists:
      | role                 | editingteacher  |
      | mod/assign:addinstance | prohibit        |
    And I log in as "admin"
    And I am on the "Course 1" "permissions" page
    And I override the system permissions of "Teacher" role with:
      | mod/glossary:addinstance | Prohibit |
    And I log out
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    Then "Add a new Assignment" "link" should not exist in the "Add an activity or resource" "dialogue"
    Then "Add a new Glossary" "link" should not exist in the "Add an activity or resource" "dialogue"
