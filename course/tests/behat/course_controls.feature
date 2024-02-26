@core @core_course
Feature: Course activity controls works as expected
  In order to manage my course's activities
  As a teacher
  I need to edit, hide and show activities inside course sections

  # The difference between these two scenario outlines is that one is with
  # JS enabled and the other one with JS disabled; we can not use Background
  # sections when using Scenario Outlines because of Behat framework restrictions.

  # We are testing:
  # * Javascript on and off
  # * Topics and weeks course formats
  # * Course controls without paged mode
  # * Course controls with paged mode in the course home page
  # * Course controls with paged mode in a section's page

  @javascript @_cross_browser
  Scenario Outline: Check activities using topics and weeks formats, and paged mode and not paged mode
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname     | Course 1        |
      | shortname    | C1              |
      | format       | <courseformat>  |
      |coursedisplay | <coursedisplay> |
      | numsections  | 5               |
      | startdate    | 0               |
      | initsections | <initsections>  |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | section            | name              |
      | forum    | C1     | <targetsectionnum> | Test forum name 1 |
      | forum    | C1     | <targetsectionnum> | Test forum name 2 |
      | forum    | C1     | 1                  | Test forum name 3 |
    And I am on the "Course 1" course page logged in as "teacher1"
    When I click on <targetpage> "link" in the "region-main" "region"
    And I turn editing mode on
    And I add the "Recent activity" block
    And I open the action menu in "Recent activity" "block"
    And I click on "Delete Recent activity block" "link"
    And I click on "Delete" "button" in the "Delete block?" "dialogue"
    And <belowpage> "section" should not exist
    And I open "Test forum name 1" actions menu
    And I click on "Edit settings" "link" in the "Test forum name 1" activity
    And I should see "Edit settings"
    And I should see "Display description on course page"
    And I set the following fields to these values:
      | Forum name | Just to check that I can edit the name |
      | Description | Just to check that I can edit the description |
      | Display description on course page | 1 |
    And I click on "Cancel" "button"
    And <belowpage> "section" should not exist
    And I open "Test forum name 1" actions menu
    And I choose "Availability > Hide on course page" in the open action menu
    And <belowpage> "section" should not exist
    And I delete "Test forum name 1" activity
    And I should not see "Test forum name 1" in the "region-main" "region"
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And <belowpage> "section" should not exist
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    # General section can't be hidden. Check this part using always the first section.
    And I am on the "Course 1" course page
    And I hide section "1"
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And I show section "1"
    And section "1" should be visible
    And I am on the "C1 > Section 1" "course > section" page
    And <belowpage> "section" should not exist
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Section links" block
    And <belowpage> "section" should not exist
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I should not see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | initsections | targetsectionnum | targetpage              | belowpage                |
      | topics       | 0             | 1            | 0                | "General"               | "Section 2"              |
      | topics       | 1             | 1            | 0                | "General"               | "Section 2"              |
      | topics       | 0             | 1            | 1                | "Section 1"             | "Section 2"              |
      | topics       | 1             | 1            | 1                | "Section 1"             | "Section 2"              |
      | weeks        | 0             | 0            | 0                | "General"               | "8 January - 14 January" |
      | weeks        | 1             | 0            | 0                | "General"               | "8 January - 14 January" |
      | weeks        | 0             | 0            | 1                | "1 January - 7 January" | "8 January - 14 January" |
      | weeks        | 1             | 0            | 1                | "1 January - 7 January" | "8 January - 14 January" |

  Scenario Outline: Check, without javascript, activities using topics and weeks formats, and paged mode and not paged mode
    Given the following "users" exist:
      | username | firstname   | lastname | email                |
      | teacher1 | Teacher     | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname     | Course 1        |
      | shortname    | C1              |
      | format       | <courseformat>  |
      |coursedisplay | <coursedisplay> |
      | numsections  | 5               |
      | startdate    | 0               |
      | initsections | <initsections>  |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | section            | name              | idnumber |
      | forum    | C1     | <targetsectionnum> | Test forum name 1 | 00001    |
      | forum    | C1     | <targetsectionnum> | Test forum name 2 | 00002    |
      | forum    | C1     | 1                  | Test forum name 3 | 00003    |
    And I am on the "Course 1" course page logged in as "teacher1"
    When I click on <targetpage> "link" in the "region-main" "region"
    And I turn editing mode on
    And I add the "Recent activity" block
    And I open the action menu in "Recent activity" "block"
    And I click on "Delete Recent activity block" "link"
    And I press "Yes"
    And <belowpage> "section" should not exist
    And I click on "Edit settings" "link" in the "Test forum name 1" activity
    And I should see "Edit settings"
    And I should see "Display description on course page"
    And I press "Save and return to course"
    And <belowpage> "section" should not exist
    And I click on "Hide on course page" "link" in the "Test forum name 1" activity
    And <belowpage> "section" should not exist
    And I delete "Test forum name 1" activity
    And <belowpage> "section" should not exist
    And I should not see "Test forum name 1" in the "region-main" "region"
    And I duplicate "Test forum name 2" activity editing the new copy with:
      | Forum name | Edited test forum name 2 |
    And <belowpage> "section" should not exist
    And I should see "Test forum name 2"
    And I should see "Edited test forum name 2"
    # General section can't be hidden. Check this part using always the first section.
    And I am on the "Course 1" course page
    And I hide section "1"
    And section "1" should be hidden
    And all activities in section "1" should be hidden
    And I show section "1"
    And section "1" should be visible
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Section links" block
    And I should see "1 2 3 4 5" in the "Section links" "block"
    And I click on "2" "link" in the "Section links" "block"
    And I should not see "Test forum name 2"

    Examples:
      | courseformat | coursedisplay | initsections | targetsectionnum | targetpage              | belowpage                |
      | topics       | 0             | 1            | 0                | "General"               | "Section 2"              |
      | topics       | 1             | 1            | 0                | "General"               | "Section 2"              |
      | topics       | 0             | 1            | 1                | "Section 1"             | "Section 2"              |
      | topics       | 1             | 1            | 1                | "Section 1"             | "Section 2"              |
      | weeks        | 0             | 0            | 0                | "General"               | "8 January - 14 January" |
      | weeks        | 1             | 0            | 0                | "General"               | "8 January - 14 January" |
      | weeks        | 0             | 0            | 1                | "1 January - 7 January" | "8 January - 14 January" |
      | weeks        | 1             | 0            | 1                | "1 January - 7 January" | "8 January - 14 January" |

  @javascript
  Scenario Outline: Indentation should allow one level only
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | coursedisplay   | numsections | startdate |
      | Course 1 | C1        | <courseformat> | <coursedisplay> | 5           | 0         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name               | intro                     | course | idnumber |
      | forum    | Test forum name    | Test forum description    | C1     | forum1   |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open "Test forum name" actions menu
    Then "Move right" "link" should be visible
    And "Move left" "link" should not be visible
    And I click on "Move right" "link" in the "Test forum name" activity
    And I open "Test forum name" actions menu
    And "Move right" "link" should not be visible
    And "Move left" "link" should be visible
    And I click on "Move left" "link" in the "Test forum name" activity
    And I open "Test forum name" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible

    Examples:
      | courseformat |
      | topics       |
      | weeks        |

  @javascript
  Scenario Outline: Admins could disable indentation
    Given the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | startdate |
      | Course 1 | C1 | <courseformat> | <coursedisplay> | 5 | 0 |
    And the following "activities" exist:
      | activity | name               | intro                     | course | idnumber |
      | forum    | Test forum name    | Test forum description    | C1     | forum1   |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I open "Test forum name" actions menu
    And "Move right" "link" should be visible
    And "Move left" "link" should not be visible
    And I click on "Move right" "link" in the "Test forum name" activity
    When the following config values are set as admin:
      | indentation | 0 | format_<courseformat> |
    And I am on "Course 1" course homepage with editing mode on
    And I open "Test forum name" actions menu
    Then "Move right" "link" should not exist
    And "Move left" "link" should not exist

    Examples:
      | courseformat |
      | topics       |
      | weeks        |
