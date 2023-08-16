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
      | student2 | Student | 2 | student2@example.com | 'Bill'&"Ben"<tag>Hello</tag> |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | course | idnumber | name                 |
      | assign   | C1     | a1       | Test assignment name |
    And the following "grade grades" exist:
      | gradeitem            | user     | grade |
      | Test assignment name | student1 | 80.00 |
      | Test assignment name | student2 | 42.00 |
    And I am on the "Course 1" course page logged in as teacher1

  @javascript
  Scenario: Export grades as XML
    When I navigate to "XML file" export page in the course gradebook
    And I expand all fieldsets
    And I set the field "Grade export decimal places" to "1"
    And I press "Download"
    Then I should see "s1" in the "//results//result[1]//student" "xpath_element"
    And I should see "a1" in the "//results//result[1]//assignment" "xpath_element"
    And I should see "80.0" in the "//results//result[1]//score" "xpath_element"
    And I should not see "80.00" in the "//results//result[1]//score" "xpath_element"
    # Ensure we have the encoded ID number of student 2.
    And I should see "'Bill'&\"Ben\"<tag>Hello</tag>" in the "//results//result[2]//student" "xpath_element"
    And I should see "a1" in the "//results//result[2]//assignment" "xpath_element"
    And I should see "42.0" in the "//results//result[2]//score" "xpath_element"
    And I should not see "42.00" in the "//results//result[2]//score" "xpath_element"
