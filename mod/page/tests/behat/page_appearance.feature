@mod @mod_page
Feature: Configure page appearance
  In order to change the appearance of the page resource
  As an admin
  I need to configure the page appearance settings

  Background:
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | page     | PageName1  | PageDesc1  | C1     | PAGE1    |

  @javascript
  Scenario Outline: Hide and display page features
    Given I am on the "PageName1" "page activity editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Display page name" to "<value>"
    And I press "Save and display"
    Then I <shouldornot> see "PageName1" in the "region-main" "region"

    Examples:
      | feature                    | lookfor        | value | shouldornot |
      | Display page name          | PageName1      | 1     | should      |
      | Display page name          | PageName1      | 0     | should not  |
      | Display page description   | PageDesc1      | 1     | should      |
      | Display page description   | PageDesc1      | 0     | should not  |
      | Display last modified date | Last modified: | 1     | should      |
      | Display last modified date | Last modified: | 0     | should not  |
