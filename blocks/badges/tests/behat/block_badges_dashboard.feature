@block @block_badges @core_badges @_file_upload @javascript
Feature: Enable Block Badges on the dashboard and view awarded badges
  In order to view recent badges on the dashboard
  As a teacher
  I can add badges block to the dashboard

  Scenario: Add the recent badges block to a course.
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "blocks" exist:
      | blockname  | contextlevel | reference | pagetypepattern | defaultregion |
      | badges     | System       | 1         | my-index        | side-post     |
    And the following "core_badges > Badge" exists:
      | name        | Badge 1                      |
      | course      | C1                           |
      | description | Badge 1                      |
      | image       | badges/tests/behat/badge.png |
      | status      | active                       |
      | type        | 2                            |
    And the following "core_badges > Criteria" exists:
      | badge  | Badge 1       |
      | role   | editingteacher |
    And I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Badges" in current page administration
    And I follow "Badge 1"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    And I log out
    When I log in as "teacher1"
    Then I should see "Badge 1" in the "Latest badges" "block"
