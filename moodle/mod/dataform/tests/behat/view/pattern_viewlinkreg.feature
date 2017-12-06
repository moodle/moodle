@mod @mod_dataform @dataformview @dataformviewpattern
Feature: Pattern ##viewlink:viewname;some label;some args;##.

    @javascript
    Scenario: Use the ##viewlink:viewname;some label;;## pattern.
        #Section:
        Given I start afresh with dataform "Test the viewlink:viewname;some label pattern"
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Field Text   | text          | dataform1 |
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |
            | View 02  | aligned   | dataform1 |           |
        And view "View 01" in dataform "1" has the following view template:
            """
            <div class="viewlink">##viewlink:View 02;Click me to go to View 02;;##</div>
            ##entries##
            """
        And view "View 02" in dataform "1" has the following view template:
            """
            <h3>I am View 02</h3>
            ##entries##
            """

        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test the viewlink:viewname;some label pattern"
        And I follow "Click me to go to View 02"

        Then I see "I am View 02"
        #:Section
