@report @report_aiusage @core_ai
Feature: Course-level AI usage report
  In order to review AI usage within my course
  As a teacher or student
  I need to be able to view a course-scoped AI usage report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username  | firstname | lastname | email                  |
      | teacher1  | Teacher   | One      | teacher1@example.com   |
      | nedteach1 | Nonedit   | Teacher  | nedteach1@example.com  |
      | student1  | Student   | One      | student1@example.com   |
      | student2  | Student   | Two      | student2@example.com   |
      | other1    | Other     | One      | other1@example.com     |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | nedteach1 | C1     | teacher        |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
    And the following "core_ai > ai providers" exist:
      | provider          | name            | enabled | apikey | orgid |
      | aiprovider_openai | OpenAI API test | 1       | 123    | abc   |
    And the following "core_ai > ai actions" exist:
      | actionname    | user     | success | provider          | course |
      | generate_text | student1 | 1       | aiprovider_openai | C1     |
      | generate_text | student2 | 1       | aiprovider_openai | C1     |

  Scenario: An editing teacher can view AI usage for every student in the course
    Given I am logged in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "AI usage" "link"
    Then the following should exist in the "AI usage" table:
      | Action        | First name | Provider            | Success |
      | Generate text | Student    | OpenAI API provider | Yes     |
    And I should see "Student One" in the "AI usage" "table"
    And I should see "Student Two" in the "AI usage" "table"

  Scenario: A non-editing teacher can view AI usage for every student in the course
    Given I am logged in as "nedteach1"
    And I am on "Course 1" course homepage
    When I navigate to "Reports" in current page administration
    And I click on "AI usage" "link"
    Then I should see "Student One" in the "AI usage" "table"
    And I should see "Student Two" in the "AI usage" "table"

  Scenario: A student can view only their own AI usage in the course
    Given I am logged in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I click on "AI usage" "link"
    Then I should see "Student One" in the "AI usage" "table"
    And I should not see "Student Two" in the "AI usage" "table"

  Scenario: A student can view the full detail of their own AI usage
    Given I am logged in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I click on "AI usage" "link"
    And I click on "View detail" "link" in the "Generate text" "table_row"
    Then I should see "AI action detail"
    And I should see "Prompt text"
    And I should see "Your generated content"

  Scenario: A user without either capability cannot access the course report
    Given the following "permission overrides" exist:
      | capability             | permission | role    | contextlevel | reference |
      | report/aiusage:viewown | Prevent    | student | Course       | C1        |
    And the following "course enrolments" exist:
      | user   | course | role    |
      | other1 | C1     | student |
    And I am logged in as "other1"
    And I am on "Course 1" course homepage
    When I follow "Profile" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    Then I should not see "AI usage"
    When I am on the "C1" "report_aiusage > Course report" page
    Then I should see "You do not have permission to view the AI usage report for this course."
