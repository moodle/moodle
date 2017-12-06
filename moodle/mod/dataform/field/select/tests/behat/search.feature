@mod @mod_dataform @dataformfield @dataformfield_select @dataformfilter @dataformfieldselectsearch
Feature: Search by a select field

    @javascript
    Scenario: Search equal
        Given I start afresh with dataform "Test select field search"

        And the following dataform "fields" exist:
            | name         | type           | dataform  | param1        |
            | select       | select         | dataform1 | {OP,TL}       |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        And the following dataform "filters" exist:
            | name      | dataform  | searchoptions                 |
            | Filter1   | dataform1 | AND,select,Content,,=,OP      |
            | Filter2   | dataform1 | AND,select,Content,,=,BS      |
            | Filter3   | dataform1 | AND,select,Content,,=,op      |

        And the following dataform "entries" exist:
            | dataform  | user           | select_newvalue  |
            | dataform1 | teacher1       | OP               |
            | dataform1 | teacher1       | TL               |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test select field search"

        Then I see "OP"
        And I see "TL"

        When I set the field "id_filtersmenu" to "Filter1"
        Then I see "OP"
        And I do not see "TL"

        When I set the field "id_filtersmenu" to "Filter2"
        Then I do not see "OP"
        And I do not see "TL"

        When I set the field "id_filtersmenu" to "Filter3"
        Then I see "OP"
        And I do not see "TL"
