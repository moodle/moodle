@mod @mod_dataform @dataformentry @dataformfield @dataformfield_selectmulti @dataformfield_selectmulti_defaultcontent
Feature: Default content
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance

    #Section:
    @javascript
    Scenario: Default content
        Given I start afresh with dataform "Test selectmulti field default content"

        And the following dataform "fields" exist:
            | name         | type          | dataform  | param1                    |
            | Test Field   | selectmulti      | dataform1 | {The,Big,Bang,Theory}  |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |


        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test selectmulti field default content"

        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Options separator" to "Space"
        And I set the field "Default value" to "Big,Bang"
        And I press "Save changes"
        And I follow "Browse"

        #Section: Add an entry without changing its content.
        When I follow "Add a new entry"
        And I press "Save"
        Then I see "Big Bang"
        #:Section

        #Section: Add an entry with changing its content.
        When I follow "Add a new entry"
        And I set the field "id_field_1_-1_selected" to "The,Theory"
        And I press "Save"
        Then I see "The Theory"
        #:Section

        #Section: Change default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "Default value" to "The,Bang"
        And I press "Save changes"
        And I follow "Browse"
        #:Section

        #Section: Add an entry without changing its content.
        When I follow "Add a new entry"
        And I press "Save"
        Then I see "The Bang"
        #:Section

        #Section: Change default content setting in field.
        And I go to manage dataform "fields"
        And I follow "Test Field"
        And I expand all fieldsets
        And I set the field "id_contentdefault" to ""
        And I press "Save changes"
        And I follow "Browse"
        #:Section

        #Section: Open a new entry form.
        When I follow "Add a new entry"
        And I set the field "id_field_1_-1_selected" to "The,Big,Bang,Theory"
        And I press "Save"
        Then I see "The Big Bang Theory"
        #:Section
    #:Section
