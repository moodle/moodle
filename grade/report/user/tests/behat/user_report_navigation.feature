@core @core_grades @gradereport_user @javascript
Feature: Teacher can navigate to the previous or next user report.
  In order to get go the previous or next user report
  As a teacher
  I need to click on the previous/next navigation links

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course   | C1        | 0        |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher   | 1         | teacher1@example.com  | t1        |
      | student1  | Student   | 1         | student1@example.com  | s1        |
      | student2  | Student   | 2         | student2@example.com  | s2        |
      | student3  | Student   | 3         | student3@example.com  | s3        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
      | student3  | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                | grade |
      | assign   | C1     | a1       | Test assignment one | 300   |
    And I am on the "Course" "grades > User report > View" page logged in as "teacher1"

  Scenario: A teacher can navigate to the next user report
    Given I click on "Student 1" in the "user" search widget
    And I should see "Student 1" in the ".user-heading" "css_element"
    And ".previous" "css_element" should not exist in the ".user-navigation" "css_element"
    And ".next" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 2" in the ".next" "css_element"
    When I click on "Student 2" "link" in the ".next" "css_element"
    Then I should see "Student 2" in the ".user-heading" "css_element"
    And ".previous" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 1" in the ".previous" "css_element"
    And ".next" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 3" in the ".next" "css_element"
    And I click on "Student 3" "link" in the ".next" "css_element"
    And I should see "Student 3" in the ".user-heading" "css_element"
    And ".previous" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 2" in the ".previous" "css_element"
    And ".next" "css_element" should not exist in the ".user-navigation" "css_element"

  Scenario: A teacher can navigate to the previous user report
    Given I click on "Student 3" in the "user" search widget
    And I should see "Student 3" in the ".user-heading" "css_element"
    And ".previous" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 2" in the ".previous" "css_element"
    And ".next" "css_element" should not exist in the ".user-navigation" "css_element"
    When I click on "Student 2" "link" in the ".previous" "css_element"
    Then I should see "Student 2" in the ".user-heading" "css_element"
    And ".previous" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 1" in the ".previous" "css_element"
    And ".next" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 3" in the ".next" "css_element"
    And I click on "Student 1" "link" in the ".previous" "css_element"
    And I should see "Student 1" in the ".user-heading" "css_element"
    And ".previous" "css_element" should not exist in the ".user-navigation" "css_element"
    And ".next" "css_element" should exist in the ".user-navigation" "css_element"
    And I should see "Student 2" in the ".next" "css_element"
