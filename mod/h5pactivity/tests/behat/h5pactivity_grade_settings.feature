@mod @mod_h5pactivity @core_h5p
Feature: Teacher can control h5p activity grading setting
  In order to set h5p activity grade
  As a teacher
  I need to be able to control h5p activity grading setting

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | student4 | Student   | 4        | student4@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |

  @javascript
  Scenario: Verify that invalid grades given to students are not saved
    Given the following "activities" exist:
      | activity    | name      | course |
      | h5pactivity | H5P point | C1     |
    # Activity grade settings are not saved using generators so manual setting is necessary.
    And I am on the "H5P point" "h5pactivity activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | grade[modgrade_type]  | Point |
      | grade[modgrade_point] | 10    |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    When I give the grade "50" to the user "Student 1" for the grade item "H5P point"
    And I press "Save changes"
    And I turn editing mode off
    # Confirm that grades are not saved when grade entered is > maximum grade.
    Then the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3- |
      | Student 1 | student1@example.com | -   |
      | Student 2 | student2@example.com | -   |
      | Student 3 | student3@example.com | -   |
      | Student 4 | student4@example.com | -   |

  @javascript
  Scenario: Verify that valid grades given to students are saved
    Given the following "activities" exist:
      | activity    | name      | course |
      | h5pactivity | H5P point | C1     |
    # Activity grade settings are not saved using generators so manual setting is necessary.
    And I am on the "H5P point" "h5pactivity activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | grade[modgrade_type]  | Point |
      | grade[modgrade_point] | 10    |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    When I give the grade "10" to the user "Student 1" for the grade item "H5P point"
    And I give the grade "5" to the user "Student 2" for the grade item "H5P point"
    And I give the grade "0" to the user "Student 3" for the grade item "H5P point"
    And I press "Save changes"
    And I turn editing mode off
    # Confirm that corresponding grades are stored for each student.
    And the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3- |
      | Student 1 | student1@example.com | 10  |
      | Student 2 | student2@example.com | 5   |
      | Student 3 | student3@example.com | 0   |
      | Student 4 | student4@example.com | -   |

  @javascript
  Scenario: Verify that scales given to students are saved
    Given the following "activities" exist:
      | activity    | name      | course |
      | h5pactivity | H5P scale | C1     |
    # Activity grade settings are not saved using generators so manual setting is necessary.
    And I am on the "H5P scale" "h5pactivity activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | grade[modgrade_type]  | Scale                    |
      | grade[modgrade_scale] | Default competence scale |
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "Not yet competent" to the user "Student 1" for the grade item "H5P scale"
    And I give the grade "Competent" to the user "Student 2" for the grade item "H5P scale"
    And I give the grade "Competent" to the user "Student 4" for the grade item "H5P scale"
    When I press "Save changes"
    And I turn editing mode off
    # Confirm that scale set for student is successfully saved.
    Then the following should exist in the "user-grades" table:
      | -1-       | -2-                  | -3-               |
      | Student 1 | student1@example.com | Not yet competent |
      | Student 2 | student2@example.com | Competent         |
      | Student 3 | student3@example.com | -                 |
      | Student 4 | student4@example.com | Competent         |
