@mod @mod_qubitspage
Feature: Configure qubitspage appearance
  In order to change the appearance of the qubitspage resource
  As an admin
  I need to configure the qubitspage appearance settings

  Background:
    Given the following "courses" exist:
      | shortname | fullname   |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | name       | intro      | course | idnumber |
      | qubitspage     | PageName1  | PageDesc1  | C1     | PAGE1    |

  @javascript
  Scenario Outline: Hide and display qubitspage features
    Given I am on the "PageName1" "qubitspage activity editing" qubitspage logged in as admin
    And I expand all fieldsets
    And I set the field "<feature>" to "<value>"
    And I press "Save and display"
    Then I <shouldornot> see "<lookfor>" in the "region-main" "region"

    Examples:
      | feature                    | lookfor        | value | shouldornot |
      | Display qubitspage description   | PageDesc1      | 1     | should      |
      | Display qubitspage description   | PageDesc1      | 0     | should not  |
      | Display last modified date | Last modified: | 1     | should      |
      | Display last modified date | Last modified: | 0     | should not  |
