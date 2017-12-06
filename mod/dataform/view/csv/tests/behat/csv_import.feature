@mod @mod_dataform @dataformview @dataformview_csv @_file_upload
Feature: Import entries

    @javascript
    Scenario: Add dataform entries
        Given I start afresh with dataform "Test csv import"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test csv import"

        # Add a csv view and put author:username edit and delete patterns
        When I go to manage dataform "views"
        And I set the field "Add a view" to "csv"
        And I expand all fieldsets
        And I set the field "Name" to "View 01"
        And I set the field "Entry template" to
            """
            [[EAU:username]]
            [[EGR:name]]
            [[EGR:idnumber]]
            [[ETM:timecreated]]
            [[ETM:timemodified]]
            [[EAC:edit]]
            [[EAC:delete]]
            """
        And I press "Save changes"       

        Then I set "View 01" as default view

        Then I follow "Browse"
        
        Then I follow "Import"
        And I expand all fieldsets
        ##When I upload "lib/tests/fixtures/upload_users.csv" file to "File" filemanager
        And I set the field "CSV Text" to 
            """
            EAU:username,EGR:idnumber,ETM:timecreated,ETM:timemodified
            student1,G1,October 28 2013 16:13,October 27 2013
            student2,G2,9 March 2014,9 March 2014 +1 day
            student3,,,
            """
        And I press "Test"
        Then I see "No errors found"
        When I press "submitbutton"
        Then I see "3 entry(s) added"
        
        When I follow "View 01"
        Then I see "student1"
        And I see "student2"
        And I see "student3"
        And I see "Group 1"
        And I see "Group 2"
        And I see "28 October 2013, 4:13 PM"
        And I do not see "27 October 2013"
        And I see "9 March 2014"
        And I see "10 March 2014"

