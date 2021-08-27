@core @core_block
Feature: Block appearances
  In order to configure blocks appearance
  As a teacher
  I need to add and modify block configuration for the page

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name             | description              | course | idnumber | section |
      | survey   | Test survey name | Test survey description  | C1     | survey1  | 1       |
      | book     | Test book name   | Test book description    | C1     | book1    | 1       |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on "Test survey name" "link" in the "region-main" "region"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Survey type | ATTLS (20 item version) |
    And I press "Save and return to course"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Test book name" "link" in the "region-main" "region"
    And I set the following fields to these values:
      | Chapter title | Book title |
      | Content       | Book content test test |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Comments" block
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"

  Scenario: Block settings can be modified so that a block apprears on any page
    When I click on "Test survey name" "link" in the "region-main" "region"
    Then I should see "Comments" in the "Comments" "block"
    And I am on "Course 1" course homepage
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Display on page types | Any course page |
    And I press "Save changes"
    And I turn editing mode off
    And I click on "Test survey name" "link" in the "region-main" "region"
    And I should not see "Comments"

  Scenario: Block settings can be modified so that a block can be hidden
    When I click on "Test book name" "link" in the "region-main" "region"
    And I configure the "Comments" block
    And I set the following fields to these values:
      | Visible | No |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode off
    And I click on "Test book name" "link" in the "region-main" "region"
    Then I should not see "Comments"
