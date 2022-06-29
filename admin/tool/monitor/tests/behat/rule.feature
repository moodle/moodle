@javascript @tool @tool_monitor @tool_monitor_rules
Feature: tool_monitor_rule
  In order to manage rules
  As an admin
  I need to create a rule, edit a rule, duplicate a rule and delete a rule

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Reports > Event monitoring rules" in site administration
    And I click on "Enable" "link"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    And I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule course level                             |
      | plugin               | Forum                                             |
      | eventname            | Post created                                      |
      | id_description       | I want a rule to monitor posts created on a forum |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The forum post was created. {modulelink}          |
    And I press "Save changes"
    And I navigate to "Reports > Event monitoring rules" in site administration
    And I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule site level                               |
      | plugin               | Forum                                             |
      | eventname            | Post created                                      |
      | id_description       | I want a rule to monitor posts created on a forum |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The forum post was created. {modulelink}          |
    And I press "Save changes"
    And I log out

  Scenario: Add a rule on course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    When I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule                                          |
      | plugin               | Forum                                             |
      | eventname            | Post created                                      |
      | id_description       | I want a rule to monitor posts created on a forum |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The forum post was created. {modulelink}          |
    And I press "Save changes"
    Then "New rule" row "Course" column of "toolmonitorrules_table" table should contain "Course 1"
    And I should see "I want a rule to monitor posts created on a forum"
    And I should see "Forum"
    And I should see "Post created"
    And I should see "1 time(s) in 1 minute(s)"

  Scenario: Delete a rule on course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    When I click on "Delete rule" "link"
    Then I should see "Are you sure you want to delete the rule \"New rule course level\"?"
    And I press "Continue"
    And I should see "Rule successfully deleted"
    And I should not see "New rule course level"

  Scenario: Edit a rule on course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    When I click on "Edit rule" "link"
    And I set the following fields to these values:
      | name                 | New rule quiz                                  |
      | plugin               | Quiz                                           |
      | eventname            | Quiz attempt deleted                           |
      | id_description       | I want a rule to monitor quiz attempts deleted |
      | frequency            | 5                                              |
      | minutes              | 5                                              |
      | Notification message | Quiz attempt deleted. {modulelink}             |
    And I press "Save changes"
    Then I should see "New rule quiz"
    And I should see "I want a rule to monitor quiz attempts deleted"
    And I should see "Quiz attempt deleted"
    And I should see "5 time(s) in 5 minute(s)"

  Scenario: Duplicate a rule on course level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    When I click on "Duplicate rule" "link" in the "New rule course level" "table_row"
    Then I should see "Rule successfully duplicated"
    And "#toolmonitorrules_r1" "css_element" should appear before "#toolmonitorrules_r2" "css_element"
    And I should see "New rule"
    And I should see "I want a rule to monitor posts created on a forum"
    And I should see "Forum"
    And I should see "Post created"
    And I should see "1 time(s) in 1 minute(s)"

  Scenario: Add a rule on site level
    Given I log in as "admin"
    And I navigate to "Reports > Event monitoring rules" in site administration
    When I press "Add a new rule"
    And I set the following fields to these values:
      | name                 | New rule                                          |
      | plugin               | Forum                                             |
      | eventname            | Post created                                      |
      | id_description       | I want a rule to monitor posts created on a forum |
      | frequency            | 1                                                 |
      | minutes              | 1                                                 |
      | Notification message | The forum post was created. {modulelink}          |
    And I press "Save changes"
    Then "New rule" row "Course" column of "toolmonitorrules_table" table should contain "Site"
    And I should see "I want a rule to monitor posts created on a forum"
    And I should see "Forum"
    And I should see "Post created"
    And I should see "1 time(s) in 1 minute(s)"

  Scenario: Delete a rule on site level
    Given I log in as "admin"
    And I navigate to "Reports > Event monitoring rules" in site administration
    When I click on "Delete rule" "link"
    Then I should see "Are you sure you want to delete the rule \"New rule site level\"?"
    And I press "Continue"
    And I should see "Rule successfully deleted"
    And I should not see "New rule site level"

  Scenario: Edit a rule on site level
    Given I log in as "admin"
    And I navigate to "Reports > Event monitoring rules" in site administration
    When I click on "Edit rule" "link"
    And I set the following fields to these values:
      | name                 | New Rule Quiz                                  |
      | plugin               | Quiz                                           |
      | eventname            | Quiz attempt deleted                           |
      | id_description       | I want a rule to monitor quiz attempts deleted |
      | frequency            | 5                                              |
      | minutes              | 5                                              |
      | Notification message | Quiz attempt deleted. {modulelink}             |
    And I press "Save changes"
    Then I should see "New Rule Quiz"
    And I should see "I want a rule to monitor quiz attempts deleted"
    And I should see "Quiz attempt deleted"
    And I should see "5 time(s) in 5 minute(s)"

  Scenario: Duplicate a rule on site level
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Event monitoring rules" "link"
    When I click on "Duplicate rule" "link" in the "New rule site level" "table_row"
    Then I should see "Rule successfully duplicated"
    And "#toolmonitorrules_r2" "css_element" should appear after "#toolmonitorrules_r1" "css_element"
    And I should see "I want a rule to monitor posts created on a forum"
    And I should see "Forum"
    And I should see "Post created"
    And I should see "1 time(s) in 1 minute(s)"
