@enrol @enrol_lti
Feature: Check that settings are adhered to when creating an enrolment plugin
  In order to create an LTI enrolment instance
  As an admin
  I need to ensure the site-wide settings are used

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Manage enrol plugins" node in "Site administration > Plugins > Enrolments"
    And I click on "Enable" "link" in the "Publish as LTI tool" "table_row"
    And I navigate to "Publish as LTI tool" node in "Site administration > Plugins > Enrolments"
    And I set the following fields to these values:
      | Email display       | Allow everyone to see my email address |
      | City/town           | Perth                                  |
      | Select a country    | Australia                              |
      | Timezone            | Australia/Perth                        |
      | Institution         | Moodle Pty Ltd                         |
    And I press "Save changes"
    And I log out

  Scenario: As an admin set site-wide settings for the enrolment plugin and ensure they are used
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I select "Publish as LTI tool" from the "Add method" singleselect
    When I expand all fieldsets
    Then the field "Email display" matches value "Allow everyone to see my email address"
    And the field "City/town" matches value "Perth"
    And the field "Select a country" matches value "Australia"
    And the field "Timezone" matches value "Australia/Perth"
    And the field "Institution" matches value "Moodle Pty Ltd"
    And I set the following fields to these values:
      | Email display       | Hide my email address from everyone |
      | City/town           | Whistler                            |
      | Select a country    | Canada                              |
      | Timezone            | America/Vancouver                   |
      | Institution         | Moodle Pty Ltd - remote             |
    And I press "Add method"
    And I click on "Edit" "link" in the "Publish as LTI tool" "table_row"
    And the field "Email display" matches value "Hide my email address from everyone"
    And the field "City/town" matches value "Whistler"
    And the field "Select a country" matches value "Canada"
    And the field "Timezone" matches value "America/Vancouver"
    And the field "Institution" matches value "Moodle Pty Ltd - remote"
