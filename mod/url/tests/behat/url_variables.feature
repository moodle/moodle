@mod @mod_url @javascript
Feature: Manage URL variables
  In order to maintain privacy for URLs
  As a teacher
  I need to be able to manage URL variables safely

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |

  Scenario: Disabling URL variables hides Role names as URL variables check box
    Given the following config values are set as admin:
      | allowvariables | 1 | url |
    And I log in as "admin"
    And I navigate to "Plugins > Activity modules > URL" in site administration
    When I click on "Allow URL variables" "checkbox"
    Then I should not see "Role names as URL variables"
    And I click on "Allow URL variables" "checkbox"
    And I should see "Role names as URL variables"

  Scenario: Disable the use of URL variables
    Given the following config values are set as admin:
      | allowvariables | 0 | url |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" using the activity chooser
    Then I should not see "URL variables"

  Scenario: Enable the use of URL variables without role names
    Given the following config values are set as admin:
      | allowvariables | 1 | url |
      | rolesinparams  | 0 | url |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" using the activity chooser
    Then I should see "URL variables"
    And I expand all fieldsets
    And I should see "Full site name" in the "id_variable_0" "select"
    But I should not see "Roles" in the "id_variable_0" "select"

  Scenario: Enable the use of URL variables with role names
    Given the following config values are set as admin:
      | allowvariables | 1 | url |
      | rolesinparams  | 1 | url |
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" using the activity chooser
    Then I should see "URL variables"
    And I expand all fieldsets
    And I should see "Full site name" in the "id_variable_0" "select"
    And I should see "Your word for 'Student'" in the "id_variable_0" "select"
