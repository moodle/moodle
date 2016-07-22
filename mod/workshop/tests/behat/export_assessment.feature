@mod @mod_workshop
@javascript

Feature: Workshop submission's assessment export to portfolio
  In order to be able to reuse my assessment content in my submission
  As a student
  I need to be able to export my own submission's assessment from other people

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | student1 | Sam1      | Student1 | student1@example.com  |
      | student2 | Sam2      | Student2 | student2@example.com  |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com  |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c2        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c2     | student        |
      | student2 | c2     | student        |
      | teacher1 | c2     | editingteacher |
    And the following "activities" exist:
      | activity | name         | intro                     | course | idnumber  |
      | workshop | TestWorkshop | Test workshop description | c2     | workshop1 |
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
    And I allocate submissions in workshop "TestWorkshop" as:"
      | Participant   | Reviewer      |
      | Sam1 Student1 | Sam2 Student2 |
      | Sam2 Student2 | Sam1 Student1 |
    And I follow "TestWorkshop"
    And I change phase in workshop "TestWorkshop" to "Assessment phase"
    And I log out
    # student1 assesses work of student2
    And I log in as "student1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I assess submission "Sam2" in workshop "TestWorkshop" as:"
      | grade__idx_0            | 5 / 10            |
      | peercomment__idx_0      | You can do better |
      | grade__idx_1            | 10 / 10           |
      | peercomment__idx_1      | Amazing           |
      | Feedback for the author | Good work         |
    And I log out
    # student2 assesses work of student1
    And I log in as "student2"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I assess submission "Sam1" in workshop "TestWorkshop" as:"
      | grade__idx_0            | 6 / 10     |
      | peercomment__idx_0      |            |
      | grade__idx_1            | 7 / 10     |
      | peercomment__idx_1      |            |
      | Feedback for the author | Keep it up |
    And I log out

  Scenario: Hide export to portfolio button of assessment if admin disable portfolio feature
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
    Then "Export assessment to portfolio" "button" should not exist
    And I log out

  Scenario: Students can export to portfolio their own submission's assessments
    Given I log in as "teacher1"
    And I follow "Course1"
    And I change phase in workshop "TestWorkshop" to "Closed"
    And I log out
    When I log in as "student1"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I follow "My submission"
    Then I should see "Submission1"
    Then "Export assessment to portfolio" "button" should exist
    And I click on "Export assessment to portfolio" "button"
    Then I should see "Available export formats"
    And I click on "Next" "button"
    Then I should see "Summary of your export"
    And I click on "Continue" "button"
    Then I should see "Return to where you were"
    And I log out

  Scenario: Students can not export to portfolio the assessment content when viewing submission of other people
    Given I log in as "teacher1"
    And I follow "Course1"
    And I change phase in workshop "TestWorkshop" to "Closed"
    And I log out
    When I log in as "student2"
    And I follow "Course1"
    And I follow "TestWorkshop"
    And I follow "Submission1"
    Then I should see "Assessment"
    Then "Export assessment to portfolio" "button" should not exist
    And I log out
