@mod @mod_dataform @dataformfield @dataformfield_time
Feature: Adding entries with field

    Background:
        Given I start afresh with dataform "Test time field"

        ## Field
        And the following dataform "fields" exist:
            | name      | type  | dataform  |
            | Time 01   | time  | dataform1 |

        ## View
        And the following dataform "views" exist:
            | name     | type    | dataform  | default   |
            | View 01  | aligned | dataform1 | 1         |


    @javascript
    Scenario: The data/time selector is disabled by default.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        When I follow "Add a new entry"
        Then the "field_1_-1[day]" "select" should be disabled
        #:Section

    @javascript
    Scenario: Required field is enabled by default and cannot be disabled.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        And view "View 01" in dataform "1" has the following entry template:
            """
            [[EAU:picture]]
            [[EAU:name]]
            [[*Time 01]]
            [[EAC:edit]]
            [[EAC:delete]]
            """

        When I follow "Add a new entry"

        Then the "field_1_-1[day]" "select" should be enabled
        And "id_field_1_-1_enabled" "checkbox" should not exist
        #:Section

    @javascript
    Scenario: Noedit field does not display input elements in editing mode.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        And view "View 01" in dataform "1" has the following entry template:
            """
            [[EAU:picture]]
            [[EAU:name]]
            [[!Time 01]]
            [[EAC:edit]]
            [[EAC:delete]]
            """

        When I follow "Add a new entry"

        Then "field_1_1[day]" "select" should not exist
        #:Section

    @javascript
    Scenario: Noedit field displays content in editing mode.
        #Section:
        Given view "View 01" in dataform "1" has the following entry template:
            """
            [[EAU:picture]]
            [[EAU:name]]
            [[!Time 01]]
            [[EAC:edit]]
            [[EAC:delete]]
            """
        And the following dataform "entries" exist:
            | dataform  | user           | Time 01           |
            | dataform1 | student1       | 2014-12-05 08:00  |
        And I am in dataform "Test time field" "Course 1" as "teacher1"

        When I follow "id_editentry1"

        Then I see "December 2014"
        #:Section

    @javascript
    Scenario: Teacher adds entry without content.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        When I follow "Add a new entry"
        And I press "Save"
        Then "id_editentry1" "link" should exist
        #:Section

    @javascript
    Scenario: Teacher adds entry with content.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        When I add a dataform entry with:
            | field_1_-1[enabled] | checked |
            | field_1_-1[year]    | 2013    |
        Then I see "2013"
        #:Section

    @javascript
    Scenario: Add time content of a masked field.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        And the following dataformfield time exists:
            | dataform          | dataform1 |
            | name              | Time 01   |
            | date only         |           |
            | masked            | 1         |
            | start year        |           |
            | stop year         |           |
            | display format    |           |
            | default content   |           |
        When I add a dataform entry with:
            | field_1_-1[year]    | 2014    |
        Then I see "1 January 2014, 12:00 AM"
        #:Section

    @javascript
    Scenario: Add time content of a masked field with custom start year.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        And the following dataformfield time exists:
            | dataform          | dataform1 |
            | name              | Time 01   |
            | date only         |           |
            | masked            | 1         |
            | start year        |           |
            | stop year         | 2025      |
            | display format    |           |
            | default content   |           |
        When I add a dataform entry with:
            | field_1_-1[year]    | 2024    |
        Then I see "1 January 2024, 12:00 AM"
        #:Section

    @javascript
    Scenario: Add time content with display format.
        #Section:
        Given I am in dataform "Test time field" "Course 1" as "teacher1"
        And the following dataformfield time exists:
            | dataform          | dataform1 |
            | name              | Time 01   |
            | date only         |           |
            | masked            |           |
            | start year        |           |
            | stop year         |           |
            | display format    | %Y-%m-%d  |
            | default content   |           |
        When I add a dataform entry with:
            | field_1_-1[enabled] | checked |
            | field_1_-1[year]    | 2012    |
            | field_1_-1[month]   | 11      |
            | field_1_-1[day]     | 26      |
        Then I see "2012-11-26"
        #:Section
