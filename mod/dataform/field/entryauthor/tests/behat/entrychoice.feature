@mod @mod_dataform @dataformfield @dataformfield_entryauthor
Feature: Entry choice
    With this feature the teacher1 can allow students to take ownership
    on a predefined entry. For example, the teacher1 can add project entries,
    for different project and instead of assigning the projects to the students,
    allow the students to choose their preferred project.

    Background:
        Given a fresh site for dataform scenario

        #Section: Add activity 'Entry choice'.
        And the following "activities" exist:
            | activity | course | idnumber  | name              | maxentries |
            | dataform | C1     | dataform1 | Entry choice    | 2          |
        #:Section

        #Section: Add fields.
        And the following dataform "fields" exist:
            | name          | type      | dataform  | editable |
            | Description   | textarea  | dataform1 | 0        |
            | From          | time      | dataform1 | 0        |
            | To            | time      | dataform1 | 0        |
        #:Section

        #Section: Add filters.
        And the following dataform "filters" exist:
            | name          | dataform  | searchoptions                 |
            | My slots      | dataform1 | AND,EAU,currentuser,NOT,,      |
        #:Section

        #Section: Add views.
        And the following dataform "views" exist:
            | name            | type    | dataform  | default   | visible | filterid | submission |
            | Select entries  | aligned | dataform1 | 0         | 1       |          |            |

        And the following dataform "views" exist:
            | name            | type    | dataform  | default   | visible | filterid |
            | Project entries | aligned | dataform1 | 1         | 1       | 1        |

        And the following dataform "views" exist:
            | name        | type    | dataform  | default   | visible |
            | Management  | aligned | dataform1 | 0         | 0       |
        #:Section

        #Section: View 'Management' templates.
        And view "Management" in dataform "1" has the following entry template:
            """
            [[EAU:picture]]
            [[EAU:name]]
            [[Description]]|Description
            [[From]]|From
            [[To]]|To
            [[EAC:edit]]
            [[EAC:delete]]
            """
        #:Section

        #Section: View 'Select entries' templates.
        And view "Select entries" in dataform "1" has the following view template:
            """
            <h3>Select entries</h3>
            <p>Click on an 'Assign me' button to assign yourself to an entry.</p>
            <p>If you wish to change your selection, click on the 'Unassign me' button,
            and then select another entry.</p>
            ##entries##
            """
        And view "Select entries" in dataform "1" has the following entry template:
            """
            [[Description]]|Description
            [[From]]|From
            [[To]]|To
            [[EAU:assignme]]
            """
        #:Section

        #Section: View 'Project entries' templates.
        And view "Project entries" in dataform "1" has the following view template:
            """
            <h3>Project entries</h3>
            <p>With your selected entries, proceed to complete and submit your project.</p>
            <p>If you have not yet selected entries, please go to ##viewlink:Select entries## view
            to select your project entries.</p>
            ##entries##
            """
        And view "Project entries" in dataform "1" has the following entry template:
            """
            [[EAU:picture]]
            [[EAU:name]]
            [[!Description]]|Description
            [[!From]]|From
            [[!To]]|To
            [[EAC:edit]]
            """
        #:Section


    @javascript
    Scenario: A student can assign him/her self to an entry.

        Given the following dataform "entries" exist:
            | dataform  | user           | Description       | From              | To                |
            | dataform1 | teacher1       | Slot 1            | 2014-12-05 08:00  | 2014-12-05 16:00  |
            | dataform1 | teacher1       | Slot 2            | 2014-12-05 16:00  | 2014-12-06 00:00  |
            | dataform1 | teacher1       | Slot 3            | 2014-12-06 00:00  | 2014-12-06 08:00  |
            | dataform1 | teacher1       | Slot 4            | 2014-12-06 08:00  | 2014-12-06 16:00  |
            | dataform1 | teacher1       | Slot 5            | 2014-12-06 16:00  | 2014-12-07 00:00  |

        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Entry choice"
        And I follow "Select entries"
        And I click on "Assign me" "button" in the "Slot 1" "table_row"
        And I click on "Assign me" "button" in the "Slot 5" "table_row"

        Then "Unassign me" "button" should exist in the "Slot 1" "table_row"
        And "Unassign me" "button" should exist in the "Slot 5" "table_row"
        And "Assign me" "button" should not exist in the "Slot 2" "table_row"
        And I follow "Project entries"
        And I see "Slot 1"
        And I see "Slot 5"
        And I do not see "Slot 2"
    #:Scenario

    @javascript
    Scenario: A student can unassign him/her self from an entry.

        Given the following dataform "entries" exist:
            | dataform  | user           | Description       | From              | To                |
            | dataform1 | student1       | Slot 1            | 2014-12-05 08:00  | 2014-12-05 16:00  |
            | dataform1 | teacher1       | Slot 2            | 2014-12-05 16:00  | 2014-12-06 00:00  |
            | dataform1 | teacher1       | Slot 3            | 2014-12-06 00:00  | 2014-12-06 08:00  |
            | dataform1 | teacher1       | Slot 4            | 2014-12-06 08:00  | 2014-12-06 16:00  |
            | dataform1 | student1       | Slot 5            | 2014-12-06 16:00  | 2014-12-07 00:00  |

        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Entry choice"
        And I follow "Select entries"
        And I click on "Unassign me" "button" in the "Slot 1" "table_row"

        Then "Assign me" "button" should exist in the "Slot 1" "table_row"
        And I follow "Project entries"
        And I do not see "Slot 1"
    #:Scenario

    @javascript
    Scenario: A teacher can assign him/her self to an entry that is already assigned to another student.

        Given the following dataform "entries" exist:
            | dataform  | user           | Description       | From              | To                |
            | dataform1 | student1       | Slot 1            | 2014-12-05 08:00  | 2014-12-05 16:00  |
            | dataform1 | teacher1       | Slot 2            | 2014-12-05 16:00  | 2014-12-06 00:00  |
            | dataform1 | teacher1       | Slot 3            | 2014-12-06 00:00  | 2014-12-06 08:00  |
            | dataform1 | teacher1       | Slot 4            | 2014-12-06 08:00  | 2014-12-06 16:00  |
            | dataform1 | student1       | Slot 5            | 2014-12-06 16:00  | 2014-12-07 00:00  |

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Entry choice"
        And I follow "Select entries"
        And I click on "Assign me" "button" in the "Slot 1" "table_row"
        And I click on "Assign me" "button" in the "Slot 5" "table_row"

        Then "Unassign me" "button" should exist in the "Slot 1" "table_row"
        And "Unassign me" "button" should exist in the "Slot 5" "table_row"
    #:Scenario

    @javascript
    Scenario: A student does not see Assign me button on entries that are already assigned to another student.

        Given the following dataform "entries" exist:
            | dataform  | user           | Description       | From              | To                |
            | dataform1 | student1       | Slot 1            | 2014-12-05 08:00  | 2014-12-05 16:00  |
            | dataform1 | teacher1       | Slot 2            | 2014-12-05 16:00  | 2014-12-06 00:00  |
            | dataform1 | teacher1       | Slot 3            | 2014-12-06 00:00  | 2014-12-06 08:00  |
            | dataform1 | teacher1       | Slot 4            | 2014-12-06 08:00  | 2014-12-06 16:00  |
            | dataform1 | student1       | Slot 5            | 2014-12-06 16:00  | 2014-12-07 00:00  |

        When I log in as "student2"
        And I follow "Course 1"
        And I follow "Entry choice"
        And I follow "Select entries"

        Then "Assign me" "button" should not exist in the "Slot 1" "table_row"
        And "Assign me" "button" should not exist in the "Slot 5" "table_row"
    #:Scenario
