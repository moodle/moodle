@format @format_tiles @javascript_nav @javascript
Feature: Interface can be enhabced with JS nav if allowed by site admin
  In order to improve navigation
  As a user
  I need to navigate the courses without errors

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1        | tiles  | 0             | 5           |
    And the following "activities" exist:
      | activity | name                    | intro                       | course | idnumber | section | visible |
      | assign   | Test assignment name    | Test assignment description | C1     | assign1  | 0       | 1       |
      | forum    | Announcements Sec 0     | Test forum description      | C1     | forum1   | 0       | 1       |
      | book     | Test book name s1       | Test book description       | C1     | book1    | 1       | 1       |
      | chat     | Test chat name s2       | Test chat description       | C1     | chat1    | 2       | 1       |
      | choice   | Test choice name hidden | Test choice description     | C1     | choice1  | 2       | 0       |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |

  @javascript
  Scenario: Open section 1 then close and open section 2 with JS as student
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible
    And I click on tile "1"
    And I wait until the page is ready
    And I should see "Test book name s1"
    And I should not see "Test chat name s2"
    And "#editsectiontbtn-1" "css_element" should not be visible
    And I click on close button for tile "1"
    And I should not see "Test book name s1"
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible

    And I click on tile "2"
    And I wait until the page is ready
    And I should not see "Test book name s1"
    And I should see "Test chat name s2"
    And I click on close button for tile "2"
    And I should not see "Test chat name s2"
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible

  Scenario: Open section 1 then close and open section 2 without JS as student
    When the following config values are set as admin:
      | config           | value | plugin       |
      | usejavascriptnav | 0     | format_tiles |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible

    And I click on tile "1"
    And I wait until the page is ready
    And I should see "Test book name s1"
    And I should not see "Test chat name s2"
    # No close button as we are not using JS this time
#    And I click on "#navigation-arrows[title='Course home']" "css_element"
    And I click on "C1" "text" in the "#page-navbar" "css_element"
    And I am on "Course 1" course homepage
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible

    And I click on tile "2"
    And I wait until the page is ready
    And I should not see "Test book name s1"
    And I should see "Test chat name s2"

    And I click on "C1" "text" in the "#page-navbar" "css_element"
    And I should not see "Test chat name s2"
    And I should not see "Test choice name hidden"
    And section "1" should be visible
    And section "2" should be visible
    And section "3" should be visible

#  @javascript
#  Scenario: Open section 1 then edit as teacher
#    When I log in as "teacher1"
#    And the following config values are set as admin:
#      | config | value | plugin |
#      | usejavascriptnav | 1 | format_tiles |
#    And I am on "Course 1" course homepage
#    And I click on tile "1"
#    And I wait until the page is ready
#    And I should see "Test book name s1"
#    And I should not see "Test chat name s2"
#    And "#editsectiontbtn-1" "css_element" should be visible
#    And I click on "#editsectiontbtn-1" "css_element"
#    And I should see "Test book name s1"
#    And I should see "Add an activity or resource"
