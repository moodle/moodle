@tool @tool_uploaduser @_file_upload
Feature: Admin can suspend user course enrolment via CSV upload
  In order to manage enrolments in bulk
  As an administrator
  I need to be able to enrol and suspend users using CSV upload

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
      | Course 3 | C3        |

  @javascript
  Scenario: Admin uploads enrol and suspend CSVs and verifies enrolment status
    Given I log in as "admin"
    And I navigate to "Users > Accounts > Upload users" in site administration
    When I upload "lib/tests/fixtures/QA_user_enrol.txt" file to "File" filemanager
    And I press "Upload users"
    And I press "Upload users"
    And I press "Continue"
    And I upload "lib/tests/fixtures/QA_user_suspend.txt" file to "File" filemanager
    And I press "Upload users"
    And I set the field "Upload type" to "Update existing users only"
    And I press "Upload users"
    And I press "Continue"
    And I am on the "Course 1" "enrolled users" page
    Then the following should exist in the "participants" table:
      | First name  | Status |
      | Learner One | Active |
      | Learner Two | Active |
    And I am on the "Course 2" "enrolled users" page
    And the following should exist in the "participants" table:
      | First name  | Status    |
      | Learner One | Active    |
      | Learner Two | Suspended |
    And I am on the "Course 3" "enrolled users" page
    And the following should exist in the "participants" table:
      | First name  | Status    |
      | Learner One | Suspended |
      | Learner Two | Active    |
