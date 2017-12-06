@mod @mod_dataform @dataformfield @dataformfield_radiobutton
Feature: Add dataform entries
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance
    
    @javascript
    Scenario: Use required or noedit patterns
        Given I start afresh with dataform "Test radiobutton field"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test radiobutton field"

        # Add fields
        When I go to manage dataform "fields"
        And I add a dataform field "radiobutton" with "Radiobutton 01"        
        And I set dataform field "Radiobutton 01" options to "RB 01\nRB 02\nRB 03\nRB 04"        

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
        And I fill textarea "Entry template" with "[[*Radiobutton 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        And I press "Save"
        Then I see "You must supply a value here."
        And I set the field "RB 04" to "checked"
        And I press "Save"
        Then I do not see "RB 01"
        And I do not see "RB 02"
        And I do not see "RB 03"
        And I see "RB 04"

        # No edit !
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I fill textarea "Entry template" with "[[!Radiobutton 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        Then "RB 01" "radio" should not exist
        And "RB 02" "radio" should not exist
        And "RB 03" "radio" should not exist
        And "RB 04" "radio" should not exist
        And I press "Save"
        Then I do not see "RB 01"
        And I do not see "RB 02"
        And I do not see "RB 03"
        And I see "RB 04"

        # No rules
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets       
        And I fill textarea "Entry template" with "[[Radiobutton 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        And I set the field "RB 02" to "checked"
        And I set the field "RB 01" to "checked"
        And I press "Save"
        Then I see "RB 01"
        And I do not see "RB 02"
        And I do not see "RB 03"
        And I do not see "RB 04"

    @javascript
    Scenario Outline: Add dataform entry with radiobutton field
        Given I start afresh with dataform "Test Dataform"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Basic Dataform Management"

        # Add a field
        When I go to manage dataform "fields"
        And I add a dataform field "radiobutton" with "<fielddata>"       
        
        # Add a default view
        When I follow "Views"
        And I add a dataform view "grid" with "View 01"
        Then I see "View 01"
        And I see "Default view is not set."
        When I set "View 01" as default view
        Then I do not see "Default view is not set."

        # Go to browse view
        When I follow "Browse"
        Then I see "Add a new entry"        

        # Add an entry with content
        When I follow "Add a new entry"
        #And I set the field "field_1_-1" to "<input>"
        And I press "Save"
        Then I see "<result>"
        
    Examples:
| input | result | fielddata |
#|    Option 1    |    Option 1    |    Field 01    Field description 01    0    1        Option 1\nOption 2\nOption 3\nOption 4    Option 1    3    1    |
#|    Today    |    Today    |    Field 02    Field description 02    1    0        Yesterday\nToday\nTomorrow    Today    1    1    |
#|    8    |    8    |    Field 03    Field description 03    2    1        1\n2\n3\n4\n5\n6\n7\n8\n9\n10\n11\n12    8    2    1    |
#|    One option    |    One option    |    Field 04    Field description 04    0    0        One option    One option    2    0    |
#|    Two    |    Two    |    Field 05    Field description 05    1    1        Two\nTwo    Two    1    0    |
#|    Useful    |    Useful    |    Field 06    Field description 06    2    0        Useful    Useful    0    0    |
