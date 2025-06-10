@block @block_badges @core_badges @_file_upload @javascript
Feature: Enable Block Badges on the frontpage and view awarded badges
  In order to enable the badges block on the frontpage
  As a admin
  I can add badges block to the frontpage

  Scenario: Add the recent badges block on the frontpage and view recent badges
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
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | badges    | System       | 1         | site-index      | side-pre      |
    And I am on the "Course 1" course page logged in as teacher1
    # Issue badge 1 of 2
    And I navigate to "Badges > Add a new badge" in current page administration
    And I set the following fields to these values:
      | id_name | Badge 1 |
      | id_description | Badge 1 |
    And I upload "blocks/badges/tests/fixtures/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I select "Manual issue by role" from the "Add badge criteria" singleselect
    And I set the field "Teacher" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Teacher 1 (teacher1@example.com)"
    And I press "Award badge"
    When I am on site homepage
    Then I should see "Badge 1" in the "Latest badges" "block"
