@mod @mod_dataform @dataformfield @dataformfield_text
Feature: Various inputs

    @javascript
    Scenario: Add dataform entry with text field
        Given I start afresh with dataform "Test field text"

        ## Field
        And the following dataform "fields" exist:
            | name     | type       | dataform  |
            | Text     | text       | dataform1 |

        ## View
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test field text"

        ## Some text.
        And I follow "Add a new entry"
        And I set the field "id_field_1_-1" to "Hello world"
        And I press "Save"
        Then I see "Hello world"

        ## No content
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to ""
        And I press "Save"
        Then I do not see "Hello world"

        ## Alphanumeric123456
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "Alphanumeric123456"
        And I press "Save"
        Then I see "Alphanumeric123456"

        ## Lettersonly
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "Lettersonly"
        And I press "Save"
        Then I see "Lettersonly"

        ## 123456
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "123456"
        And I press "Save"
        Then I see "123456"

        ## email@email.com
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "email@email.com"
        And I press "Save"
        Then I see "email@email.com"

        ## No punctuation!
        And I follow "id_editentry1"
        And I set the field "id_field_1_1" to "No punctuation!"
        And I press "Save"
        Then I see "No punctuation!"
