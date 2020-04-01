@core @core_user
Feature: Users' names are displayed across the site according to the user policy settings
  In order to control the way students and teachers see users' names
  As a teacher or admin
  I need to be able to configure the name display formats 'fullnamedisplay' and 'alternativefullnameformat'

  Background:
    Given the following "users" exist:
      | username | firstname | lastname    | email                | middlename | alternatename | firstnamephonetic | lastnamephonetic |
      | user1    | Grainne   | Beauchamp   | one@example.com      | Ann        | Jill          | Gronya            | Beecham          |
      | user2    | Niamh     | Cholmondely | two@example.com      | Jane       | Nina          | Nee               | Chumlee          |
      | user3    | Siobhan   | Desforges   | three@example.com    | Sarah      | Sev           | Shevon            | De-forjay        |
      | teacher1 | Teacher   | 1           | teacher1@example.com |            |               |                   |                  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
    And the following config values are set as admin:
      | fullnamedisplay | firstnamephonetic,lastnamephonetic |
      | alternativefullnameformat | middlename, alternatename, firstname, lastname |

  Scenario: As a student, 'fullnamedisplay' should be used in the participants list and when viewing my own course profile
    Given I log in as "user1"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    And I click on "Gronya,Beecham" "link" in the "Gronya,Beecham" "table_row"
    Then I should see "Gronya,Beecham" in the "region-main" "region"
    And I log out

  Scenario: As a student, 'fullnamedisplay' should be used in the participants list and when viewing another user's course profile
    Given I log in as "user2"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    And I click on "Gronya,Beecham" "link" in the "Gronya,Beecham" "table_row"
    Then I should see "Gronya,Beecham" in the "region-main" "region"
    And I log out

  Scenario: As a teacher, 'alternativefullnameformat' should be used in the participants list but 'fullnamedisplay' used on the course profile
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    Then I should see "Ann, Jill, Grainne, Beauchamp" in the "Ann, Jill, Grainne, Beauchamp" "table_row"
    And I click on "Ann, Jill, Grainne, Beauchamp" "link" in the "Ann, Jill, Grainne, Beauchamp" "table_row"
    And I should see "Gronya,Beecham" in the "region-main" "region"
    And I log out

  Scenario: As an authenticated user, 'fullnamedisplay' should be used in the navigation and when viewing my profile
    Given I log in as "user1"
    When I follow "Profile" in the user menu
    Then I should see "Gronya,Beecham" in the ".usermenu" "css_element"
    And I should see "Gronya,Beecham" in the ".page-context-header" "css_element"
    And I log out

  Scenario: As an admin, 'fullnamedisplay' should be used when using the 'log in as' function
    Given I log in as "admin"
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Jane, Nina, Niamh, Cholmondely"
    And I follow "Log in as"
    Then I should see "You are logged in as Nee,Chumlee"
    And I log out

  Scenario: As an admin, 'fullnamedisplay' should be used when viewing another user's site profile
    Given I log in as "admin"
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Ann, Jill, Grainne, Beauchamp"
    Then I should see "Gronya,Beecham" in the ".page-header-headings" "css_element"
    And I log out

  @javascript
  Scenario: As a teacher, the 'alternativefullnameformat' should be used when searching for and enrolling a user
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to course participants
    And I press "Enrol users"
    And I set the field "Select users" to "three@example.com"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Select users" "form_row"
    Then I should see "Sarah, Sev, Siobhan, Desforges"
