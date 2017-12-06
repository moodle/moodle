@mod @mod_dataform @dataformfield @dataformfield_entrystate
Feature: Completion

    @javascript
    Scenario: Completion requires specific grade
        Given a fresh site for dataform scenario

        # Dataform activity
        And the following dataform exists:
            | course                | C1        |
            | idnumber              | dataform1 |
            | name                  | Dataform completion requires specific grade |
            | intro                 | Dataform completion requires specific grade |
            | grade                 | 10        |
            | gradeitem 0 ca        | SUM(##2:State##)/2 |

        # Site completion enabling
        And the following config values are set as admin:
            | enablecompletion    | 1 |
            | enableavailability  | 1 |

        # Course completion enabling
        And the following config values are set as admin:
            | enablecompletion | 1 | moodlecourse |

        # Course completion enabling
        Then I log in as "teacher1"
        And I follow "Course 1"
        And I turn editing mode on
        And I click on "Edit settings" "link" in the "Administration" "block"
        And I expand all fieldsets
        And I set the field "Enable completion tracking" to "Yes"
        And I press "Save and display"


        # Add a field with  Submitted and Approved  states
        Then I follow "Dataform completion requires specific grade"
        And I go to manage dataform "fields"
        And I set the field "Add a field" to "entrystate"
        And I expand all fieldsets
        And I set the field "Name" to "State"
        And I set the field "States" to
            """
            Draft
            Submitted
            Approved
            """
        And I press "Save changes"

        # Add a default view
        Then I go to manage dataform "views"
        And I add a dataform view "aligned" with "View 01"
        And I set "View 01" as default view

        ## Completion
        Then I follow "Edit settings"
        And I expand all fieldsets
        And I set the field "Completion tracking" to "Show activity as complete when conditions are met"
        And I set the field "completionspecificgradeenabled" to "1"
        And I set the field "completionspecificgrade" to "3"
        And I press "Save and display"

        # Add some entries
        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student2      |       |               |               |
            | dataform1 | student2      |       |               |               |
            | dataform1 | student3      |       |               |               |
            | dataform1 | teacher1      |       |               |               |

        Then I log out

        # Student 1 not yet completed
        Then I log in as "student1"
        And I follow "Course 1"
        And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_dataform ')]/descendant::img[@alt='Not completed: Dataform completion requires specific grade']" "xpath_element"
        And I log out

        # Teacher updates entries
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform completion requires specific grade"
        And I follow "entrystate_1_2"
        And I follow "entrystate_2_2"
        And I follow "entrystate_3_2"
        And I follow "entrystate_5_2"
        And I follow "entrystate_6_1"
        And I log out

        # Student 1 completed
        And I log in as "student1"
        And I follow "Course 1"
        And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_dataform ')]/descendant::img[@alt='Completed: Dataform completion requires specific grade']" "xpath_element"
        And I log out

        # Teacher reverts approval
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform completion requires specific grade"
        And I follow "View 01"
        And I follow "entrystate_3_0"
        And I log out

        # Student 1 not yet completed
        Then I log in as "student1"
        And I follow "Course 1"
        And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_dataform ')]/descendant::img[@alt='Not completed: Dataform completion requires specific grade']" "xpath_element"
        And I log out

        # Teacher approves another one for Student 1
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform completion requires specific grade"
        And I follow "View 01"
        And I follow "entrystate_4_2"
        And I log out

        # Student 1 completed
        And I log in as "student1"
        And I follow "Course 1"
        And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_dataform ')]/descendant::img[@alt='Completed: Dataform completion requires specific grade']" "xpath_element"
