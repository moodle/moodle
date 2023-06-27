@format @format_tiles @course_mod_visibility_nosubtiles_hidden_sec @javascript
Feature:  In a section for Teacher, hidden activities are dimmed where subtiles are not being used.
  In this specific case when Teacher hides a whole section, its contents are *all* dimmed even if not individually hidden

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | enablecompletion |
      | Course Mod Vis NoSubtiles Course2 | C1        | tiles  | 0             | 5           | 1                |
    And the following "activities" exist:
      | activity | name          | intro                  | course | idnumber | section | visible |
      | quiz     | Test quiz V   | Test quiz description  | C1     | quiz1    | 1       | 1       |
      | page     | Test page V   | Test page description  | C1     | page1    | 1       | 1       |
      | forum    | Test forum V  | Test forum description | C1     | forum1   | 1       | 1       |
      | url      | Test URL V    | Test url description   | C1     | url1     | 1       | 1       |
      | label    | Test label V  | Test label description | C1     | label1   | 1       | 1       |
      | quiz     | Test quiz NV  | Test quiz description  | C1     | quiz2    | 1       | 0       |
      | page     | Test page NV  | Test page description  | C1     | page2    | 1       | 0       |
      | forum    | Test forum NV | Test forum description | C1     | forum2   | 1       | 0       |
      | url      | Test URL NV   | Test url description   | C1     | url2     | 1       | 0       |
      | label    | Test label NV | Test label description | C1     | label2   | 1       | 0       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

    And format_tiles subtiles are off for course "Course Mod Vis NoSubtiles Course2"

    And  I log in as "teacher1"
    And I am on "Course Mod Vis NoSubtiles Course2" course homepage with editing mode on
    And I wait until the page is ready
    And I wait "1" seconds
    And I hide tile "1"
#  Section 1 is hidden so all activities below should be dimmed even if not individually hidden
    And I turn editing mode off
    And I log out tiles

  @javascript
  Scenario: Teacher sees hidden section but activities are dimmed with subtiles off
    When I log in as "teacher1"

    And I am on "Course Mod Vis NoSubtiles Course2" course homepage

    And I click on tile "1"
    And I wait until the page is ready
    Then I should see "Test quiz V"
    And activity in format tiles is dimmed "Test quiz V"

    And I should see "Test page V"
    And activity in format tiles is dimmed "Test page V"

    And I should see "Test forum V"
    And activity in format tiles is dimmed "Test forum V"

    And I should see "Test URL V"
    And activity in format tiles is dimmed "Test URL V"

    And I should see "Test quiz NV"
    And activity in format tiles is dimmed "Test quiz NV"

    And I should see "Test page NV"
    And activity in format tiles is dimmed "Test page NV"

    And I should see "Test forum NV"
    And activity in format tiles is dimmed "Test forum NV"

    And I should see "Test URL NV"
    And activity in format tiles is dimmed "Test URL NV"

    And I click on close button for tile "1"
    And I log out tiles

  @javascript
  Scenario: Student cannot see visible (NV) activities or section with subtiles off
    When I log in as "student1"
    And I am on "Course Mod Vis NoSubtiles Course2" course homepage
    And I wait "1" seconds
    Then "li#tile-1" "css_element" should not be visible
    And I should not see "Test quiz V"
    And I should not see "Test page V"
    And I should not see "Test forum V"
    And I should not see "Test URL V"
    And I should not see "Test quiz NV"
    And I should not see "Test page NV"
    And I should not see "Test forum NV"
    And I should not see "Test URL NV"
    And I log out tiles
