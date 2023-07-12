@format @format_tiles @format_tiles_section_vis
Feature: Show/hide course sections in format_tiles
  In order to delay sections availability
  As a teacher
  I need to show or hide sections
# Adapted from core - course/tests/bahat.

  @javascript
  Scenario: Show / hide section icon functions correctly
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | tiles  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

    And the following "activities" exist:
      | activity | name               | intro | course | idnumber  | section | visible |
      | forum    | Test visible forum 2 | intro | C1     | forrumvis | 2       | 1       |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I wait until the page is ready
    And I follow "Collapse all"
    And I wait until the page is ready
    And I expand section "1" for edit
    And I wait until the page is ready
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name   | Test hidden forum 11 name        |
      | Description  | Test hidden forum 11 description |
      | Availability | Hide from students               |
    And I wait until the page is ready
    And I wait "1" seconds
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name   | Test hidden forum 12 name        |
      | Description  | Test hidden forum 12 description |
      | Availability | Show on course page              |
    And I wait until the page is ready

    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I follow "Collapse all"
    And I wait until the page is ready
    And I expand section "2" for edit
    And I wait until the page is ready
    And I wait "1" seconds
    And I add a "Forum" to section "2" and I fill the form with:
      | Forum name   | Test hidden forum 21 name        |
      | Description  | Test hidden forum 21 description |
      | Availability | Hide from students               |
    And I wait until the page is ready
    And activity in format tiles is dimmed "Test hidden forum 21 name"

    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I follow "Collapse all"
    And I wait until the page is ready
    And I expand section "3" for edit
    And I wait until the page is ready
    And I wait "1" seconds
    And I add a "Forum" to section "3" and I fill the form with:
      | Forum name   | Test hidden forum 31 name        |
      | Description  | Test hidden forum 31 description |
      | Availability | Hide from students               |
    And I wait until the page is ready
    And I wait "1" seconds
    And activity in format tiles is dimmed "Test hidden forum 31 name"
    And I wait "1" seconds
    And I add a "Forum" to section "3" and I fill the form with:
      | Forum name   | Test visible forum 32 name        |
      | Description  | Test visible forum 32 description |
      | Availability | Show on course page              |
    And I wait until the page is ready
    And I wait "1" seconds
    And activity in format tiles is not dimmed "Test hidden forum 31 name"

    And I am on "Course 1" course homepage
    And I wait "1" seconds
    And I wait until the page is ready
    And I hide tile "1"
    And I wait until the page is ready
    And I wait "1" seconds
    And section "1" should be hidden
    And section "2" should be visible
    And section "3" should be visible
    And I hide tile "2"
    And section "2" should be hidden
    And I show section "2"
    And section "2" should be visible
    And I hide tile "3"
    And I show section "3"
    And I hide tile "3"
    And section "3" should be hidden
    And I reload the page
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And section "2" should be visible
    And section "3" should be hidden
    And all activities in section "1" should be hidden
    And I log out tiles

    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I should not see "Tile 1"
    And I should not see "Tile 3"

    And I should see "Tile 2"
    And I should see "Tile 4"
    And I should see "Tile 5"
    And I click on tile "2"
    And I should not see "Test hidden forum 22 name"
