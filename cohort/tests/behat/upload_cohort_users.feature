@core @core_cohort @_file_upload
Feature: Upload users to a cohort
  In order to quickly fill site-wide groups with users
  As an admin
  I need to upload a file with users data containing cohort assigns

  @javascript @skip_chrome_zerosize
  Scenario: Upload users and assign them to a course with cohort enrolment method enabled
    Given the following "cohorts" exist:
      | name | idnumber |
      | Cohort 1 | ASD |
      | Cohort 2 | DSA |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And I log in as "admin"
    And I add "Cohort sync" enrolment method in "Course 1" with:
      | Cohort | Cohort 1 |
    And I should see "Cohort sync (Cohort 1 - Student)"
    And I add "Cohort sync" enrolment method in "Course 2" with:
      | Cohort | Cohort 2 |
    And I should see "Cohort sync (Cohort 2 - Student)"
    When I navigate to "Users > Accounts > Upload users" in site administration
    And I upload "lib/tests/fixtures/upload_users_cohorts.csv" file to "File" filemanager
    And I press "Upload users"
    And I press "Upload users"
    And I press "Continue"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I press "Assign" action in the "Cohort 1" report row
    Then the "Current users" select box should contain "Tom Jones (tomjones@example.com)"
    And the "Current users" select box should contain "Bob Jones (bobjones@example.com)"
    And I press "Back to cohorts"
    And I press "Assign" action in the "Cohort 2" report row
    And the "Current users" select box should contain "Mary Smith (marysmith@example.com)"
    And the "Current users" select box should contain "Alice Smith (alicesmith@example.com)"
    And I am on the "Course 1" "enrolled users" page
    And I should see "Tom Jones"
    And I should see "Bob Jones"
    And I should not see "Mary Smith"
    And I am on the "Course 2" "enrolled users" page
    And I should see "Mary Smith"
    And I should see "Alice Smith"
    And I should not see "Tom Jones"
