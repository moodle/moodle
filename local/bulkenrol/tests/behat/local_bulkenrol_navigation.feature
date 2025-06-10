@local @local_bulkenrol @local_bulkenrol_navigation @javascript
Feature: Using the local_bulkenrol plugin
  In order to bulk enrol users into the course
  As user with the appropriate rights
  I need to have a proper navigation

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config      | value  | plugin          |
      | enrolplugin | manual | local_bulkenrol |
    Given I log in as "admin"
    And I navigate to "Plugins > Enrolments > User bulk enrolment" in site administration
    And I set the following fields to these values:
      | Role | Student |
    And I press "Save changes"
    And I set the following system permissions of "Teacher" role:
      | capability                 | permission |
      | local/bulkenrol:enrolusers | Allow      |
    And I log out

  Scenario Outline: Access the bulk enrolment page via the participants page jump menu
    Given the following config values are set as admin:
      | config     | value        | plugin          |
      | navigation | <navigation> | local_bulkenrol |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I select "Participants" from secondary navigation
    And I select "User bulk enrolment" from the "jump" singleselect
    Then I should see "User bulk enrolment" in the "#region-main h2" "css_element"

    Examples:
      | navigation |
      | navpart  |
      | navboth    |

  Scenario Outline: Access the bulk enrolment page via the participants page jump menu
    Given the following config values are set as admin:
      | config     | value        | plugin          |
      | navigation | <navigation> | local_bulkenrol |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "User bulk enrolment" in current page administration
    Then I should see "User bulk enrolment" in the "#region-main h2" "css_element"

    Examples:
      | navigation |
      | navcourse  |
      | navboth    |
