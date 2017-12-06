@mod @mod_dataform @dataformfilter
Feature: Filtering

    Background:
    #Section: Activity setup.
        Given a fresh site with dataform "Test dataform filtering"

        And the following dataform "fields" exist:
            | name         | type          | dataform  |
            | Text field   | text          | dataform1 |

        And the following dataform "views" exist:
            | name         | type      | dataform  | default   |
            | Main View    | grid      | dataform1 | 1         |

        And view "Main View" in dataform "1" has the following view template:
            """
            <div>
                <div class="addnewentry-wrapper">##addnewentry##</div>
                <div class="quickfilters-wrapper">
                    <div class="quickfilter">Current filter ##filtersmenu##</div>
                    <div class="quickfilter">
                        <a href="javascript:document.querySelector('#quicksearch form').submit();">Search</a>
                        <div id="quicksearch">##quicksearch##</div>
                    </div>
                    <div class="quickfilter">Per page ##quickperpage##</div>
                    <div class="clearfix"></div>
                </div>
                <div>##advancedfilter##</div>
                <div>##paging:bar##</div>
                <div>
                    <table class="generaltable">
                        ##entries##
                    </table>
                </div>
            </div>
            """

        And view "Main View" in dataform "1" has the following entry template:
            """
            <tr>
                <td>[[entryid]]</td>
                <td>[[EAU:name]]</td>
                <td>[[Text field]] by [[EAU:firstname]] [[EAU:lastname]]</td>
            </tr>
            """
    #:Section


    @javascript @dataformfiltering1
    Scenario: Filtering
    #Section: Steps.
        #Section: Add entries.
        And the following dataform "entries" exist:
            | dataform  | user          | Text field   |
            | dataform1 | teacher1      | Entry 01     |
            | dataform1 | teacher1      | Entry 02     |
            | dataform1 | teacher1      | Entry 03     |
            | dataform1 | teacher1      | Entry 04     |
            | dataform1 | teacher1      | Entry 05     |
            | dataform1 | teacher1      | Entry 06     |
        #:Section

        #Section: Log in.
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test dataform filtering"
        And I see "Entry 01"
        And I see "Entry 02"
        And I see "Entry 03"
        And I see "Entry 04"
        And I see "Entry 05"
        And I see "Entry 06"
        #:Section

        ### Quick filtering ###

        #Section: Quick per page.
        And I do not see "Quick filter"
        And I do not see "Next"
        And I do not see "Previous"

        Then I set the field "uperpage" to "1"
        And I see "Quick filter"
        And I see "Next"
        And I do not see "Previous"
        And I see "Entry 01"
        And I do not see "Entry 02"

        Then I click on ".page1 a" "css_element"
        And I see "Quick filter"
        And I see "Next"
        And I see "Previous"
        And I do not see "Entry 01"
        And I see "Entry 02"

        Then I set the field "uperpage" to "2"
        And I see "Quick filter"
        And I see "Next"
        And I see "Previous"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        And I see "Entry 03"
        And I see "Entry 04"

        Then I set the field "uperpage" to "3"
        And I see "Quick filter"
        And I do not see "Next"
        And I see "Previous"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        And I see "Entry 04"
        And I see "Entry 05"
        And I see "Entry 06"

        Then I set the field "id_filtersmenu" to "* Reset quick filter"
        And I do not see "Quick filter"
        And I do not see "Next"
        And I do not see "Previous"
        And I see "Entry 01"
        And I see "Entry 02"
        And I see "Entry 03"
        And I see "Entry 04"
        And I see "Entry 05"
        And I see "Entry 06"
        #:Section

        #Section: Quick search.
        Then I set the field "usearch" to "Entry 01"
        And I click on "Search" "link"
        And I see "Quick filter"
        And I see "Entry 01"
        And I do not see "Entry 02"
        #:Section

        ### Define and apply a standard filter ###

        #Section: Add filter: Last 2 entries
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "Last 2 Entries"
        And I set the field "Per page" to "2"
        And I set sort criterion "1" to "1,content" "1"
        And I press "Save changes"
        And I see "Last 2 Entries"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "Last 2 Entries"
        And I see "Entry 05"
        And I see "Entry 06"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        #:Section

        #Section: Add filter: With "Entry 01" content
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "With Entry_01"
        And I set search criterion "1" to "AND" "1,content" "" "=" "Entry 01"
        And I press "Save changes"
        And I see "With Entry_01"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "With Entry_01"
        And I see "Entry 01"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        And I do not see "Entry 04"
        And I do not see "Entry 05"
        And I do not see "Entry 06"
        #:Section

        #Section: Add filter: With Entry 01 and Entry_05 content
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "With Entry_01 and Entry_05"
        And I set search criterion "1" to "AND" "1,content" "" "=" "Entry 01"
        And I set search criterion "2" to "AND" "1,content" "" "=" "Entry 05"
        And I press "Save changes"
        And I see "With Entry_01 and Entry_05"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "With Entry_01 and Entry_05"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        And I do not see "Entry 04"
        And I do not see "Entry 05"
        And I do not see "Entry 06"
        #:Section

        #Section: Add filter: With Entry 01 or Entry_05 content
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "With Entry_01 or Entry_05"
        And I set search criterion "1" to "OR" "1,content" "" "=" "Entry 01"
        And I set search criterion "2" to "OR" "1,content" "" "=" "Entry 05"
        And I press "Save changes"
        And I see "With Entry_01 or Entry_05"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "With Entry_01 or Entry_05"
        And I see "Entry 01"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        And I do not see "Entry 04"
        And I see "Entry 05"
        And I do not see "Entry 06"
        #:Section

        #Section: Add filter: With Entry 01 or Entry_03 or Entry_06
        Then I go to manage dataform "filters"
        Then I follow "Add a filter"
        And I set the field "Name" to "With Entry 01 or Entry_03 or Entry_06"
        And I set search criterion "1" to "OR" "1,content" "" "=" "Entry 03"
        And I set search criterion "2" to "OR" "1,content" "" "=" "Entry 01"
        And I press "Continue"
        And I set search criterion "3" to "OR" "1,content" "" "=" "Entry 06"
        And I press "Save changes"

        Then I follow "Browse"
        Then I set the field "id_filtersmenu" to "With Entry 01 or Entry_03 or Entry_06"
        And I see "Entry 01"
        And I do not see "Entry 02"
        And I see "Entry 03"
        And I do not see "Entry 04"
        And I do not see "Entry 05"
        And I see "Entry 06"
        #:Section

        ### Define and apply advanced filters ###

        #Section: Advanced filter: My Entry 01 or Entry_03 or Entry_06 content
        Then I follow "Advanced filter"
        And I set the field "Name" to "My Entry 01 or Entry_03 or Entry_06"
        And I set search criterion "1" to "OR" "1,content" "" "=" "Entry 03"
        And I set search criterion "2" to "OR" "1,content" "" "=" "Entry 01"
        And I press "Continue"
        And I set search criterion "3" to "OR" "1,content" "" "=" "Entry 06"
        And I press "Save changes"

        And I see "Entry 01"
        And I do not see "Entry 02"
        And I see "Entry 03"
        And I do not see "Entry 04"
        And I do not see "Entry 05"
        And I see "Entry 06"

        Then I set the field "id_filtersmenu" to "* Reset saved filters"
        And I see "Entry 01"
        And I see "Entry 02"
        And I see "Entry 03"
        And I see "Entry 04"
        And I see "Entry 05"
        And I see "Entry 06"
        #:Section
    #:Section

    @javascript @dataformfiltering2
    Scenario Outline: Filtering a view with forced filter.
    #Section: Steps.
        #Section: Add entries.
        And the following dataform "entries" exist:
            | dataform  | user          | Text field   |
            | dataform1 | teacher1      | Entry 01     |
            | dataform1 | student1      | Entry 01     |
            | dataform1 | teacher1      | Entry 02     |
            | dataform1 | student1      | Entry 02     |
            | dataform1 | teacher1      | Entry 03     |
            | dataform1 | student1      | Entry 03     |
        #:Section

        #Section: Add filters.
        And the following dataform "filters" exist:
            | name              | dataform  | visible   | searchoptions                 |
            | My Entries        | dataform1 | 0         | AND,EAU,currentuser,NOT,,     |
            | Two Last Entries  | dataform1 | 1         | OR,Text field,content,,=,Entry 02;OR,Text field,content,,=,Entry 03     |
        #:Section

        #Section: Adjust the view filter.
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test dataform filtering"
        Then I go to manage dataform "views"
        And I set the field with xpath "//select[@name='fid']" to "My Entries"
        And I log out
        #:Section

        #Section: Log in.
        And I log in as "<user>"
        And I follow "Course 1"
        And I follow "Test dataform filtering"

        And I see "Entry 01 by <user name>"
        And I see "Entry 02 by <user name>"
        And I see "Entry 03 by <user name>"
        And I do not see "Entry 01 by <other name>"
        And I do not see "Entry 02 by <other name>"
        And I do not see "Entry 03 by <other name>"
        #:Section

        #Section: Quick per page.
        And I do not see "Quick filter"
        And I do not see "Next"
        And I do not see "Previous"

        Then I set the field "uperpage" to "1"
        And I see "Quick filter"
        And I see "Next"
        And I do not see "Previous"
        And I see "Entry 01 by <user name>"
        And I do not see "Entry 02"

        Then I click on ".page1 a" "css_element"
        And I see "Quick filter"
        And I see "Next"
        And I see "Previous"
        And I do not see "Entry 01"
        And I see "Entry 02 by <user name>"

        Then I set the field "uperpage" to "2"
        And I see "Quick filter"
        And I do not see "Next"
        And I see "Previous"
        And I do not see "Entry 01"
        And I do not see "Entry 02"
        And I see "Entry 03 by <user name>"

        Then I set the field "uperpage" to "3"
        And I see "Quick filter"
        And I do not see "Next"
        And I do not see "Previous"
        And I see "Entry 01 by <user name>"
        And I see "Entry 02 by <user name>"
        And I see "Entry 03 by <user name>"

        Then I set the field "id_filtersmenu" to "* Reset quick filter"
        And I do not see "Quick filter"
        And I do not see "Next"
        And I do not see "Previous"
        And I see "Entry 01 by <user name>"
        And I see "Entry 02 by <user name>"
        And I see "Entry 03 by <user name>"
        #:Section

        #Section: Quick search.
        Then I set the field "usearch" to "Entry 01"
        And I click on "Search" "link"
        And I see "Quick filter"
        And I see "Entry 01 by <user name>"
        And I do not see "Entry 01 by <other name>"
        And I do not see "Entry 02"
        And I do not see "Entry 03"
        #:Section

        #Section: Predefined filter.
        Then I set the field "id_filtersmenu" to "Two Last Entries"
        And I do not see "Entry 01"
        And I do not see "<other name>"
        And I see "Entry 02 by <user name>"
        And I see "Entry 03 by <user name>"
        #:Section
    #:Section
    Examples:
        | user      | user name | other name    |
        | teacher1  | Teacher 1 | Student 1     |
        | student1  | Student 1 | Teacher 1 |
