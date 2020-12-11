@core @core_user
Feature: Set email display preference
  In order to control who can see my email address on my profile page
  As a student
  I need my email to be shown to only the user groups chosen

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname    | email                 | maildisplay |
      | teacher1  | Teacher   | 1           | teacher1@example.com  | 2           |
      | studentp  | Student   | PEER        | studentP@example.com  | 2           |
      | studentn  | Student   | NONE        | studentN@example.com  | 0           |
      | studente  | Student   | EVERYONE    | studentE@example.com  | 1           |
      | studentm  | Student   | MEMBERS     | studentM@example.com  | 2           |
    And the following "courses" exist:
      | fullname  | shortname | format |
      | Course 1  | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course | role           | status | timeend |
      | teacher1  | C1     | teacher        |    0   |    0    |
      | studentp  | C1     | student        |    0   |    0    |
      | studentn  | C1     | student        |    0   |    0    |
      | studente  | C1     | student        |    0   |    0    |
      | studentm  | C1     | student        |    0   |    0    |

  @javascript
  Scenario: Student viewing own profile
    Given I log in as "studentp"
    When I follow "Profile" in the user menu
    Then I should see "studentP@example.com"
    And I should see "(Visible to other course participants)"

  @javascript
  Scenario: Student peer on the same course viewing profiles
    Given I log in as "studentp"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I follow "Student NONE"
    Then I should not see "studentN@example.com"
    And I navigate to course participants
    When I follow "Student EVERYONE"
    Then I should see "studentE@example.com"
    And I navigate to course participants
    When I follow "Student MEMBERS"
    Then I should see "studentM@example.com"

  @javascript
  Scenario: Student viewing teacher email (whose maildisplay = MEMBERS)
    Given I log in as "studentp"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I follow "Teacher 1"
    Then I should see "teacher1@example.com"

  @javascript
  Scenario: Teacher viewing student email, whilst site:showuseridentity = “email”
    Given the following config values are set as admin:
      | showuseridentity      | email |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I follow "Student NONE"
    Then I should see "studentN@example.com"
    And I navigate to course participants
    When I follow "Student MEMBERS"
    Then I should see "studentM@example.com"

  @javascript
  Scenario: Teacher viewing student email, whilst site:showuseridentity = “”
    Given I log in as "teacher1"
    And the following config values are set as admin:
      | showuseridentity      | |
    And I am on "Course 1" course homepage
    And I navigate to course participants
    When I follow "Student NONE"
    Then I should not see "studentN@example.com"
    And I navigate to course participants
    When I follow "Student MEMBERS"
    Then I should see "studentM@example.com"

  @javascript
  Scenario: User can see user's email address settings on own profile
    Given I log in as "studentp"
    And I follow "Profile" in the user menu
    Then I should see "studentP@example.com"
    And I should see "(Visible to other course participants)"
    When I click on "Edit profile" "link" in the "region-main" "region"
    And I set the following fields to these values:
      | maildisplay | 0 |
    And I click on "Update profile" "button"
    Then I should see "(Hidden from all non-privileged users)"
    When I click on "Edit profile" "link" in the "region-main" "region"
    And I set the following fields to these values:
      | maildisplay | 1 |
    And I click on "Update profile" "button"
    Then I should see "(Visible to everyone)"
