@core
Feature: Context freezing apply to child contexts
  In order to preserve content
  As a manager
  I can disbale writes at different areas

  Background:
    Given the following config values are set as admin:
      | contextlocking | 1 |
    And the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher   | Ateacher  | Teacher  | teacher@example.com   |
      | student1  | Astudent  | Astudent | student1@example.com  |
    And the following "categories" exist:
      | name  | category | idnumber |
      | cata  | 0        | cata     |
      | cataa | cata     | cataa    |
      | catb  | 0        | catb     |
    And the following "courses" exist:
      | fullname  | shortname | category  |
      | courseaa1 | courseaa1 | cataa     |
      | courseaa2 | courseaa2 | cataa     |
      | courseb   | courseb   | catb      |
    And the following "activities" exist:
      | activity  | name    | course    | idnumber  |
      | forum     | faa1    | courseaa1 | faa1      |
      | forum     | faa1b   | courseaa1 | faa1b     |
      | forum     | faa2    | courseaa2 | faa2      |
      | forum     | fb      | courseb   | fb        |
    And the following "course enrolments" exist:
      | user      | course    | role           |
      | teacher   | courseaa1 | editingteacher |
      | student1  | courseaa1 | student        |
      | teacher   | courseaa2 | editingteacher |
      | student1  | courseaa2 | student        |
      | teacher   | courseb   | editingteacher |
      | student1  | courseb   | student        |

  Scenario: Freeze course module module should freeze just that module
    Given I log in as "admin"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    And I should see "Add a new discussion topic"
    When I follow "Freeze this context"
    And I click on "Continue" "button"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should see "Turn editing on"
    When I follow "faa1b"
    Then I should see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    Then I should see "Turn editing on"
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "teacher"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should see "Turn editing on"
    When I follow "faa1b"
    Then I should see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    Then I should see "Turn editing on"
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "student1"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    When I follow "faa1b"
    Then I should see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    When I follow "fb"
    Then I should see "Add a new discussion topic"

  Scenario: Freeze course should freeze all children
    Given I log in as "admin"
    And I am on "courseaa1" course homepage
    And I should see "Turn editing on"
    When I follow "Freeze this context"
    And I click on "Continue" "button"
    Then I should not see "Turn editing on"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should not see "Turn editing on"
    And I should see "Unfreeze this context"
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    And I should not see "Unfreeze this context"
    When I am on "courseaa2" course homepage
    Then I should see "Turn editing on"
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "teacher"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should not see "Turn editing on"
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    Then I should see "Turn editing on"
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "student1"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    When I follow "faa2"
    Then I should see "Add a new discussion topic"
    When I am on "courseb" course homepage
    When I follow "fb"
    Then I should see "Add a new discussion topic"

  Scenario: Freeze course category should freeze all children
    Given I log in as "admin"
    And I go to the courses management page
    And I click on "managecontextlock" action for "cata" in management category listing
    And I click on "Continue" "button"
    And I am on "courseaa1" course homepage
    And I should not see "Turn editing on"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should not see "Turn editing on"
    And I should not see "Unfreeze this context"
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    And I should not see "Unfreeze this context"
    When I am on "courseaa2" course homepage
    Then I should not see "Turn editing on"
    When I follow "faa2"
    Then I should not see "Add a new discussion topic"
    And I should not see "Unfreeze this context"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "teacher"
    And I am on "courseaa1" course homepage
    Then I should not see "Turn editing on"
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    Then I should not see "Turn editing on"
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    Then I should not see "Turn editing on"
    When I follow "faa2"
    Then I should not see "Add a new discussion topic"
    When I am on "courseb" course homepage
    Then I should see "Turn editing on"
    When I follow "fb"
    Then I should see "Add a new discussion topic"

    And I log out
    When I log in as "student1"
    And I am on "courseaa1" course homepage
    And I follow "faa1"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa1" course homepage
    When I follow "faa1b"
    Then I should not see "Add a new discussion topic"
    When I am on "courseaa2" course homepage
    When I follow "faa2"
    Then I should not see "Add a new discussion topic"
    When I am on "courseb" course homepage
    When I follow "fb"
    Then I should see "Add a new discussion topic"
