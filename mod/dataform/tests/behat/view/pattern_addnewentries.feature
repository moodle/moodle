@mod @mod_dataform @dataformview @dataformviewpattern
Feature: Pattern ##addnewentries##.

    @javascript
    Scenario: Open multiple new entries for editing.
        #Section:
        Given I start afresh with dataform "Test the addnewentries pattern"
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Field Text   | text          | dataform1 |
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |
        And view "View 01" in dataform "1" has the following view template:
            """
            ##addnewentries##
            ##entries##
            """

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test the addnewentries pattern"
        And I set the field "Add new entries" to "3"

        Then "field_1_-1" "field" exists
        And "field_1_-2" "field" exists
        And "field_1_-3" "field" exists
        And "field_1_-4" "field" does not exist
        #:Section

    @javascript
    Scenario: Student opens multiple new entries for editing.
        #Section:
        Given I start afresh with dataform "Test the addnewentries pattern"
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Field Text   | text          | dataform1 |
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |
        And view "View 01" in dataform "1" has the following view template:
            """
            ##addnewentries##
            ##entries##
            """

        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Test the addnewentries pattern"
        And I set the field "Add new entries" to "3"

        Then "field_1_-1" "field" exists
        And "field_1_-2" "field" exists
        And "field_1_-3" "field" exists
        And "field_1_-4" "field" does not exist
        #:Section
