@mod @mod_dataform @dataformactivity
Feature: Dataform activity individualized

    @javascript
    Scenario: Students cannot see teacher's entries or other students' entries
        Given I start afresh with dataform "Dataform separate participants test"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"

        # As teacher1 add a dataform and an entry
        #---------------------------------------------
        Then I see "This dataform appears to be new or with incomplete setup"

        When I follow "Edit settings"
        And I set the field "Separate participants" to "Yes"
        And I press "Save and display"
        Then I see "This dataform appears to be new or with incomplete setup"

        When I go to manage dataform "views"
        And I set the field "Add a view" to "aligned"
        And I expand all fieldsets
        And I set the field "Name" to "View 01"
        And I fill textarea "Entry template" with "[[EAU:username]]\n[[EAC:edit]]\n[[EAC:delete]]"
        And I press "Save changes"
        Then I see "View 01"

        When I set "View 01" as default view
        Then I do not see "Default view is not set."

        When I follow "Browse"
        And I follow "Add a new entry"
        And I press "Save"
        Then I see "teacher1"

        And I log out

        # As student1 add an entry and should not be able to see other entries
        #---------------------------------------------
        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"
        Then I do not see "teacher1"

        When I follow "Add a new entry"
        And I press "Save"
        Then I see "student1"
        And I do not see "teacher1"

        And I log out

        # As assistan1 add an entry and should not be able to see other entries
        #---------------------------------------------
        When I log in as "assistant1"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"
        Then I do not see "teacher1"
        And I do not see "student1"

        When I follow "Add a new entry"
        And I press "Save"
        Then I see "assistant1"
        And I do not see "teacher1"
        And I do not see "student1"

        And I log out

        # As student2 add an entry and should not be able to see other entries
        #---------------------------------------------
        When I log in as "student2"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"
        Then I do not see "teacher1"
        And I do not see "assistant1"
        And I do not see "student1"

        When I follow "Add a new entry"
        And I press "Save"
        Then I see "student2"
        And I do not see "student1"
        And I do not see "assistant1"
        And I do not see "teacher1"

        And I log out

        # As student1 I should not be able to access other entries via url
        #---------------------------------------------
        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"
        Then I do not see "teacher1"
        And I do not see "assistant1"
        And I do not see "student2"

        # Try accessing student1's entry
        Then I go to dataform page "view.php?d=1&view=1&eids=2"
        And I see "student1"

        # Try viewing student2's entry
        Then I go to dataform page "view.php?d=1&view=1&eids=4"
        And I do not see "student2"

        # Try editing student2's entry
        Given I go to dataform page "view.php?d=1&view=1&editentries=4"
        And I do not see "student2"
        And I do not see "Save"

        And I log out

        # As teacher1 make sure I see all entries
        #---------------------------------------------
        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform separate participants test"
        Then I see "teacher1"
        And I see "assistant1"
        And I see "student1"
        And I see "student2"
