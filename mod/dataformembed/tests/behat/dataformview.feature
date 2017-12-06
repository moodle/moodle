@mod @mod_dataformembed @mod_dataform
Feature: Dataform view

    @javascript
    Scenario: Add dataform view block on the frontpage
        Given I start afresh with dataform "Test Dataform View label"

        ## Add a text field.
        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Field Text   | text          | dataform1 |

        ## Add an aligned view.
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        ## Add filters.
        And the following dataform "filters" exist:
            | name      | dataform  | searchoptions                           |
            | F1        | dataform1 | AND,Field Text,content,,=,1 Entry by Teacher 01  |
            | F4        | dataform1 | AND,Field Text,content,,=,4 Entry by Student 02  |

        ## Add entries.
        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  | Field Text                |
            | dataform1 | teacher1      |       |               |               | 1 Entry by Teacher 01     |
            | dataform1 | assistant1    |       |               |               | 2 Entry by Assistant 01   |
            | dataform1 | student1      |       |               |               | 3 Entry by Student 01     |
            | dataform1 | student2      |       |               |               | 4 Entry by Student 02     |
            | dataform1 | student3      |       |               |               | 5 Entry by Student 03     |

        ## Add a dataformembed instance.
        And I log in as "teacher1"
        And I follow "Course 1"

        And I follow "Turn editing on"
        And I follow "Add an activity or resource"
        And I click on "Dataform embedded" "radio"
        And I press "Add"

        And I set the field "Select dataform" to "Test Dataform View label"
        And I press "Save and display"

        And I set the field "Select view" to "View 01"
        And I press "Save and display"

        Then I see "1 Entry by Teacher 01"
        And I see "2 Entry by Assistant 01"
        And I see "3 Entry by Student 01"
        And I see "4 Entry by Student 02"
        And I see "5 Entry by Student 03"

        ## Add a filter.
        And I click on "li.activity.dataformembed a.toggle-display.textmenu" "css_element"
        And I click on "a.editing_update" "css_element" in the "li.activity.dataformembed" "css_element"

        And I set the field "Select filter" to "F1"
        And I press "Save and display"

        Then I see "1 Entry by Teacher 01"
        And I do not see "2 Entry by Assistant 01"
        And I do not see "3 Entry by Student 01"
        And I do not see "4 Entry by Student 02"
        And I do not see "5 Entry by Student 03"
