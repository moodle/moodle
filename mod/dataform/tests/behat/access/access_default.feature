@mod @mod_dataform @dataformactivity @dataformaccess
Feature: Dataform access permissions

    @javascript
    Scenario: Default access
        Given I start afresh with dataform "Test access default"

        ## Field
        And the following dataform "fields" exist:
            | name          | type          | dataform  |
            | Field Text    | text          | dataform1 |

        ## View
        And the following dataform "views" exist:
            | name          | type      | dataform  | default   |
            | View Aligned  | aligned   | dataform1 | 1         |


        And view "View Aligned" in dataform "1" has the following view template:
            """
            <div>##addnewentry##</div>
            <div>Num entries total: ##numentriestotal##</div>
            <div>Num entries max: ##numentriesviewable##</div>
            <div>Num entries filtered: ##numentriesfiltered##</div>
            <div>Num entries displayed: ##numentriesdisplayed##</div>
            <div>##entries##</div>
            """

        ## Entries
        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  | Field Text                |
            | dataform1 | teacher1      |       |               |               | 1 Entry by Teacher 01     |
            | dataform1 | assistant1    |       |               |               | 2 Entry by Assistant 01   |
            | dataform1 | student1      |       |               |               | 3 Entry by Student 01     |
            | dataform1 | student2      |       |               |               | 4 Entry by Student 02     |
            | dataform1 | student3      |       |               |               | 5 Entry by Student 03     |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test access default"

        # Teacher access

        # View
        Then I see "Num entries total: 5"
        And I see "Num entries max: 5"
        And I see "Num entries filtered: 5"
        And I see "Num entries displayed: 5"
        And I see "1 Entry by Teacher 01"
        And I see "2 Entry by Assistant 01"
        And I see "3 Entry by Student 01"
        # ... same for other entries

        # Add
        And I see "Add a new entry"

        # Update
        And "Edit" "link" should exist in the "1 Entry by" "table_row"
        And "Edit" "link" should exist in the "2 Entry by" "table_row"
        And "Edit" "link" should exist in the "3 Entry by" "table_row"
        # ... same for other entries

        # Delete
        And "Delete" "link" should exist in the "1 Entry by" "table_row"
        And "Delete" "link" should exist in the "2 Entry by" "table_row"
        And "Delete" "link" should exist in the "3 Entry by" "table_row"
        # ... same for other entries

        And I log out

        # Assistant access
        #---------------------------
        Given I log in as "assistant1"
        And I follow "Course 1"
        And I follow "Test access default"

        # View
        Then I see "Num entries total: 5"
        And I see "Num entries max: 5"
        And I see "Num entries filtered: 5"
        And I see "Num entries displayed: 5"
        And I see "1 Entry by Teacher 01"
        And I see "2 Entry by Assistant 01"
        And I see "3 Entry by Student 01"
        # ... same for other entries

        # Add
        And I see "Add a new entry"

        # Update
        And "Edit" "link" should exist in the "1 Entry by" "table_row"
        And "Edit" "link" should exist in the "2 Entry by" "table_row"
        And "Edit" "link" should exist in the "3 Entry by" "table_row"
        # ... same for other entries

        # Delete
        And "Delete" "link" should exist in the "1 Entry by" "table_row"
        And "Delete" "link" should exist in the "2 Entry by" "table_row"
        And "Delete" "link" should exist in the "3 Entry by" "table_row"
        # ... same for other entries

        And I log out

        # Student 1 access
        #---------------------------
        Given I log in as "student1"
        And I follow "Course 1"
        And I follow "Test access default"

        # View
        Then I see "Num entries total: 5"
        And I see "Num entries max: 5"
        And I see "Num entries filtered: 5"
        And I see "Num entries displayed: 5"

        And I see "1 Entry by Teacher 01"
        And I see "2 Entry by Assistant 01"
        And I see "3 Entry by Student 01"
        And I see "4 Entry by Student 02"
        # ... same for other entries

        # Add/Update/delete own entry
        And "Edit" "link" should exist in the "3 Entry by" "table_row"
        And "Delete" "link" should exist in the "3 Entry by" "table_row"
        And I follow "Add a new entry"
        And I set the field "field_1_-1" to "My new entry in standard access"
        And I press "Save"
        And I see "My new entry in standard access"
        And I click on "Edit" "link" in the "My new entry in standard access" "table_row"
        And I set the field "field_1_6" to "My new entry in standard access - updated"
        And I press "Save"
        And I see "My new entry in standard access - updated"
        And I see "Num entries total: 6"
        And I click on "Delete" "link" in the "My new entry in standard access - updated" "table_row"
        And I see "You are about to delete 1 entry(s)"
        And I press "Continue"
        Then I see "Num entries total: 5"
        And I do not see "My new entry in standard access - updated"

        # Cannot update others' entries
        And I cannot edit entry "1" in dataform "1" view "1"
        And I cannot edit entry "2" in dataform "1" view "1"
        And I cannot edit entry "4" in dataform "1" view "1"
        # ... same for other entries

        # Cannot delete others' entries
        And I cannot delete entry "1" with content "1 Entry by Teacher 01" in dataform "1" view "1"
        And I cannot delete entry "2" with content "2 Entry by Assistant 01" in dataform "1" view "1"
        And I cannot delete entry "4" with content "4 Entry by Student 02" in dataform "1" view "1"
        # ... same for other entries
