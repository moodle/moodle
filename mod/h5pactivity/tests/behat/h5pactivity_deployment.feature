@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Undeployed H5P activities packages should be available only to any user that can deploy packages.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
      | student1 | C1     | student        |
    # Make sure that the teacher2 can update libraries so it show the right info when.
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    # Now create the activity as teacher1.
    And the following "activities" exist:
      | activity    | course | name          | username | packagefilepath                      |
      | h5pactivity | C1     | Music history | teacher1 | h5p/tests/fixtures/filltheblanks.h5p |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Delete" action in the "Teacher 1" report row
    And I click on "Delete" "button" in the "Delete user" "dialogue"
    And I should see "Deleted user Teacher 1"

  @javascript
  Scenario: In an H5P activity, as student I should not be able to deploy the package if not deployed by the teacher
  beforehand. Then if a second teacher deploys the package, I can see it.
    Given I am on the "Music history" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And "This file can't be displayed because it has been uploaded by a user without the required capability to deploy H5P content" "text" should exist
    And I switch to the main frame
    And I log out
    # Then teacher2 will be allowed to deploy the package.
    And I am on the "Music history" "h5pactivity activity" page logged in as teacher2
    And I switch to "h5p-player" class iframe
    When I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries are Berlin"
    And I switch to the main frame
    And I log out
    # Now student1 should be able to see the package.
    And I am on the "Music history" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    When I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries are Berlin"
