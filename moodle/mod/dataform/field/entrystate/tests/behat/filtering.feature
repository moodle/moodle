@mod @mod_dataform @dataformfilter @dataformfield @dataformfield_entrystate
Feature: Filtering

    @javascript
    Scenario: Filtering
        Given a fresh site with dataform "Filtering entry state"

        #Section: Add an entry state field.
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Text field   | text          | dataform1 |

        And the following dataformfield entrystate exists:
            | dataform      | dataform1 |
            | name          | Status    |
            | states        | Submitted#Under review#Approved#Declined  |
            | from1         |           |
            | to1           |           |
            | permission1   |           |
            | notification1 |           |
        #:Section

        #Section: Add an aligned view.
        And the following dataform "views" exist:
            | name          | type      | dataform  | default   |
            | Aligned view  | aligned   | dataform1 | 1         |

        And view "Aligned view" in dataform "1" has the following entry template:
            """
            [[Text field]]
            [[Status]]
            """
        #:Section

        #Section: Add entries.
        And the following dataform "entries" exist:
            | dataform  | user          | state | Text field   |
            | dataform1 | teacher1      | 1     | Entry 01     |
            | dataform1 | teacher1      | 2     | Entry 02     |
            | dataform1 | teacher1      | 3     | Entry 03     |
            | dataform1 | teacher1      | 1     | Entry 04     |
            | dataform1 | teacher1      | 2     | Entry 05     |
            | dataform1 | teacher1      | 3     | Entry 06     |
            | dataform1 | teacher1      | 0     | Entry 07     |
        #:Section

        #Section: Log in.
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Filtering entry state"
        And I see "Entry 01"
        And I see "Entry 02"
        And I see "Entry 03"
        And I see "Entry 04"
        And I see "Entry 05"
        And I see "Entry 06"
        And I see "Entry 07"
        #:Section

        ### Quick filtering ###

        #Section: Quick search.
        #Then I set the field "usearch" to "Submitted"
        #And I press Enter on "usearch" "field"
        #And I see "Entry 07"
        #And I do not see "Entry 01"
        #And I do not see "Entry 02"
        #And I do not see "Entry 03"
        #And I do not see "Entry 04"
        #And I do not see "Entry 05"
        #And I do not see "Entry 06"
        #:Section

        ### Define and apply a standard filter ###

        #Section: Add filter: Last 2 entries
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "Last 2 Entries"
        And I set the field "Per page" to "2"
        And I set sort criterion "1" to "2,state" "1"
        And I press "Save changes"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "Last 2 Entries"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        And I see "Entry 03"
        And I do not see "Entry 04"
        And I do not see "Entry 05"
        And I see "Entry 06"
        And I do not see "Entry 07"
        #:Section

        #Section: Add filter: Status Under review
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "Status Under review"
        And I set search criterion "1" to "AND" "2,state" "" "=" "Under review"
        And I press "Save changes"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "Status Under review"
        And I see "Entry 01"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        And I see "Entry 04"
        And I do not see "Entry 05"
        And I do not see "Entry 06"
        And I do not see "Entry 07"
        #:Section
