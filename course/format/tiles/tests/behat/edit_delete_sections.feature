@format @format_tiles @edit_delete_sections @javascript
Feature: Sections can be edited and deleted in tiles format
  In order to rearrange my course contents
  As a teacher
  I need to edit and Delete tiles

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections |
      | Edit Delete Secs Course | C1        | tiles  | 0             | 5           |
    And the following "activities" exist:
      | activity | name                 | intro                       | course | idnumber | section |
      | assign   | Test assignment name | Test assignment description | C1     | assign1  | 0       |
      | forum    | Announcements Sec 0  | Test forum description      | C1     | forum1   | 0       |
      | book     | Test book name       | Test book description       | C1     | book1    | 1       |
      | chat     | Test chat name       | Test chat description       | C1     | chat1    | 4       |
      | choice   | Test choice name     | Test choice description     | C1     | choice1  | 4       |
      | choice   | Test choice name 2   | Test choice description     | C1     | choice2  | 5       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

    And I log in as "teacher1"
    And I am on "Edit Delete Secs Course" course homepage with editing mode on
    And I wait until the page is ready
    And I wait "1" seconds

  Scenario: View the default name of the second section in tiles format
    And I edit the section "2"
    And I wait until the page is ready
    Then the field "Custom" matches value "0"
    And the field "New value for Section name" matches value "Tile 2"

  Scenario: Edit section default name in tiles format
    And I edit the section "2" and I fill the form with:
      | Custom                     | 1                       |
      | New value for Section name | This is the second Tile |
    Then I should see "This is the second Tile" in the "li#section-2" "css_element"
    And I should not see "Tile 2" in the "li#section-2" "css_element"

  @javascript
  Scenario: Inline edit section name in tiles format
    When I click on "Edit tile name" "link" in the "li#section-1" "css_element"
    And I set the field "New name for topic Tile 1" to "Setting up in business"
    And I press the enter key
    Then I should not see "Tile 1" in the "region-main" "region"
    And I should see "Setting up in business" in the "li#section-1" "css_element"
    And I am on "Edit Delete Secs Course" course homepage
    And I should not see "Tile 1" in the "region-main" "region"
    And I should see "Setting up in business" in the "li#section-1" "css_element"

  Scenario: Deleting the last section in tiles format
    And I wait "1" seconds
    And I delete section "5"
    Then I should see "Are you absolutely sure you want to completely delete \"Tile 5\" and all the activities it contains?"
    And I press "Delete"
    And I should not see "Tile 5"
    And I should see "Tile 4"

  Scenario: Deleting the middle section in tiles format
    And I wait "1" seconds
    And I delete section "4"
    And I press "Delete"
    Then I should not see "Tile 5"
    And I should see "Tile 4"

  @javascript
  Scenario: Adding sections in tiles format
#    Increase by 1 tile
#    And I wait "1" seconds
#    And I follow "Add tiles"
#    And I wait until the page is ready
#    And I wait "1" seconds
#    Then the field "Number of sections" matches value "1"
#    And I press "Add tiles"
#    And I should see "Tile 6" in the "li#section-6" "css_element"
#    And "li#section-7" "css_element" should not exist
#todo fix this test - not working as menu item at bottom of course now says add tiles too

#    Increase by 3 more tiles
#    And I follow "Add tiles"
#    And I wait until the page is ready
#    And I wait "1" seconds
#    And I set the field "Number of sections" to "3"
#    And I press "Add tiles"
#    And I wait until the page is ready
#    And I should see "Tile 7" in the "li#section-7" "css_element"
#    And I should see "Tile 8" in the "li#section-8" "css_element"
#    And I should see "Tile 9" in the "li#section-9" "css_element"
#    And "li#section-10" "css_element" should not exist
