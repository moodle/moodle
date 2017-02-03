@block @block_participants
Feature: People Block used in a course
  In order to view participants in a course
  As a teacher
  I can add the people block to a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C101      | 0        |
    And the following "users" exist:
      | username    | firstname | lastname | email            |
      | student1    | Sam       | Student  | student1@example.com |
    And the following "course enrolments" exist:
      | user        | course | role           |
      | student1    | C101   | student        |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "People" block
    And I log out

  Scenario: Student can view participants link
    When I log in as "student1"
    And I follow "Course 1"
    Then "People" "block" should exist
    And I should see "Participants" in the "People" "block"

  Scenario: Student can follow participants link and be directed to the correct page
    When I log in as "student1"
    And I follow "Course 1"
    And I click on "Participants" "link" in the "People" "block"
    Then I should see "All participants" in the "#page-content" "css_element"
    And the "My courses" select box should contain "C101"

  Scenario: Student without permission can not view participants link
    Given the following "permission overrides" exist:
         | capability | permission | role | contextlevel | reference |
         | moodle/course:viewparticipants | Prevent | student | Course | C101 |
    When I log in as "student1"
    And I follow "Course 1"
    Then "People" "block" should not exist
