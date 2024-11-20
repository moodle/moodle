@core_h5p @_file_upload @_switch_iframe @editor_tiny
Feature: Undeployed H5P content should be only available to users that can deploy packages.

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
    And the following "activities" exist:
      | activity | name    | intro     | introformat | course | content | contentformat | idnumber |
      | page     | H5PPage | PageDesc1 | 1           | C1     | H5Ptest | 1             | 1        |
    And I am on the H5PPage "page activity editing" page logged in as teacher1
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | teacher1 | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
    And I click on the "Configure H5P content" button for the "Page content" TinyMCE editor
    And I click on "Browse repositories..." "button" in the "Insert H5P content" "dialogue"
    And I click on "Content bank" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P content" "button" in the "Insert H5P content" "dialogue"
    # This is important here not to do Save and display as if not this will be deployed and the student will see it in the first step.
    And I click on "Save and return to course" "button"
    And I log out
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Delete" action in the "Teacher 1" report row
    And I click on "Delete" "button" in the "Delete user" "dialogue"
    And I should see "Deleted user Teacher 1"

  @javascript
  Scenario: A student I should not be able to see a package that has been deployed by a deleted user. Then if another user deploys the package, I can see it.
    Given I am on the "H5PPage" "page activity" page logged in as student1
    And I switch to "h5p-iframe" class iframe
    And I should see "This file can't be displayed"
    And I switch to the main frame
    And I log out
    # Then teacher2 will be allowed to deploy the package.
    When I am on the "H5PPage" "page activity" page logged in as teacher2
    # Note the double switch to iframe is needed because the first iframe is the one that contains the H5P package and
    # the second iframe is the one that contains the H5P content.
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries are Berlin"
    And I switch to the main frame
    And I log out
    # Now student1 should be able to see the package.
    And I am on the "H5PPage" "page activity" page logged in as student1
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Of which countries are Berlin"
