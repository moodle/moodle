@mod @mod_h5pactivity @core_h5p
Feature: Reporting information in the h5p activity
  In order to let teachers view a report on the attempts made in the h5p activity
  As a teacher
  I can access the report page and see the attempts made by the students

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    # This test is only about reporting, we don't need to specify any valid H5P file for it.
    And the following "activity" exists:
      | activity | h5pactivity          |
      | name     | H5P package          |
      | intro    | Test H5P description |
      | course   | C1                   |
      | idnumber | h5ppackage           |
    And the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      | student1 | H5P package | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
      | student2 | H5P package | 1       | choice          | 2        | 2        | 4        | 1          | 1       |
      | student1 | H5P package | 2       | compound        | 2        | 2        | 4        | 1          | 1       |

  Scenario: Access the report page and check the label for the attempts and attempt headers
    Given I am on the "H5P package" "h5pactivity activity" page logged in as teacher1
    When I navigate to "Attempts report" in current page administration
    Then I should see "View (2)" in the "Student 1" "table_row"
    And I should see "View (1)" in the "Student 2" "table_row"
    And I should see "Attempts (3)" in the "table" "table"
