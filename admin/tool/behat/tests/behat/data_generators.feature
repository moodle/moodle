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
    And I follow "Cat 2"
    And I should see "No courses in this category"
    And I follow "Miscellaneous"
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

  @javascript
  Scenario: Role overrides
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "categories" exists:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exists:
      | user | course | role |
      | student1 | C1 | student |
      | teacher1 | C1 | editingteacher |
    And the following "permission overrides" exists:
      | capability | permission | role | contextlevel | reference |
      | mod/forum:editanypost | Allow | student | Course | C1 |
      | mod/forum:replynews | Prevent | editingteacher | Course | C1 |
    When I log in as "admin"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Permissions"
    And I select "Student (1)" from "Advanced role override"
    Then the "mod/forum:editanypost" field should match "1" value
    And I press "Cancel"
    And I select "Teacher (1)" from "Advanced role override"
    And the "mod/forum:replynews" field should match "-1" value
    And I press "Cancel"

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

  Scenario: Add role assigns
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | user1 | User | 1 | user1@moodlemoodle.com |
      | user2 | User | 2 | user2@moodlemoodle.com |
      | user3 | User | 3 | user3@moodlemoodle.com |
    And the following "categories" exists:
      | name | category | idnumber |
      | Cat 1 | 0 | CAT1 |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | CAT1 |
    And the following "role assigns" exists:
      | user  | role           | contextlevel | reference |
      | user1 | manager        | System       |           |
      | user2 | editingteacher | Category     | CAT1      |
      | user3 | editingteacher | Course       | C1        |
    When I log in as "user1"
    Then I should see "Front page settings"
    And I log out
    And I log in as "user2"
    And I follow "Course 1"
    And I should see "Turn editing on"
    And I log out
    And I log in as "user3"
    And I follow "Course 1"
    And I should see "Turn editing on"

  Scenario: Add modules
    Given the following "courses" exists:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "activities" exists:
      | activity   | name                   | intro                         | course | idnumber    |
      | assign     | Test assignment name   | Test assignment description   | C1     | assign1     |
      | assignment | Test assignment22 name | Test assignment22 description | C1     | assignment1 |
      | book       | Test book name         | Test book description         | C1     | book1       |
      | chat       | Test chat name         | Test chat description         | C1     | chat1       |
      | choice     | Test choice name       | Test choice description       | C1     | choice1     |
      | data       | Test database name     | Test database description     | C1     | data1       |
      | feedback   | Test feedback name     | Test feedback description     | C1     | feedback1   |
      | folder     | Test folder name       | Test folder description       | C1     | folder1     |
      | forum      | Test forum name        | Test forum description        | C1     | forum1      |
      | glossary   | Test glossary name     | Test glossary description     | C1     | glossary1   |
      | imscp      | Test imscp name        | Test imscp description        | C1     | imscp1      |
      | label      | Test label name        | Test label description        | C1     | label1      |
      | lesson     | Test lesson name       | Test lesson description       | C1     | lesson1     |
      | lti        | Test lti name          | Test lti description          | C1     | lti1        |
      | page       | Test page name         | Test page description         | C1     | page1       |
      | quiz       | Test quiz name         | Test quiz description         | C1     | quiz1       |
      | resource   | Test resource name     | Test resource description     | C1     | resource1   |
      | scorm      | Test scorm name        | Test scorm description        | C1     | scorm1      |
      | survey     | Test survey name       | Test survey description       | C1     | survey1     |
      | url        | Test url name          | Test url description          | C1     | url1        |
      | wiki       | Test wiki name         | Test wiki description         | C1     | wiki1       |
      | workshop   | Test workshop name     | Test workshop description     | C1     | workshop1   |
    When I log in as "admin"
    And I follow "Course 1"
    Then I should see "Test assignment name"
    # Assignment 2.2 module type is disabled by default
    # And I should see "Test assignment22 name"
    And I should see "Test book name"
    And I should see "Test chat name"
    And I should see "Test choice name"
    And I should see "Test database name"
    # Feedback module type is disabled by default
    # And I should see "Test feedback name"
    And I should see "Test folder name"
    And I should see "Test forum name"
    And I should see "Test glossary name"
    And I should see "Test imscp name"
    # We don't see label name, we see only description:
    And I should see "Test label description"
    And I should see "Test lesson name"
    And I should see "Test lti name"
    And I should see "Test page name"
    And I should see "Test quiz name"
    And I should see "Test resource name"
    And I should see "Test scorm name"
    And I should see "Test survey name"
    And I should see "Test url name"
    And I should see "Test wiki name"
    And I should see "Test workshop name"
    And I follow "Test assignment name"
    And I should see "Test assignment description"

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
    And the "members" select box should contain "Student 1"
    And I select "Group 2 (1)" from "groups"
    And the "members" select box should contain "Student 2"
