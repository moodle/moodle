@block @block_badges @core_badges @_file_upload @javascript
Feature: Enable Block Badges in a course
  In order to enable the badges block in a course
  As a teacher
  I can add badges block to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    # Issue badge 1 of 2
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | id_name | Badge 1 |
      | id_description | Badge 1 |
      | id_issuername | Teacher 1 |
    And I upload "blocks/badges/tests/fixtures/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I select "Manual issue by role" from the "Add badge criteria" singleselect
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    # Issue Badge 2 of 2
    And I am on "Course 1" course homepage
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | id_name | Badge 2 |
      | id_description | Badge 2 |
      | id_issuername | Teacher 1 |
    And I upload "blocks/badges/tests/fixtures/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I select "Manual issue by role" from the "Add badge criteria" singleselect
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I follow "Recipients (0)"
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    And I log out

  Scenario: Add the recent badges block to a course.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Latest badges" block
    Then I should see "Badge 1" in the "Latest badges" "block"
    And I should see "Badge 2" in the "Latest badges" "block"

  Scenario: Add the recent badges block to a course and limit it to only display 1 badge.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Latest badges" block
    And I configure the "Latest badges" block
    And I set the following fields to these values:
      | id_config_numberofbadges | 1 |
    And I press "Save changes"
    Then I should see "Badge 2" in the "Latest badges" "block"
    And I should not see "Badge 1" in the "Latest badges" "block"
