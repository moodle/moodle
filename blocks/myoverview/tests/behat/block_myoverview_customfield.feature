@block @block_myoverview @javascript
Feature: The my overview block allows users to group courses by custom fields

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "custom field categories" exist:
      | name          | component   | area   | itemid |
      | Course fields | core_course | course | 0      |
    And the following "custom fields" exist:
      | name           | category      | type     | shortname     | configdata                                           |
      | Checkbox field | Course fields | checkbox | checkboxfield |                                                      |
      | Date field     | Course fields | date     | datefield     | {"mindate":0, "maxdate":0}                           |
      | Select field   | Course fields | select   | selectfield   | {"options":"Option 1\nOption 2\nOption 3\nOption 4"} |
      | Text field     | Course fields | text     | textfield     |                                                      |
    And the following "courses" exist:
      | fullname | shortname | category | customfield_checkboxfield | customfield_datefield | customfield_selectfield | customfield_textfield |
      | Course 1 | C1        | 0        | 1                         | 981028800             | 1                       | fish                  |
      | Course 2 | C2        | 0        | 0                         | 334324800             |                         |                       |
      | Course 3 | C3        | 0        | 0                         | 981028800             | 2                       | dog                   |
      | Course 4 | C4        | 0        | 1                         |                       | 3                       | cat                   |
      | Course 5 | C5        | 0        |                           | 334411200             | 2                       | fish                  |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |
      | student1 | C4     | student |
      | student1 | C5     | student |

  Scenario: Group courses by checkbox: Yes
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1             | block_myoverview |
      | customfiltergrouping       | checkboxfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "Checkbox field: Yes" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by checkbox: No
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1             | block_myoverview |
      | customfiltergrouping       | checkboxfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "Checkbox field: No" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by date: 1 February 2001
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | datefield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "1 February 2001" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by date: 6 August 1980
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | datefield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "6 August 1980" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by date: No Date field
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | datefield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "No Date field" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by select: Option 1
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1           | block_myoverview |
      | customfiltergrouping       | selectfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    And I should not see "Option 4" in the "Course overview" "block"
    When I click on "Option 1" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by select: Option 2
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1           | block_myoverview |
      | customfiltergrouping       | selectfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "Option 2" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by select: No Select field
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1           | block_myoverview |
      | customfiltergrouping       | selectfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "No Select field" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by text: fish
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | textfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "fish" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by text: dog
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | textfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "dog" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: Group courses by text: No Text field
    Given the following config values are set as admin:
      | displaygroupingcustomfield | 1         | block_myoverview |
      | customfiltergrouping       | textfield | block_myoverview |
    And I log in as "student1"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    When I click on "No Text field" "link" in the "Course overview" "block"
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
