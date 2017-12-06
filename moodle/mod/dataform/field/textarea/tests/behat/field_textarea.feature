@mod @mod_dataform @dataformfield @dataformfield_textarea
Feature: Add dataform entries

    @javascript
    Scenario: Use required or noedit patterns
        Given I start afresh with dataform "Test textarea field"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test textarea field"

        # Add fields
        When I go to manage dataform "fields"
        And I add a dataform field "textarea" with "Textarea"

        # Add a default view
        When I follow "Views"
        And I add a dataform view "aligned" with "View 01"
        When I set "View 01" as default view

        # No rules no content
        When I follow "Browse"
        And I follow "Add a new entry"
        And I press "Save"
        Then "id_editentry1" "link" exists

        # No rules with content
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "Hello world"
        And I press "Save"
        Then I see "Hello world"

        When I follow "id_editentry1"
        And I set the field "id_field_1_1" to ""
        And I press "Save"
        Then I do not see "Hello world"

        # Required *
        When I go to manage dataform "views"
        And I follow "Edit View 01"
        And I expand all fieldsets
        And I replace in field "Entry template" "[[Textarea]]" with "[[*Textarea]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        And I press "Save"
        Then I see "You must supply a value here."
        And I set the field "id_field_1_1" to "This world is required"
        And I press "Save"
        Then I see "This world is required"

        # No edit !
        When I go to manage dataform "views"
        And I follow "Edit View 01"
        And I expand all fieldsets
        And I replace in field "Entry template" "[[*Textarea]]" with "[[!Textarea]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        Then "id_field_1_1" "field" does not exist
        And I see "This world is required"
        And I press "Save"
        Then I see "This world is required"
