@mod @mod_dataform @dataformview @dataformviewpattern
Feature: Pattern ##addnewentry##.

    @javascript
    Scenario: Open a new entry for editing.
        #Section:
        Given I start afresh with dataform "Test the addnewentry pattern"
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Field Text   | text          | dataform1 |
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test the addnewentry pattern"
        And I follow "Add a new entry"

        Then "field_1_-1" "field" exists
        And "field_1_-2" "field" does not exist
        #:Section
