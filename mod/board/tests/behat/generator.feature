@mod @mod_board @javascript
Feature: Use mod_board generator to create test data

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions | groupmode | groupmodeforce |
      | Course 1 | C1        | 1                | 1                        | 0         | 0              |
      | Course 2 | C2        | 1                | 1                        | 2         | 1              |
      | Course 3 | C3        | 1                | 1                        | 1         | 1              |
    And the following "groups" exist:
      | name     | course | idnumber  |
      | Group AV | C2     | GAV       |
      | Group BV | C2     | GBV       |
      | Group AS | C3     | GAS       |
      | Group BS | C3     | GBS       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C3     | student        |
      | student2 | C3     | student        |
      | teacher1 | C3     | editingteacher |
    And the following "group members" exist:
      | user     | group  |
      | student1 | GAV    |
      | student2 | GBV    |
      | student1 | GAS    |
      | student2 | GBS    |

  Scenario: Use generator to create mod_board instance
    When the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | intro          | Sample test baord intro |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    Then I should see "Sample test baord intro"

  Scenario: Use generator to create columns in mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
    When the following "mod_board > columns" exist:
      | board        | name      |
      | Sample board | Sloupec 4 |
      | Sample board | Sloupec 5 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    Then I should see "Sloupec 4"
    And I should see "Sloupec 5"

  Scenario: Use generator to create posts in general mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
    When the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     |
      | Sample board | 1           | Heading 1x1 |             | student1 |
      | Sample board | 2           |             | Content 2x2 | teacher1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    Then I should see "Heading 1x1" in the "1" "mod_board > column"
    And I should see "Content 2x2" in the "2" "mod_board > column"

  Scenario: Use generator to create posts in private single user mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | singleusermode | 1                      |
    When the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | owner    |
      | Sample board | 1           | Heading AAA | Content AAA | teacher1 | student1 |
      | Sample board | 1           | Heading BBB | Content BBB | teacher1 | student2 |

    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    Then I should not see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"
    And I select "First Student" from the "Select user" singleselect
    And I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"

    And I am on the "Sample board" "board activity" page logged in as "student1"
    Then I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"

  Scenario: Use generator to create posts in visible groups mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C2                     |
      | name           | Sample board           |
    When the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | group     |
      | Sample board | 1           | Heading AAA | Content AAA | teacher1 | GAV       |
      | Sample board | 1           | Heading BBB | Content BBB | teacher1 | GBV       |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"

    Then I should see "Heading AAA" in the "1" "mod_board > column"
    And I should see "Heading BBB" in the "1" "mod_board > column"
    And I select "Group AV" from the "Visible groups" singleselect
    And I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"

    And I am on the "Sample board" "board activity" page logged in as "student1"
    Then I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"
    And I select "Group BV" from the "Visible groups" singleselect
    And I should not see "Heading AAA" in the "1" "mod_board > column"
    And I should see "Heading BBB" in the "1" "mod_board > column"
    And I select "All participants" from the "Visible groups" singleselect
    And I should see "Heading AAA" in the "1" "mod_board > column"
    And I should see "Heading BBB" in the "1" "mod_board > column"

  Scenario: Use generator to create posts in separate groups mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C3                     |
      | name           | Sample board           |
    When the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | group     |
      | Sample board | 1           | Heading AAA | Content AAA | teacher1 | GAS       |
      | Sample board | 1           | Heading BBB | Content BBB | teacher1 | GBS       |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"

    Then I should see "Heading AAA" in the "1" "mod_board > column"
    And I should see "Heading BBB" in the "1" "mod_board > column"
    And I select "Group AS" from the "Separate groups" singleselect
    And I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"

    And I am on the "Sample board" "board activity" page logged in as "student1"
    Then I should see "Heading AAA" in the "1" "mod_board > column"
    And I should not see "Heading BBB" in the "1" "mod_board > column"

  Scenario: Use generator to create post comments in mod_board
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content    | user     |
      | Sample board | 1           | Heading T1 | Content T1 | student1 |
    When the following "mod_board > comments" exist:
      | note       | content      | user     |
      | Heading T1 | Comment T1x1 | teacher1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I click on ".mod_board_note_content" "css_element" in the "Heading T1" "mod_board > note"
    And I should see "1 Comments" in the "Heading T1" "dialogue"
    And I should see "Comment T1x1"

    And I click on "Close" "button" in the "Heading T1" "dialogue"
    And I am on homepage

  Scenario: Use generator to create templates in mod_board
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
    When the following "mod_board > templates" exist:
      | name        |
      | Template 01 |
    And the following "mod_board > templates" exist:
      | name        | description        | columns                    | contextlevel | reference | singleusermode | sortby |
      | Template 02 | Second description | Col 1\nCol 2\nCol 3\nCol 4 | Category     | cata      | 1              | 3      |
    And I log in as "admin"
    And I navigate to "Plugins > Activity modules > Board > Board templates" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description | Category | Columns | Settings      |
      | Template 01 |                      | System   |         |               |
      | Template 02 | Second description   | Cat A    | Col 1   | Sort by: None |
