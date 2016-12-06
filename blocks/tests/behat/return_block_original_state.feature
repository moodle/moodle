@core @core_block
Feature: The context of a block can always be returned to it's original state.
  In order to revert actions when configuring blocks
  As an admin
  I need to be able to return the block to original state

  Scenario: Add and configure a block to display on every page and revert back
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I am on site homepage
    When I follow "Course 1"
    And I follow "Turn editing on"
    And I add the "Tags" block
    Then I should see "Tags" in the "Tags" "block"
    And I navigate to course participants
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"
    And I follow "Course 1"
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Assignment1 |
      | Description | Description |
    And I follow "Assignment1"
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any assignment module page |
    And I press "Save changes"
    And I should see "Tags" in the "Tags" "block"
    And I follow "Course 1"
    And "Tags" "block" should not exist
    And I navigate to course participants
    And "Tags" "block" should not exist
    And I follow "Course 1"
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Assignment2 |
      | Description | Description |
    And I follow "Assignment2"
    And I should see "Tags" in the "Tags" "block"
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"
    And I follow "Course 1"
    And I should see "Tags" in the "Tags" "block"
    And I navigate to course participants
    And I should see "Tags" in the "Tags" "block"
