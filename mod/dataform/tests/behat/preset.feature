@mod @mod_dataform @dataformactivity @dataformpreset @_file_upload
Feature: Manage Dataform presets

    @javascript
    Scenario: Add presets
        #Section: Setup.
        Given a fresh site with dataform "Preset Dataform"

        And the following dataform "fields" exist:
            | name          | type          | dataform  |
            | Field Text    | text          | dataform1 |
            | Field File    | file          | dataform1 |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        ## Entries
        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  | Field Text                |
            | dataform1 | student1      |       |               |               | 1 Entry by Student 01     |
            | dataform1 | student2      |       |               |               | 2 Entry by Student 02     |
            | dataform1 | student3      |       |               |               | 3 Entry by Student 03     |

        And the following "courses" exist:
            | fullname | shortname | category  |
            | Course 2 | C2        | 0         |
        #:Section

        Given I log in as "admin"
        And I follow "Courses"
        And I follow "Course 1"
        And I follow "Preset Dataform"

        And I go to manage dataform "presets"

        #Section: Add a preset without user data (default settings).
        Then I expand all fieldsets
        And I press "id_add"
        And I should see "Preset_Dataform-dataform-preset" in the "table.coursepresets" "css_element"
        And I should see "-without-user-data" in the "table.coursepresets" "css_element"
        #:Section

        #Section: Add a preset with user data.
        Then I expand all fieldsets
        And I set the field "id_preset_data" to "with user data"
        And I press "id_add"
        And I should see "-with-user-data" in the "table.coursepresets" "css_element"
        #:Section

        #Section: Add a preset with user data ANONYMIZED.
        #Then I expand all fieldsets
        #And I set the field "id_preset_data" to "with user data anonymized"
        #And I press "id_add"
        #And I should see "-with-user-data-anonymized" in the "table.coursepresets" "css_element"
        #:Section

        #Section: Add preset from file.
        Then I expand all fieldsets
        And I click on "Upload preset from file" "radio"
        And I upload "mod/dataform/tests/fixtures/Preset-dataform-preset.mbz" file to "Upload" filemanager
        And I press "id_add"
        And I should see "Preset-dataform-preset" in the "table.coursepresets" "css_element"
        #:Section

        ### SHARE PRESETS
        # There are no site presets yet
        And I should not see "Preset_Dataform-dataform-preset" in the "table.sitepresets" "css_element"

        #Section: Share the preset WITHOUT user data.
        Then I click on "img[title=Share]" "css_element" in the "-without-user-data" "table_row"
        And I should see "Preset_Dataform-dataform-preset" in the "table.sitepresets" "css_element"
        And I should see "-without-user-data" in the "table.sitepresets" "css_element"
        #:Section

        #Section: Share the preset WITH user data.
        Then I click on "img[title=Share]" "css_element" in the "-with-user-data" "table_row"
        And I should see "-with-user-data" in the "table.sitepresets" "css_element"
        #:Section

        #Section: Share the preset with user data ANONYMIZED.
        #Then I click on "img[title=Share]" "css_element" in the "-with-user-data-anonymized" "table_row"
        #And I should see "-with-user-data-anonymized" in the "table.sitepresets" "css_element"
        #:Section

        #Section: Share the uploaded preset.
        Then I click on "img[title=Share]" "css_element" in the "Preset-dataform-preset" "table_row"
        And I should see "Preset-dataform-preset" in the "table.sitepresets" "css_element"
        #:Section

        ### APPLY PRESET WITHOUT USER DATA
        #Section: Apply in same course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 1"
        And I turn editing mode on
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset Without User Data"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "-without-user-data" "table_row"
        And I see "View 01"

        And I delete this dataform
        #:Section

        #Section: Apply in a different course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 2"
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset Without User Data in another course"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "-without-user-data" "table_row"
        And I see "View 01"

        And I delete this dataform
        #:Section

        ### APPLY PRESET WITH USER DATA
        #Section: Apply in same course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 1"
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset With User Data"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "-with-user-data" "table_row"
        And I see "View 01"

        And I delete this dataform
        #:Section

        #Section: Apply in a different course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 2"
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset With User Data in another course"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "-with-user-data" "table_row"
        And I see "View 01"

        And I delete this dataform
        #:Section

        ### APPLY UPLOADED PRESET
        #Section: Apply in same course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 1"
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset uploaded"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "Preset-dataform-preset" "table_row"
        And I see "View 01"

        And I delete this dataform
        #:Section

        #Section: Apply in a different course.
        Then I am on homepage
        And I follow "Courses"
        And I follow "Course 2"
        And I add a "Dataform" to section "1"
        And I set the field "Name" to "Dataform Preset With User Data in another course"
        And I press "Save and display"
        And I do not see "View 01"

        Then I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "Preset-dataform-preset" "table_row"
        And I see "View 01"
        #:Section

