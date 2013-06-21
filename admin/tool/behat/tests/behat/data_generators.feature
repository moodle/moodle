@tool @tool_behat
Feature: Set up contextual data for tests
  In order to write tests quickly
  As a developer
  I need to fill the database with fixtures

  Scenario: Add a bunch of users
    Given the following "users" exists:
      | username  | password  | firstname | lastname |
      | testuser  | testuser  |  |  |
      | testuser2 | testuser2 | TestFirstname | TestLastname |
    And I log in as "testuser"
    And I log out
    When I log in as "testuser2"
    Then I should see "TestFirstname"

  @javascript
  Scenario: Add a bunch of courses and categories
    Given the following "categories" exists:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
      | Cat 2 | CAT1 | CAT2 |
      | Cat 3 | CAT1 | CAT3 |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | COURSE1 | CAT3 |
      | Course 2 | COURSE2 | CAT3 |
      | Course 3 | COURSE3 | 0 |
    When I log in as "admin"
    Then I should see "Course 1"
    And I should see "Course 2"
    And I should see "Course 3"
    And I go to the courses management page
    And I follow "Cat 1"
    And I should see "Cat 2"
    And I should see "Cat 3"
    And I follow "Cat 3"
    And I should see "Course 1"
    And I should see "Course 2"
    And I select "Cat 1 / Cat 2" from "Course categories:"
    And I should see "No courses in this category"
    And I select "Miscellaneous" from "Course categories:"
    And I should see "Course 3"

  @javascript
  Scenario: Add a bunch of groups and groupings
    Given the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "groups" exists:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "groupings" exists:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
      | Grouping 2 | C1 | GG2 |
    When I log in as "admin"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    Then I should see "Group 1"
    And I should see "Group 2"
    And I follow "Groupings"
    And I should see "Grouping 1"
    And I should see "Grouping 2"

  Scenario: Add course enrolments
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | student1 | C1 | student |
    When I log in as "student1"
    And I follow "Course 1"
    Then I should see "Topic 1"

  @javascript
  Scenario: Add relations between users and groups
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "groups" exists:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "groupings" exists:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
    And the following "course enrolments" exists:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "group members" exists:
      | user | group |
      | student1 | G1 |
      | student2 | G2 |
    And the following "grouping groups" exists:
      | grouping | group |
      | GG1 | G1 |
    When I log in as "admin"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    Then the "groups" select box should contain "Group 1 (1)"
    And the "groups" select box should contain "Group 2 (1)"
    And I select "Group 1 (1)" from "groups"
    And I wait "5" seconds
    And the "members" select box should contain "Student 1"
    And I select "Group 2 (1)" from "groups"
    And I wait "5" seconds
    And the "members" select box should contain "Student 2"
