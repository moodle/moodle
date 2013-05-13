@core @core_cohort @_only_local
Feature: Upload users to a cohort
  In order to quickly fill site-wide groups with users
  As an admin
  I need to upload a file with users data containing cohort assigns

  @javascript
  Scenario: Upload users and assign them to a course with cohort enrolment method enabled
    Given the following "cohorts" exists:
      | name | idnumber |
      | Cohort 1 | ASD |
      | Cohort 2 | DSA |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And I log in as "admin"
    And I follow "Course 1"
    And I add "Cohort sync" enrolment method with:
      | Cohort | Cohort 1 |
    And I am on homepage
    And I follow "Course 2"
    And I add "Cohort sync" enrolment method with:
      | Cohort | Cohort 2 |
    And I collapse "Course administration" node
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    When I follow "Upload users"
    And I upload "lib/tests/fixtures/upload_users_cohorts.csv" file to "File" filepicker
    And I press "Upload users"
    And I press "Upload users"
    And I press "Continue"
    And I follow "Cohorts"
    And I click on "Assign" "link" in the "//table[@id='cohorts']//tr[contains(., 'Cohort 1')]" "xpath_element"
    Then the "Current users" select box should contain "Tom Jones (tomjones@example.com)"
    And the "Current users" select box should contain "Bob Jones (bobjones@example.com)"
    And I press "Back to cohorts"
    And I click on "Assign" "link" in the "//table[@id='cohorts']//tr[contains(., 'Cohort 2')]" "xpath_element"
    And the "Current users" select box should contain "Mary Smith (marysmith@example.com)"
    And the "Current users" select box should contain "Alice Smith (alicesmith@example.com)"
    And I am on homepage
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Enrolled users"
    And I should see "Tom Jones"
    And I should see "Bob Jones"
    And I should not see "Mary Smith"
    And I am on homepage
    And I follow "Course 2"
    And I expand "Users" node
    And I follow "Enrolled users"
    And I should see "Mary Smith"
    And I should see "Alice Smith"
    And I should not see "Tom Jones"
