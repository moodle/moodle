@mod @mod_dataform @dataformfield @dataformfield_entrygroup
Feature: Patterns

    Background:
        Given I start afresh with dataform "Test entry group field"

        ## View
        And the following dataform "views" exist:
            | name     | type    | dataform  | default   |
            | View 01  | grid    | dataform1 | 1         |

        And view "View 01" in dataform "1" has the following entry template:
            """
                <div class="entry">
                <div>Group name is [[EGR:name]]</div>
                <div>Group idnumber is [[EGR:idnumber]]</div>
                <div>Group members count is [[EGR:members:count]]</div>
                <div>Group members names are [[EGR:members:list]]</div>
                [[EAC:edit]]
                </div>
            """

        And the following "group members" exist:
            | user     | group  |
            | student2 | G1 |

    @javascript
    Scenario: The field patterns display the group info in browse mode.
        Given the following dataform "entries" exist:
            | dataform  | group    |
            | dataform1 | G1       |

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test entry group field"

        Then I see "Group name is Group 1"
        And I see "Group idnumber is G1"
        And I see "Group members count is 2"
        And I see "Group members names are Student 1, Student 2"
    #:Scenario

    @javascript
    Scenario: The field patterns display the group info in edit mode.
        Given the following dataform "entries" exist:
            | dataform  | group    |
            | dataform1 | G1       |

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test entry group field"
        And I follow "id_editentry1"

        Then I see "Group name is Group 1"
        And I see "Group idnumber is G1"
        And I see "Group members count is 2"
        And I see "Group members names are Student 1, Student 2"
    #:Scenario
