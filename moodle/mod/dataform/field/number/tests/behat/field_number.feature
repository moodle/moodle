@mod @mod_dataform @dataformfield @dataformfield_number
Feature: Add dataform entries
    In order to work with a dataform activity
    As a teacher
    I need to add dataform entries to a dataform instance
    
    @javascript
    Scenario: Use required or noedit patterns
        Given I start afresh with dataform "Test number field"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test number field"

        # Add fields
        When I go to manage dataform "fields"
        And I add a dataform field "number" with "Number 01"        

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

        # No rules with content
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "356"
        And I press "Save"
        Then I see "356"
        
        When I follow "id_editentry1"
        And I set the field "id_field_1_1" to ""
        And I press "Save"
        Then I do not see "356"
        
        # Required *
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I fill textarea "Entry template" with "[[*Number 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        And I press "Save"
        Then I see "You must supply a value here."
        And I set the field "id_field_1_1" to "1112367"
        And I press "Save"
        Then I see "1112367"

        # No edit !
        When I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I fill textarea "Entry template" with "[[!Number 01]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        And I follow "Browse"
        And I follow "id_editentry1"
        Then "id_field_1_1" "field" should not exist
        And I press "Save"
        Then I see "1112367"       

    
    @javascript
    Scenario Outline: Add dataform entry with number field
        Given I start afresh with dataform "Test number field"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test number field"

        # Add a field field
        When I go to manage dataform "fields"
        And I add a dataform field "number" with "<fielddata>"       
        
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
        And I set the field "field_1_-1" to "<input>"
        And I press "Save"
        Then I see "<result>"
        
    Examples:
| input | result | fielddata |
#|    455552.3    |    455552    |    Field 01    Field description 01    0    1        0    100    px    |
#|    1005.5    |    1005    |    Field 02    Field description 02    1    0        0    240    px    |
#|    14    |    14.0    |    Field 03    Field description 03    2    1        1    100    %    |
#|    1000.3335    |    1000.33    |    Field 04    Field description 04    0    0        2    50    %    |
#|    99.999    |    99.99900    |    Field 05    Field description 05    1    1        5    60    em    |
#|    0.12345678    |    0.123457    |    Field 06    Field description 06    2    0        6    100    em    |
