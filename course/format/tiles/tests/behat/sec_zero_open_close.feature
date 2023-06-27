@format @format_tiles @sec_zero_open_close @javascript
Feature: Section zero can be collapsed or expanded in tiles format
  In order to hide irrelevant material
  As a student
  I need to expand and collapse section zero

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Course 1 | C1        | tiles  | 0             | 5           |
    And the following "activities" exist:
      | activity | name                 | intro                       | course | idnumber | section |
      | assign   | Test assignment name | Test assignment description | C1     | assign1  | 0       |
      | forum    | Announcements Sec 0  | Test forum description      | C1     | forum1   | 0       |
      | book     | Test book name       | Test book description       | C1     | book1    | 1       |
      | chat     | Test chat name       | Test chat description       | C1     | chat1    | 4       |
      | choice   | Test choice name     | Test choice description     | C1     | choice1  | 5       |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | usejavascriptnav       | 1        | format_tiles |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |

    And I log in as "student1"
    And I am on "Course 1" course homepage

  @javascript
  Scenario: Collapse section zero then expand it again
    And I wait "2" seconds
    When "#buttonhidesec0" "css_element" should be visible
    And I click on "#buttonhidesec0" "css_element"
    And I wait "1" seconds
    And I should not see "Announcements Sec 0" in the "#section-0" "css_element"

    And "#buttonhidesec0" "css_element" should be visible
    And I click on "#buttonhidesec0" "css_element"
    And I wait "1" seconds
    And I should see "Announcements Sec 0" in the "#section-0" "css_element"
    And I log out tiles
