@mod @mod_page
Feature: Edit page resource settings
  In order to configure the page resource
  As a teacher
  I need to be able to edit its settings

  Background:
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    |

  @javascript @accessibility
  Scenario: Check the accessibility of the page activity editing page
    Given I am on the "PageName1" "page activity editing" page logged in as admin
    Then the page should meet accessibility standards
