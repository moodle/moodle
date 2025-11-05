@mod @mod_board @javascript
Feature: Add and delete comments in mod_board

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | GA       |
      | Group B | C1     | GB       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "group members" exist:
      | user     | group |
      | student1 | GA    |
      | student2 | GB    |

  Scenario: All users may comment on all posts when single user mode disabled
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
      | Sample board | 1           | Heading S2 | Content S2  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on ".mod_board_note_content" "css_element" in the "Heading T1" "mod_board > note"
    And I should see "0 Comments" in the "Heading T1" "dialogue"
    And I type mod_board comment "First comment from teacher"
    And I click on "Add comment" "button" in the "Heading T1" "dialogue"
    Then I should see "1 Comments" in the "Heading T1" "dialogue"
    And I should see "First comment from teacher" in the "Heading T1" "dialogue"
    And I click on "Close" "button" in the "Heading T1" "dialogue"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "0 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Another comment from teacher"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "1 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on ".mod_board_note_content" "css_element" in the "Heading T1" "mod_board > note"
    And I should see "1 Comments" in the "Heading T1" "dialogue"
    And I type mod_board comment "First comment from Student 1"
    And I click on "Add comment" "button" in the "Heading T1" "dialogue"
    Then I should see "2 Comments" in the "Heading T1" "dialogue"
    And I should see "First comment from teacher" in the "Heading T1" "dialogue"
    And I should see "First comment from Student 1" in the "Heading T1" "dialogue"
    And I click on "Close" "button" in the "Heading T1" "dialogue"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "1 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Another comment from Student 1"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "2 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I should see "Another comment from Student 1" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And ".fa-trash-can" "css_element" should not exist in the "Another comment from teacher" "mod_board > comment"
    And I click on ".fa-trash-can" "css_element" in the "Another comment from Student 1" "mod_board > comment"
    Then I should see "1 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I should not see "Another comment from Student 1" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "teacher1"

    When I click on ".mod_board_note_content" "css_element" in the "Heading T1" "mod_board > note"
    And I should see "2 Comments" in the "Heading T1" "dialogue"
    And I click on ".fa-trash-can" "css_element" in the "First comment from Student 1" "mod_board > comment"
    And I should see "1 Comments" in the "Heading T1" "dialogue"
    And I click on ".fa-trash-can" "css_element" in the "First comment from teacher" "mod_board > comment"
    Then I should see "0 Comments" in the "Heading T1" "dialogue"
    And I click on "Close" "button" in the "Heading T1" "dialogue"

  Scenario: Owners and teachers may comment on all posts in private single user mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 1                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
      | Sample board | 1           | Heading S2 | Content S2  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I select "First Student" from the "Select user" singleselect

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "0 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Another comment from teacher"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "1 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "1 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Another comment from Student 1"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "2 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I should see "Another comment from Student 1" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And ".fa-trash-can" "css_element" should not exist in the "Another comment from teacher" "mod_board > comment"
    And I click on ".fa-trash-can" "css_element" in the "Another comment from Student 1" "mod_board > comment"
    Then I should see "1 Comments" in the "Heading S1" "dialogue"
    And I should see "Another comment from teacher" in the "Heading S1" "dialogue"
    And I should not see "Another comment from Student 1" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

  Scenario: Everybody may comment on all posts in public single user mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 2                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
      | Sample board | 1           | Heading S2 | Content S2  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I select "First Student" from the "Select user" singleselect

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "0 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Some comment from teacher"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "1 Comments" in the "Heading S1" "dialogue"
    And I should see "Some comment from teacher" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "student2"
    And I select "First Student" from the "Select user" singleselect

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "1 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Some comment from Student 2"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "2 Comments" in the "Heading S1" "dialogue"
    And I should see "Some comment from teacher" in the "Heading S1" "dialogue"
    And I should see "Some comment from Student 2" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "2 Comments" in the "Heading S1" "dialogue"
    And I type mod_board comment "Some comment from Student 1"
    And I click on "Add comment" "button" in the "Heading S1" "dialogue"
    Then I should see "3 Comments" in the "Heading S1" "dialogue"
    And I should see "Some comment from teacher" in the "Heading S1" "dialogue"
    And I should see "Some comment from Student 1" in the "Heading S1" "dialogue"
    And I should see "Some comment from Student 2" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And ".fa-trash-can" "css_element" should not exist in the "Some comment from teacher" "mod_board > comment"
    And I click on ".fa-trash-can" "css_element" in the "Some comment from Student 1" "mod_board > comment"
    Then I should see "2 Comments" in the "Heading S1" "dialogue"
    And I should see "Some comment from teacher" in the "Heading S1" "dialogue"
    And I should not see "Some comment from Student 1" in the "Heading S1" "dialogue"
    And I should see "Some comment from Student 2" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"

    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I select "First Student" from the "Select user" singleselect

    When I click on ".mod_board_note_content" "css_element" in the "Heading S1" "mod_board > note"
    And I should see "2 Comments" in the "Heading S1" "dialogue"
    And I click on ".fa-trash-can" "css_element" in the "Some comment from Student 2" "mod_board > comment"
    And I should see "1 Comments" in the "Heading S1" "dialogue"
    And I click on ".fa-trash-can" "css_element" in the "Some comment from teacher" "mod_board > comment"
    Then I should see "0 Comments" in the "Heading S1" "dialogue"
    And I click on "Close" "button" in the "Heading S1" "dialogue"
