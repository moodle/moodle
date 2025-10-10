@block @block_navigation
Feature: Test that admin can see related nodes in Administration block
  In order to manage
  As an admin
  I need to be able to see related nodes in Administration block

  Background:
    Given the following "categories" exist:
      | name   | category | idnumber | visible |
      | cat1   | 0        | cat1     | 1       |
    And the following "courses" exist:
      | fullname | shortname | category | visible |
      | Course 1 | c1        | cat1     | 1       |
    And the following config values are set as admin:
      | unaddableblocks |  | theme_boost |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Administration" block if not present
    And I configure the "Administration" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"

  @javascript
  Scenario: As admin I must not see question related nodes in Administration.
    Given the following "activities" exist:
      | activity | name   | intro                           | course | idnumber |
      | quiz     | Quiz 1 | Quiz 1 for testing the Add menu | c1     | quiz1    |
    And I am on "Course 1" course homepage
    Then I should see "Question bank"
    And I should not see "Questions"
    And I am on the "Quiz 1" "mod_quiz > view" page
    And "Question bank" "link" should exist
    And "Questions" "link" should exist
    And "Categories" "link" should exist
