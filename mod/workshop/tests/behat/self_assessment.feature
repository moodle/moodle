@mod @mod_workshop @javascript
Feature: Workshop self-assessment
  In order to use workshop activity
  As a student
  I need to be able to add and assess my own submission

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | student1 | Sam1      | Student1 | student1@example.com |
      | student2 | Sam2      | Student2 | student2@example.com |
      | student3 | Sam3      | Student3 | student3@example.com |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course1   | c1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | c1     | student        |
      | student2 | c1     | student        |
      | student3 | c1     | student        |
      | teacher1 | c1     | editingteacher |
    And the following "activities" exist:
      | activity | name         | course | idnumber  | useselfassessment |
      | workshop | TestWorkshop | c1     | workshop1 | 1                 |
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I change phase in workshop "TestWorkshop" to "Submission phase"
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I add a submission in workshop "TestWorkshop" as:"
      | Title              | Submission1  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as student2
    And I add a submission in workshop "TestWorkshop" as:"
      | Title              | Submission2  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as student3
    And I add a submission in workshop "TestWorkshop" as:"
      | Title              | Submission3  |
      | Submission content | Some content |
    And I am on the "TestWorkshop" "workshop activity" page logged in as teacher1
    And I click on "Submissions allocation" "link"
    And I select "Random allocation" from the "jump" singleselect
    And I set the following fields to these values:
      | addselfassessment | 1 |
    And I press "Save changes"

  Scenario: Student can assess their own submission
    When I select "Manual allocation" from the "jump" singleselect
    Then the "by" select box should contain "Sam1 Student1"
    And the "by" select box should contain "Sam2 Student2"
    And the "by" select box should contain "Sam3 Student3"
    And I should see "Sam1 Student1" in the "Sam1 Student1" "table_row"
    And I should see "Sam2 Student2" in the "Sam2 Student2" "table_row"
    And I should see "Sam3 Student3" in the "Sam3 Student3" "table_row"
    # Then the following should exist in the "allocations" table:
    #  | Participant is reviewed by | Participant   | Participant is reviewer of |
    #  | Sam1 Student1              | Sam1 Student1 | Sam1 Student1              |
    #  | Sam2 Student2              | Sam2 Student2 | Sam2 Student2              |
    #  | Sam3 Student3              | Sam3 Student3 | Sam3 Student3              |
    And I change phase in workshop "TestWorkshop" to "Assessment phase"
    And I am on the "TestWorkshop" "workshop activity" page logged in as student1
    And I should see "Assess yourself"
    And I should see "Your submission"
    And I should see "Assigned submissions to assess"
    And I should see "Submission1"
    And I should see "by Sam1 Student1"
    And the "Assess" "button" should be enabled
    And I am on the "TestWorkshop" "workshop activity" page logged in as student2
    And I should see "Assess yourself"
    And I should see "Your submission"
    And I should see "Assigned submissions to assess"
    And I should see "Submission2"
    And I should see "by Sam2 Student2"
    And the "Assess" "button" should be enabled
    And I am on the "TestWorkshop" "workshop activity" page logged in as student3
    And I should see "Assess yourself"
    And I should see "Your submission"
    And I should see "Assigned submissions to assess"
    And I should see "Submission3"
    And I should see "by Sam3 Student3"
    And the "Assess" "button" should be enabled
