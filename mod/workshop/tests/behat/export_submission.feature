@mod @mod_workshop
Feature: Exporting workshop submissions and assessments to a portfolio
  In order to archive my workshop contribution in a personal storage
  As a student or as a teacher
  I need to be able to export a workshop submission and its associated assessments

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | student1 | Sam1      | Student1 | student1@example.com  |
      | student2 | Sam2      | Student2 | student2@example.com  |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com  |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | student2 | c1     | student        |
      | teacher1 | c1     | editingteacher |
    And the following "activities" exist:
      | activity | name         | course | idnumber  | submissiontypefile |
      | workshop | TestWorkshop | c1     | workshop1 | 1                  |
    # Admin needs to enable portfolio API and set a portfolio instance first.
    And I log in as "admin"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    # Teacher sets up assessment form and changes the phase to submission.
    And I am on the "Course1" course page logged in as teacher1
    And I edit assessment form in workshop "TestWorkshop" as:
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    # Student1 submits.
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission1  |
      | Submission content | Some content |
    # Student2 submits.
    And I am on the "Course1" course page logged in as student2
    And I add a submission in workshop "TestWorkshop" as:
      | Title              | Submission2  |
      | Submission content | Some content |
     # Teacher allocates reviewers and changes the phase to assessment.
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I should see "to allocate: 2"
    And I should see "Workshop submissions report"
    And I should see "Submitted (2) / not submitted (0)"
    And I should see "Submission1" in the "Sam1 Student1" "table_row"
    And I should see "Submission2" in the "Sam2 Student2" "table_row"
    And I allocate submissions in workshop "TestWorkshop" as:
      | Participant   | Reviewer      |
      | Sam1 Student1 | Sam2 Student2 |
      | Sam2 Student2 | Sam1 Student1 |
    And I follow "TestWorkshop"
    And I should see "to allocate: 0"
    And I change phase in workshop "TestWorkshop" to "Assessment phase"

  Scenario: Students can export their own submission to a portfolio.
    Given I am on the "TestWorkshop" "workshop activity" page logged in as student1
    When I follow "Submission1"
    Then I should see "Submission1"
    And "Export this page" "button" should exist
    And I click on "Export this page" "button"
    And I should see "Available export formats"
    And I click on "Next" "button"
    And I should see "Summary of your export"
    And I click on "Continue" "button"
    And I should see "Return to where you were"

  Scenario: Students can export submission they have peer-assessed.
    Given I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I should see "Submission2"
    And I follow "Submission2"
    And "Export this page" "button" should exist
    When I click on "Export this page" "button"
    Then I should see "Available export formats"
    And I click on "Next" "button"
    And I should see "Summary of your export"
    And I click on "Continue" "button"
    And I should see "Return to where you were"

  Scenario: If the portfolio API is disabled, the portfolio export button is not displayed.
    Given the following config values are set as admin:
      | enableportfolios | 0 |
    When I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I follow "Submission1"
    Then I should see "Submission1"
    And "Export this page" "button" should not exist
