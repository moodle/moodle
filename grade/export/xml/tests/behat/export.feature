@gradeexport @gradeexport_xml
Feature: I need to export grades as xml
  In order to easily review marks
  As a teacher
  I need to have a export grades as xml

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | t1 |
      | student1 | Student | 1 | student1@example.com | s1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name | intro |
      | assign | C1 | a1 | Test assignment name | Submit something! |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment name"
    And I press "Save changes"

  @javascript
  Scenario: Export grades as text
    When I navigate to "Export > XML file" in the course gradebook
    And I expand all fieldsets
    And I set the field "Grade export decimal points" to "1"
    And I press "Download"
    Then I should see "s1"
    And I should see "a1"
    And I should see "80.0"
    And I should not see "80.00"
