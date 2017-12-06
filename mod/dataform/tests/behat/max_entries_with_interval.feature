@mod @mod_dataform @dataformactivity
Feature: Dataform max entries

    @javascript
    Scenario: Students cannot add more than max entries every interval
        # 125 steps
        
        Given I start afresh with dataform "Max entries with interval"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Max entries with interval"
        Then I see "This dataform appears to be new or with incomplete setup"
        
        When I follow "Edit settings"
        And I expand all fieldsets
        
        And the "timeavailable[day]" "select" should be disabled
        And the "timedue[day]" "select" should be disabled
        And the "timeinterval[number]" "field" should be disabled
        And the "Number of intervals" "select" should be disabled
               
        And I set the field "Maximum entries" to "1" 
        And I set the field "id_timeavailable_enabled" to "checked" 
        And I set the field "timeinterval[number]" to "1" 
        And I set the field "Number of intervals" to "10" 
        And I press "Save and display"
        Then I see "This dataform appears to be new or with incomplete setup"

        # Add a text field       
        When I go to manage dataform "fields"
        And I add a dataform field "text" with "Field 01"        
        Then I see "Field 01"
        
        # Add a view with with default submission buttons
        When I follow "Views"
        And I set the field "Add a view" to "grid"
        And I set the field "Name" to "View 01"
        And I press "Save changes"
        
        Then I see "View 01"
        And I see "Default view is not set."
        When I set "View 01" as default view
        Then I do not see "Default view is not set."

        ### INTERVAL 1 ###
        
        # Teacher can submit more than max
        #---------------------------
        When I follow "Browse"
        Then I see "Add a new entry"

        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Teacher Entry 01"
        And I press "Save"
        Then I see "Entry 01"
        
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Teacher Entry 02"
        And I press "Save"
        Then I see "Teacher Entry 01"
        And I see "Teacher Entry 02"
        
        And I log out

        
        # Student cannot submit more than max
        #---------------------------------------------
        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Max entries with interval"
        Then I see "Entry 01"
        And I see "Entry 02"
        
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Student Entry 01"
        And I press "Save"
        Then I see "Teacher Entry 01"
        And I see "Teacher Entry 02"
        And I see "Student Entry 01"
        And I do not see "Add a new entry"
        
        When I go to dataform page "view.php?d=1&view=1&editentries=-1"
        Then I do not see "New entry"
        And "field_1_-1" "field" should not exist        
        
        And I log out
        
        ### INTERVAL 2 ###
        And I wait "60" seconds
        
        # Teacher can submit more than max
        #---------------------------
        When I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Max entries with interval"
        Then I see "Add a new entry"

        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Teacher Entry 03"
        And I press "Save"
        Then I see "Entry 01"
        
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Teacher Entry 04"
        And I press "Save"
        Then I see "Teacher Entry 01"
        And I see "Teacher Entry 02"
        And I see "Student Entry 01"
        And I see "Teacher Entry 03"
        And I see "Teacher Entry 04"
        
        And I log out

        
        # Student cannot submit more than max
        #---------------------------------------------
        When I log in as "student1"
        And I follow "Course 1"
        And I follow "Max entries with interval"
        Then I see "Teacher Entry 01"
        And I see "Teacher Entry 02"
        And I see "Student Entry 01"
        And I see "Teacher Entry 03"
        And I see "Teacher Entry 04"
        
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Student Entry 02"
        And I press "Save"
        Then I see "Teacher Entry 01"
        And I see "Teacher Entry 02"
        And I see "Student Entry 01"
        And I see "Teacher Entry 03"
        And I see "Teacher Entry 04"
        And I see "Student Entry 02"
        And I do not see "Add a new entry"
        
        When I go to dataform page "view.php?d=1&view=1&editentries=-1"
        Then I do not see "New entry"
        And "field_1_-1" "field" should not exist        
        
