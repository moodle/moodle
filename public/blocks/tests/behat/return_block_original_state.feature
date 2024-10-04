@core @core_block
Feature: The context of a block can always be returned to it's original state.
  In order to revert actions when configuring blocks
  As an admin
  I need to be able to return the block to original state

  Scenario: Add and configure a block to display on every page and revert back
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "activities" exist:
      | activity   | name           | intro         | course | section | idnumber |
      | assign     | Assignment1    | Description   | C1     | 1       | assign1 |
      | assign     | Assignment2    | Description   | C1     | 1       | assign1 |
    And I log in as "admin"
    When I am on "Course 1" course homepage with editing mode on
    And I add the "Tags" block
    Then I should see "Tags" in the "Tags" "block"
    And I navigate to course participants
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I follow "Assignment1"
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any assignment module page |
    And I press "Save changes"
    And I should see "Tags" in the "Tags" "block"
    And I am on "Course 1" course homepage
    And "Tags" "block" should not exist
    And I navigate to course participants
    And "Tags" "block" should not exist
    And I am on "Course 1" course homepage
    And I follow "Assignment2"
    And I should see "Tags" in the "Tags" "block"
    And I configure the "Tags" block
    And I set the following fields to these values:
      | Display on page types | Any page |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I should see "Tags" in the "Tags" "block"
    And I navigate to course participants
    And I should see "Tags" in the "Tags" "block"
