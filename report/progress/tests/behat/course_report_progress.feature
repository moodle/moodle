@report @report_progress
Feature: In a course administration page, navigate through report page, test for selector in activity completion
  In order to view and override a student's activity completion status
  As a teacher
  Go to course administration -> Reports -> Activity completion

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "activities" exist:
      | activity   | name            | intro   | course | idnumber    | section | completion | completionview | completionusegrade | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign     | my assignment   | A1 desc | C1     | assign1     | 0       | 1          | 0              |                    | 0                                   | 0                |
      | assign     | my assignment 2 | A2 desc | C1     | assign2     | 0       | 2          | 1              |                    | 0                                   | 0                |
      | assign     | my assignment 3 | A3 desc | C1     | assign3     | 0       | 2          | 1              | 1                  | 1                                   | 0                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher1@example.com |
      | student1 | Student | One | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Selector should be available in the course acitivity completion page
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then "Report" "field" should exist in the "tertiary-navigation" "region"
    And I should see "Activity completion" in the "tertiary-navigation" "region"

  Scenario: Custom profile fields selected for identity should display on the activity completion report
    Given the following "custom profile fields" exist:
      | datatype | shortname  | name           |
      | text     | frog       | Favourite frog |
    And the following "users" exist:
      | username | firstname | lastname | profile_field_frog |
      | student2 | Student   | Two      | Kermit             |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student2 | C1     | student |
    And the following config values are set as admin:
      | showuseridentity | email,profile_field_frog |
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    Then I should see "Favourite frog"
    Then I should see "Kermit" in the "Student Two" "table_row"
