@format @format_tiles @format_tiles_data_preference @javascript
Feature: user can select whether or not data is stored in browser
  In order to maintain privacy
  As a user
  I need to set this once on log in and if press the button

  Background:
    Given the following "users" exist:
      | username          | firstname | lastname | email             |
      | student-datapref1 | Student   | 1        | user1@example.com |
    And the following "courses" exist:
      | fullname           | shortname | format | coursedisplay | numsections |
      | Data Pref Course 1 | C1        | tiles  | 0             | 5           |
      | Data Pref Course 2 | C2        | tiles  | 0             | 5           |
    And the following "activities" exist:
      | activity | name                 | intro                       | course | idnumber | section |
      | assign   | Test assignment name | Test assignment description | C1     | assign1  | 0       |
      | forum    | Announcements Sec 0  | Test forum description      | C1     | forum1   | 0       |
      | book     | Test book name       | Test book description       | C1     | book1    | 1       |
      | chat     | Test chat name       | Test chat description       | C1     | chat1    | 4       |
      | choice   | Test choice name     | Test choice description     | C1     | choice1  | 5       |
    And the following "course enrolments" exist:
      | user              | course | role    |
      | student-datapref1 | C1     | student |
      | student-datapref1 | C2     | student |
    And the following config values are set as admin:
      | config                 | value | plugin       |
      | assumedatastoreconsent | 0     | format_tiles |
      | reopenlastsection      | 0     | format_tiles |
      | usejavascriptnav       | 1     | format_tiles |

    And I log in as "student-datapref1"

  @javascript
  Scenario: Accept Data Preference
    When I am on "Data Pref Course 1" course homepage
    And I wait until the page is ready
    And I wait "3" seconds
    And "Data preference" "dialogue" should be visible
    And "Yes" "button" should exist in the "Data preference" "dialogue"
    And "Cancel" "button" should exist in the "Data preference" "dialogue"
    And I click on "Yes" "button" in the "Data preference" "dialogue"

    # Visit another course to check no data preference box
    And I am on "Data Pref Course 2" course homepage
    And I wait until the page is ready
    And "Data preference" "dialogue" should not be visible

    # Visit Data Pref Course 1 again to check no data preference box
    And I am on "Data Pref Course 1" course homepage
    And I wait until the page is ready
    And "Data preference" "dialogue" should not be visible
    And I click on tile "1"

# TODO put this back in
##  @javascript
#  Scenario: Manually switch off data pref using menu item
#    When I am on "Data Pref Course 1" course homepage
#    And I wait until the page is ready
#    And I click on "Data preference" "link" in the "nav-drawer" "region"
#    And I wait until the page is ready
#    And I wait "3" seconds
#    And "Data preference" "dialogue" should be visible
#    And I click on "Cancel" "button" in the "Data preference" "dialogue"
#    And I log out tiles
