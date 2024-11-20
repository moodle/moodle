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
    And the following "core_badges > Badges" exist:
      | name    | course | description | image                        | status | type |
      | Badge 1 | C1     | Badge 1     | badges/tests/behat/badge.png | active | 2    |
      | Badge 2 | C1     | Badge 2     | badges/tests/behat/badge.png | active | 2    |
    And the following "core_badges > Criterias" exist:
      | badge   | role           |
      | Badge 1 | editingteacher |
      | Badge 2 | editingteacher |
    And I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Badges" in current page administration
    And I follow "Badge 1"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    # Issue Badge 2 of 2
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
    And I follow "Badge 2"
    And I select "Recipients (0)" from the "jump" singleselect
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
      | Number of latest badges to display | 1 |
    And I press "Save changes"
    Then I should see "Badge 2" in the "Latest badges" "block"
    And I should not see "Badge 1" in the "Latest badges" "block"
