@format @format_singleactivity
Feature: Activity navigation in a single activity course
  In order to quickly switch to another activity in a single activity course with multiple (hidden) activities
  As a teacher
  I need to use the activity navigation controls in activities

  Background:
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format         | activitytype |
      | Course 1 | C1        | singleactivity | forum        |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | student1  | C1      | student         |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section |
      | assign     | Assignment 1 | Test assignment description | C1     | assign1   | 0       |
      | chat       | Chat 1       | Test chat description       | C1     | chat1     | 0       |
      | forum      | Forum 1      | Test forum description      | C1     | forum1    | 0       |

  Scenario: Step through hidden activities in the course as a teacher.
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    # The first activity (Forum 1) won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And I should see "Assignment 1 (hidden)" in the "#next-activity-link" "css_element"
    And I follow "Assignment 1 (hidden)"
    And I should see "Forum 1" in the "#prev-activity-link" "css_element"
    And I should see "Chat 1 (hidden)" in the "#next-activity-link" "css_element"
    And I follow "Chat 1 (hidden)"
    And I should see "Assignment 1 (hidden)" in the "#prev-activity-link" "css_element"
    And "#next-activity-link" "css_element" should not exist

  Scenario: Jump to a hidden activity as a teacher
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage
    Then "Jump to..." "field" should exist
    # The current activity (Forum 1) will not be listed.
    And the "Jump to..." select box should not contain "Forum 1"
    # Check drop down menu contents.
    And the "Jump to..." select box should contain "Assignment 1 (hidden)"
    And the "Jump to..." select box should contain "Chat 1 (hidden)"
    # Jump to a hidden activity somewhere in the middle.
    When I select "Assignment 1 (hidden)" from the "Jump to..." singleselect
    Then I should see "Assignment 1"
    And I should see "Forum 1" in the "#prev-activity-link" "css_element"
    And I should see "Chat 1 (hidden)" in the "#next-activity-link" "css_element"
    # Jump to the first activity.
    And I select "Forum 1" from the "Jump to..." singleselect
    And I should see "Assignment 1 (hidden)" in the "#next-activity-link" "css_element"
    But "#prev-activity-link" "css_element" should not exist
    # Jump to the last activity.
    And I select "Chat 1 (hidden)" from the "Jump to..." singleselect
    And I should see "Assignment 1 (hidden)" in the "#prev-activity-link" "css_element"
    But "#next-activity-link" "css_element" should not exist

  Scenario: The activity navigation controls are not available as a student.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    # The first activity won't have the previous activity link.
    Then "#prev-activity-link" "css_element" should not exist
    And "#next-activity-link" "css_element" should not exist
    And "Jump to..." "field" should not exist
