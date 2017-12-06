@mod @mod_dataform @dataformentry @dataformfield @dataformfield_text @dataformfield_text_defaultcontent
Feature: Default content
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance

    #Section:
    @javascript
    Scenario: Default content
        Given I start afresh with dataform "Test text field default content"

        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Test Field   | text          | dataform1 |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |


        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test text field default content"

        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I set the field "Default value" to "Hello world"
        And I press "Save changes"
        And I follow "Browse"

        #Section: Add an entry with clearing its content.
        When I follow "Add a new entry"
        And the field "field_1_-1" matches value "Hello world"
        And I set the field "field_1_-1" to ""
        And I press "Save"
        Then I do not see "Hello world"
        #:Section

        #Section: Add an entry without changing its content.
        When I follow "Add a new entry"
        And I press "Save"
        Then I see "Hello world"
        #:Section

        #Section: Add an entry with changing its content.
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Big Bang"
        And I press "Save"
        Then I see "Big Bang"
        #:Section

        #Section: Change default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to "The Theory"
        And I press "Save changes"
        And I follow "Browse"
        #:Section

        #Section: Add an entry without changing its content.
        When I follow "Add a new entry"
        And I press "Save"
        Then I see "The Theory"
        #:Section

        #Section: clear default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to ""
        And I press "Save changes"
        And I follow "Browse"
        #:Section

        #Section: Add an entry.
        When I follow "Add a new entry"
        And the field "field_1_-1" matches value ""
        #:Section

    #:Section
