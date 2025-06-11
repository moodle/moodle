@mod @mod_forum @core_grades @javascript
Feature: I can grade a students interaction across a forum
  In order to assess a student's contributions
  As a teacher
  I can assign grades to a student based on their contributions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1 | weeks | 5 |
    And the following "grade categories" exist:
      | fullname | course |
      | Tutor | C1 |
      | Peers | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "scales" exist:
      | name | scale |
      | Test Scale 1 | Disappointing, Good, Very good, Excellent |
    And the following "activity" exists:
      | activity    | forum        |
      | course      | C1           |
      | idnumber    | 0001         |
      | name        | Test Forum 1 |
    And I log in as "teacher1"
    And I change window size to "large"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Ensure that forum grade settings do not leak to Ratings
    Given I am on the "Test Forum 1" "forum activity editing" page
    And I expand all fieldsets

    # Fields should be hidden when grading is not set.
    When I set the field "Whole forum grading > Type" to "None"
    Then "Whole forum grading > Grade to pass" "field" should not be visible
    And "Whole forum grading > Grade category" "field" should not be visible
    And "Whole forum grading > Maximum grade" "field" should not be visible
    And "Ratings > Grade to pass" "field" should not be visible
    And "Ratings > Grade category" "field" should not be visible
    And "Ratings > Maximum grade" "field" should not be visible

    # Only Whole forum grading fields should be visible.
    When I set the field "Whole forum grading > Type" to "Point"
    Then "Whole forum grading > Grade to pass" "field" should be visible
    And "Whole forum grading > Grade category" "field" should be visible
    And "Whole forum grading > Maximum grade" "field" should be visible
    But "Ratings > Grade to pass" "field" should not be visible
    And "Ratings > Grade category" "field" should not be visible
    And "Ratings > Maximum grade" "field" should not be visible

    # Save some values.
    Given I set the field "Whole forum grading > Maximum grade" to "10"
    And I set the field "Whole forum grading > Grade category" to "Tutor"
    And I set the field "Whole forum grading > Grade to pass" to "4"
    When I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook

    # There shouldn't be any Ratings grade item.
    Then I should see "Test Forum 1 whole forum"
    But I should not see "Test Forum 1 rating"

    # The values saved should be reflected here.
    And I click on grade item menu "Test Forum 1 whole forum" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the field "Maximum grade" matches value "10"
    Then the field "Grade to pass" matches value "4"
    And I should see "Tutor" in the "Parent category" "fieldset"

  Scenario: Ensure that Ratings settings do not leak to Forum grading
    Given I am on the "Test Forum 1" "forum activity editing" page
    And I expand all fieldsets

    # Fields should be hidden when grading is not set.
    When I set the field "Ratings > Aggregate type" to "No ratings"
    Then "Ratings > Type" "field" should not be visible
    And "Ratings > Grade to pass" "field" should not be visible
    And "Ratings > Grade category" "field" should not be visible
    And "Ratings > Maximum grade" "field" should not be visible
    And "Whole forum grading > Grade to pass" "field" should not be visible
    And "Whole forum grading > Grade category" "field" should not be visible
    And "Whole forum grading > Maximum grade" "field" should not be visible

    # Set to "Count of ratings"
    When I set the field "Ratings > Aggregate type" to "Count of ratings"
    Then "Ratings > Type" "field" should be visible
    When I set the field "Ratings > Type" to "None"
    Then "Ratings > Grade to pass" "field" should not be visible
    And "Ratings > Grade category" "field" should not be visible
    And "Ratings > Maximum grade" "field" should not be visible
    And "Whole forum grading > Grade to pass" "field" should not be visible
    And "Whole forum grading > Grade category" "field" should not be visible
    And "Whole forum grading > Maximum grade" "field" should not be visible

    # Use point grading
    When I set the field "Ratings > Type" to "Point"
    Then "Ratings > Grade to pass" "field" should be visible
    And "Ratings > Grade category" "field" should be visible
    And "Ratings > Maximum grade" "field" should be visible
    And "Whole forum grading > Grade to pass" "field" should not be visible
    And "Whole forum grading > Grade category" "field" should not be visible
    And "Whole forum grading > Maximum grade" "field" should not be visible

    # Save some values.
    Given I set the field "Ratings > Maximum grade" to "10"
    And I set the field "Ratings > Grade category" to "Tutor"
    And I set the field "Ratings > Grade to pass" to "4"
    When I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook

    # There shouldn't be any Whole forum grade gradeitem.
    Then I should see "Test Forum 1 rating"
    But I should not see "Test Forum 1 whole forum"

    # The values saved should be reflected here.
    And I click on grade item menu "Test Forum 1 rating" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the field "Maximum grade" matches value "10"
    Then the field "Grade to pass" matches value "4"
    And I should see "Tutor" in the "Parent category" "fieldset"

  Scenario: Setting both a rating and a whole forum grade does not bleed
    Given I am on the "Test Forum 1" "forum activity editing" page
    And I expand all fieldsets

    And I set the field "Ratings > Aggregate type" to "Count of ratings"
    And I set the field "Ratings > Type" to "Point"
    And I set the field "Ratings > Maximum grade" to "100"
    And I set the field "Ratings > Grade category" to "Peers"
    And I set the field "Ratings > Grade to pass" to "40"
    And I set the field "Whole forum grading > Type" to "Point"
    And I set the field "Whole forum grading > Maximum grade" to "10"
    And I set the field "Whole forum grading > Grade category" to "Tutor"
    And I set the field "Whole forum grading > Grade to pass" to "4"
    And I press "Save and return to course"
    And I navigate to "View > Grader report" in the course gradebook

    # There shouldn't be any Whole forum grade gradeitem.
    Then I should see "Test Forum 1 rating"
    And I should see "Test Forum 1 whole forum"

    # The values saved should be reflected here.
    And I click on grade item menu "Test Forum 1 rating" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the field "Maximum grade" matches value "100"
    Then the field "Grade to pass" matches value "40"
    And I should see "Peers" in the "Parent category" "fieldset"
    And I press "Cancel"

    And I click on grade item menu "Test Forum 1 whole forum" of type "gradeitem" on "grader" page
    And I choose "Edit grade item" in the open action menu
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the field "Maximum grade" matches value "10"
    Then the field "Grade to pass" matches value "4"
    And I should see "Tutor" in the "Parent category" "fieldset"

  Scenario: Ensure that only gradable users are available in forum grading interface
    Given I am on the "Test Forum 1" "forum activity editing" page logged in as "admin"
    And I expand all fieldsets
    When I set the field "Whole forum grading > Type" to "Point"
    And I set the field "Whole forum grading > Maximum grade" to "10"
    And I set the field "Whole forum grading > Grade to pass" to "4"
    And I press "Save and display"
    And I press "Grade users"
    Then I should see "1 out of 1"
    And I should see "Student 1"
    And I should not see "Teacher 1"
    And I press "Save changes and proceed to the next user"
    And I should see "Student 1"
    And I should not see "Teacher 1"
