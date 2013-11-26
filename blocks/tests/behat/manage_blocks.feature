@core @core_block
Feature: Block appearances
  In order to configure blocks appearance
  As a teacher
  I need to add and modify block configuration for the page

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | teacher | 1 | teacher1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I expand "Site administration" node
    And I expand "Appearance" node
    And I expand "Themes" node
    And I follow "Theme selector"
    And I click on "Change theme" "button" in the "Default" "table_row"
    And I click on "Use theme" "button" in the "Afterburner" "table_row"
    And I press "Continue"
    And I am on homepage
    And I follow "Course 1"
    And I follow "Turn editing on"
    And I add a "Survey" to section "1" and I fill the form with:
      | Name | Test survey name |
      | Survey type | ATTLS (20 item version) |
      | Description | Test survey description |
    And I add a "Book" to section "1" and I fill the form with:
      | Name | Test book name |
      | Description | Test book description |
    And I follow "Test book name"
    And I fill the moodle form with:
      | Chapter title | Book title |
      | Content       | Book content test test |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Turn editing on"
    And I add the "Comments" block
    And I follow "Configure Comments block"
    And I fill the moodle form with:
      | Display on page types | Any page |
    And I press "Save changes"

  @javascript
  Scenario: Block settings can be modified so that a block apprears on any page
    When I follow "Test survey name"
    Then I should see "Comments" in the "Comments" "block"
    And I follow "Course 1"
    And I follow "Configure Comments block"
    And I fill the moodle form with:
      | Display on page types | Any course page |
    And I press "Save changes"
    And I follow "Turn editing off"
    And I follow "Test survey name"
    And I should not see "Comments"

  @javascript
  Scenario: Block settings can be modified so that a block can be hidden or moved
    When I follow "Test book name"
    And I follow "Configure Comments block"
    And I fill the moodle form with:
      | Visible | No |
    And I press "Save changes"
    And I follow "Turn editing off"
    And I follow "Test book name"
    Then I should not see "Comments"
    And I expand "Course administration" node
    And I follow "Turn editing on"
    And I follow "Configure Comments block"
    And I fill the moodle form with:
      | Visible | Yes |
      | Region  | Right |
    And I press "Save changes"
    And I should see "Comments" in the "//*[@id='region-post' or @id='block-region-side-post']" "xpath_element"
