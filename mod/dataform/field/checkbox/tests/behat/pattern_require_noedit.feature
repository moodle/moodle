@mod @mod_dataform @dataformfield @dataformfield_checkbox @dataformfieldtest
Feature: Pattern required noedit

    @javascript
    Scenario: Use required or noedit patterns
        Given I start afresh with dataform "Test field checkbox"

        ## Field
        And the following dataform "fields" exist:
            | name         | type          | dataform  | param1 |
            | Checkbox    | checkbox       | dataform1 | {CB 01,CB 02,CB 03,CB 04} |

        ## View
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test field checkbox"

        # No rules no content
        And I follow "Add a new entry"
        And I press "Save"
        Then I do not see "CB 01"
        And I do not see "CB 02"
        And I do not see "CB 03"
        And I do not see "CB 04"
        And "id_editentry1" "link" exists

        # No rules with content
        And I follow "id_editentry1"
        And I set the field "CB 01" to "checked"
        And I press "Save"
        Then I see "CB 01"

        And I follow "id_editentry1"
        And I set the field "CB 01" to ""
        And I press "Save"
        Then I do not see "CB 01"

        # Required *
        Then I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I replace in field "Entry template" "[[Checkbox]]" with "[[*Checkbox]]"
        And I press "Save changes"
        
        And I follow "Browse"
        And I follow "id_editentry1"
        And I press "Save"
        Then I see "You must supply a value here."
        And I set the field "CB 01" to "checked"
        And I set the field "CB 02" to "checked"
        And I set the field "CB 03" to "checked"
        And I set the field "CB 02" to ""
        And I press "Save"
        Then I see "CB 01"
        And I do not see "CB 02"
        And I see "CB 03"
        And I do not see "CB 04"

        # No edit !
        Then I go to manage dataform "views"
        And I follow "id_editview1"
        And I expand all fieldsets
        And I replace in field "Entry template" "[[*Checkbox]]" with "[[!Checkbox]]"
        And I press "Save changes"
        
        And I follow "Browse"
        And I follow "id_editentry1"
        Then "CB 01" "checkbox" should not exist
        And "CB 02" "checkbox" should not exist
        And "CB 03" "checkbox" should not exist
        And "CB 04" "checkbox" should not exist
        And I press "Save"
        Then I see "CB 01"
        And I do not see "CB 02"
        And I see "CB 03"
        And I do not see "CB 04"


    @javascript
    Scenario: Add dataform entry with checkbox field
        Given I start afresh with dataform "Test field checkbox"

        ## Field
        And the following dataform "fields" exist:
            | name         | type          | dataform  | param1 |
            | Checkbox    | checkbox       | dataform1 | {Option 1,Option 2,Option 3,Option 4} |

        ## View
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test field checkbox"

        # BROWSE
        ################################
        And I follow "Add a new entry"
        And I press "Save"
        Then "id_editentry1" "link" should exist
        And I do not see "Option 1"
        And I do not see "Option 2"
        And I do not see "Option 3"
        And I do not see "Option 4"

        # Edit existing entry field with no content and tick checkboxes
        ################################
        Then I follow "id_editentry1"
        And I set the field "Option 1" to "checked"
        And I set the field "Option 2" to "checked"
        And I press "Save"
        Then "id_editentry1" "link" should exist
        And I see "Option 1"
        And I see "Option 2"
        And I do not see "Option 3"
        And I do not see "Option 4"

        # Edit existing entry with content and change selection
        ################################
        Then I follow "id_editentry1"
        And I set the field "Option 1" to ""
        And I set the field "Option 3" to "checked"
        And I set the field "Option 4" to "checked"
        And I press "Save"
        Then "id_editentry1" "link" should exist
        And I do not see "Option 1"
        And I see "Option 2"
        And I see "Option 3"
        And I see "Option 4"

        # Edit existing entry with content and clear content
        ################################
        Then I follow "id_editentry1"
        And I set the field "Option 1" to ""
        And I set the field "Option 2" to ""
        And I set the field "Option 3" to ""
        And I set the field "Option 4" to ""
        And I press "Save"
        Then "id_editentry1" "link" should exist
        And I do not see "Option 1"
        And I do not see "Option 2"
        And I do not see "Option 3"
        And I do not see "Option 4"
