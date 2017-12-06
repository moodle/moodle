@mod @mod_dataform @dataformfield @dataformfield_ratingmdl @dataformfield_ratingmdl_rating
Feature: Rating

    @javascript
    Scenario: Rate entries
        ### Background ###

        Given I start afresh with dataform "Test ratingmdl field"

        # Teacher Set up
        #---------------------------

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test ratingmdl field"

        ## Fields
        And I go to manage dataform "fields"
        And I set the field "Add a field" to "ratingmdl"
        And I expand all fieldsets
        And I set the field "Name" to "rating1"
        And I set the field "Type" to "Point"
        And I set the field "Maximum points" to "100"
        And I press "Save changes"

        And I set the field "Add a field" to "ratingmdl"
        And I expand all fieldsets
        And I set the field "Name" to "rating2"
        And I set the field "Type" to "Point"
        And I set the field "Maximum points" to "24"
        And I press "Save changes"

        ## View
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | grid   | dataform1 | 1         |


        And view "View 01" in dataform "1" has the following entry template:
            """
            <div class="entry">
            [[rating1]]
            <div>Rating1 avg: [[rating1:view:avg]]</div>
            <div>Rating1 sum: [[rating1:view:sum]]</div>
            <div>Rating1 count: [[rating1:view:count]]</div>
            <div>Rating1 max: [[rating1:view:max]]</div>
            <div>Rating1 min: [[rating1:view:min]]</div>
            [[rating2]]
            <div>Rating2 avg: [[rating2:view:avg]]</div>
            <div>Rating2 sum: [[rating2:view:sum]]</div>
            <div>Rating2 count: [[rating2:view:count]]</div>
            <div>Rating2 max: [[rating2:view:max]]</div>
            <div>Rating2 min: [[rating2:view:min]]</div>
            </div>
            """

        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  | Field Text                |
            | dataform1 | teacher1      |       |               |               | 1 Entry by Teacher 01     |
            | dataform1 | assistant1    |       |               |               | 2 Entry by Assistant 01   |
            | dataform1 | student1      |       |               |               | 3 Entry by Student 01     |
            | dataform1 | student2      |       |               |               | 4 Entry by Student 02     |
            | dataform1 | student3      |       |               |               | 5 Entry by Student 03     |

        #And the following "permission overrides" exist:
        #    | capability                    | permission    | role           | contextlevel    | reference |
        #    | mod/dataform:entryownupdate   | Prevent       | student        | Activity module | dataform1 |
        #    | mod/dataform:entryowndelete   | Prevent       | student        | Activity module | dataform1 |

        And I follow "Browse"

        # Teacher rating
        #---------------------------
        Then I set the field "ratingmenu_1_1" to "95"
        And I see "Rating1 avg: 95"
        And I see "Rating1 sum: 95"
        And I see "Rating1 count: 1"
        And I see "Rating1 max: 95"
        And I see "Rating1 min: 95"


        Then I set the field "ratingmenu_1_1" to "84"
        And I see "Rating1 avg: 84"
        And I see "Rating1 sum: 84"
        And I see "Rating1 count: 1"
        And I see "Rating1 max: 84"
        And I see "Rating1 min: 84"

        And I log out

        # Student rating
        #---------------------------
        And I log in as "student1"
        And I follow "Course 1"
        And I follow "Test ratingmdl field"

        Then I set the field "ratingmenu_1_1" to "96"
        And I see "Rating1 avg: 90"
        And I see "Rating1 sum: 100"
        And I see "Rating1 count: 2"
        And I see "Rating1 max: 96"
        And I see "Rating1 min: 84"

