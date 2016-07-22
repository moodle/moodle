@mod @mod_workshop
@javascript
Feature: Workshop submission export to portfolio
  In order to be able to reuse my submission content
  As a student
  I need to be able to export my submission

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
      | activity | name         | intro                     | course | idnumber  |
      | workshop | TestWorkshop | Test workshop description | c1     | workshop1 |
    # Admin enable portfolio plugin
    And I log in as "admin"
    And I expand "Site administration" node
    And I follow "Advanced features"
    And I set the following administration settings values:
      | Enable portfolios | 1 |
    And I expand "Plugins" node
    And I expand "Portfolios" node
    And I follow "Manage portfolios"
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    And I log out
    # Teacher sets up assessment form and changes the phase to submission.
    And I log in as "teacher1"
    And I follow "Course1"
    And I edit assessment form in workshop "TestWorkshop" as:"
      | id_description__idx_0_editor | Aspect1 |
      | id_description__idx_1_editor | Aspect2 |
      | id_description__idx_2_editor |         |
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    And I log out
    # Student1 submits.
    And I log in as "student1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I add a submission in workshop "TestWorkshop" as:"
      | Title              | Submission1  |
      | Submission content | Some content |
    And I log out
    # Student2 submits.
    And I log in as "student2"
    And I follow "Course1"
    And I add a submission in workshop "TestWorkshop" as:"
      | Title              | Submission2  |
      | Submission content | Some content |
    And I log out
     # teacher1 allocates reviewers and changes the phase to assessment
    And I log in as "teacher1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I should see "to allocate: 2"
    Then I should see "Workshop submissions report"
    And I should see "Submitted (2) / not submitted (0)"
    And I should see "Submission1" in the "Sam1 Student1" "table_row"
    And I should see "Submission2" in the "Sam2 Student2" "table_row"
    And I allocate submissions in workshop "TestWorkshop" as:"
      | Participant   | Reviewer      |
      | Sam1 Student1 | Sam2 Student2 |
      | Sam2 Student2 | Sam1 Student1 |
    And I follow "TestWorkshop"
    And I should see "to allocate: 0"
    And I change phase in workshop "TestWorkshop" to "Assessment phase"
    And I log out


  Scenario: Hide export to portfolio button if admin disable portfolio feature
    Given I log in as "admin"
    And I expand "Site administration" node
    And I follow "Advanced features"
    And I set the following administration settings values:
      | Enable portfolios | 0 |
    And I log out
    When I log in as "student1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I follow "My submission"
    Then I should see "Submission1"
    Then "Export submission to portfolio" "button" should not exist
    And I log out

  Scenario: Students can export to portfolio their own submission
    Given I log in as "student1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    When I follow "My submission"
    Then I should see "Submission1"
    Then "Export submission to portfolio" "button" should exist
    And I click on "Export submission to portfolio" "button"
    Then I should see "Available export formats"
    And I click on "Next" "button"
    Then I should see "Summary of your export"
    And I click on "Continue" "button"
    Then I should see "Return to where you were"
    And I log out

  Scenario: Students can not export to portfolio when viewing submission of other people
    Given I log in as "teacher1"
    And I follow "Course1"
    And I change phase in workshop "TestWorkshop" to "Closed"
    And I log out
    When I log in as "student2"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I follow "Submission1"
    Then "Export submission to portfolio" "button" should not exist
    And I log out
