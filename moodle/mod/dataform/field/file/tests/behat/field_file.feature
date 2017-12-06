@mod @mod_dataform @dataformfield @dataformfield_file @_file_upload
Feature: Add dataform entries
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance
    

    @javascript
    Scenario: Use required or noedit patterns
        Given I start afresh with dataform "Test file field"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test file field"

        # Add fields
        When I go to manage dataform "fields"
        And I add a dataform field "file" with "File 01"        

        # Add a default view
        When I follow "Views"
        And I add a dataform view "aligned" with "View 01"        
        Then I see "View 01"
        And I see "Default view is not set."
        When I set "View 01" as default view
        Then I do not see "Default view is not set."

        # No rules no content
        When I follow "Browse"
        And I follow "Add a new entry"
        And I press "Save"
        Then "id_editentry1" "link" should exist        

        # Required *
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I fill textarea "Entry template" with "[[*File 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        Then I see "Maximum size for new files:"
        When I press "Save"
        Then I do not see "Add a new entry"
        And "id_editentry1" "link" should not exist
        And I see "Maximum size for new files:"
        When I upload "mod/dataform/tests/fixtures/test_dataform_entries.csv" file to "File 01" filemanager
        And I press "Save"
        Then I see "test_dataform_entries.csv"

        # No edit !
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I fill textarea "Entry template" with "[[!File 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        Then I do not see "Maximum size for new files:"
        And I press "Save"
        Then "id_editentry1" "link" should exist
