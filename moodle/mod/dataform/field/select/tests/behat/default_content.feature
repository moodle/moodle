@mod @mod_dataform @dataformentry @dataformfield @dataformfield_select @dataformfield_select_defaultcontent
Feature: Default content
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance

    #Section:
    @javascript
    Scenario: Default content
        Given I start afresh with dataform "Test select field default content"

        And the following dataform "fields" exist:
            | name         | type          | dataform  | param1                    |
            | Test Field   | select        | dataform1 | {The,Big,Bang,Theory}     |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |


        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test select field default content"

        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to "Big"
        And I press "Save changes"
        And I follow "Browse"

        # Add an entry with clearing its content.
        When I follow "Add a new entry"
        And the field "field_1_-1_selected" matches value "Big"
        And I set the field "field_1_-1_selected" to ""
        And I press "Save"
        Then I do not see "Big"

        # Add an entry without changing its content.
        When I follow "Add a new entry"
        And I press "Save"
        Then I see "Big"

        # Add an entry with changing its content.
        When I follow "Add a new entry"
        And I set the field "field_1_-1_selected" to "Theory"
        And I press "Save"
        Then I see "Theory"

        # Change default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to "Bang"
        And I press "Save changes"
        And I follow "Browse"

        # Add an entry without changing its content.
        When I follow "Add a new entry"
        And the field "field_1_-1_selected" matches value "Bang"
        And I press "Save"
        Then I see "Bang"

        # Remove default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to ""
        And I press "Save changes"
        And I follow "Browse"

        # Open a new entry form.
        When I follow "Add a new entry"
        And the field "field_1_-1_selected" matches value ""
    #:Section
