@mod @mod_dataform @dataformgrading
Feature: Multiple grade items

    #Section: Enable site config.
    @javascript @dataformgrading-enablesiteconfig
    Scenario: Enable site config.
        Given a fresh site for dataform scenario

        # Add activity.
        And the following dataform exists:
            | course                | C1        |
            | idnumber              | dataform1 |
            | name                  | Dataform enable site config |
            | intro                 | Dataform enable site config |

        # Log in as a teacher.
        Then I log in as "teacher1"
        And I follow "Course 1"

        # The Grade items link does not appear.
        When I follow "Dataform enable site config"
        Then "Grade items" "link" should not exist in the "Administration" "block"

        # I cannot access it via direct url.
        When I go to dataform page "grade/items.php?d=1"
        Then I do not see "Grade items in Dataform enable site config"

        And I log out

        # Enable site config.
        Then I log in as "admin"
        And I set the following administration settings values:
            | Allow multiple grade items | 1 |
        And I log out

        # Log in as teacher.
        Then I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Dataform enable site config"

        # Can now access the grade items page.
        When I follow "Grade items"
        Then I see "Grade item 0:"
    #:Section

    #Section: Add a grade item without grade settings.
    @javascript @dataformgrading-multigradeitems
    Scenario: Add a grade item without grade settings.
        Given a fresh site for dataform scenario
        Given the following config values are set as admin:
          | dataform_multigradeitems | 1 |

        #Section: Add activity
        And the following dataform exists:
            | course                | C1        |
            | idnumber              | dataform1 |
            | name                  | Test multiple grade items |
            | intro                 | Test multiple grade items |
        #:Section

        #Section: Add view
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |
        #:Section

        Then I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test multiple grade items"
        And I follow "Grade items"

        #Section: Add a grade item without grade settings.
        When I click on "Grade item 0:" "link"
        And I press "Save changes"
        Then I do not see "Grade item 0: Test multiple grade items"
        And I do not see "Grade item 1:"
        #:Section

        #Section: Add first grade item
        When I click on "Grade item 0:" "link"
        And I set the following fields to these values:
        | gradeitem[0][modgrade_type]   | Point                 |
        | gradeitem[0][modgrade_point]  | 90        |
        And I press "Save changes"
        Then I see "Grade item 0: Test multiple grade items"
        #:Section

        #Section: Add second grade item without grade settings.
        When I click on "Grade item 1:" "link"
        And I set the following fields to these values:
        | gradeitem[1][itemname]        | Test multiple grade items     |
        And I press "Save changes"
        Then I see "Grade item 0: Test multiple grade items"
        And I do not see "Grade item 1: Test multiple grade items"
        And I do not see "Grade item 1: Test multiple grade items_1"
        And I do not see "Grade item 2:"
        #:Section

        #Section: Add second grade item with the same name as the first.
        When I click on "Grade item 1:" "link"
        And I set the following fields to these values:
        | gradeitem[1][itemname]        | Test multiple grade items    |
        | gradeitem[1][modgrade_type]   | Scale                 |
        And I press "Save changes"
        Then I see "Grade item 0: Test multiple grade items"
        And I see "Grade item 1: Test multiple grade items_1"
        And I see "Grade item 2:"
        #:Section

    #:Section

    #Section: Add two grade items.
    @javascript @dataformgrading-addtwogradeitems
    Scenario: Add two grade items.
        Given a fresh site for dataform scenario
        Given the following config values are set as admin:
          | dataform_multigradeitems | 1 |

        #Section: Add activity
        And the following dataform exists:
            | course                | C1        |
            | idnumber              | dataform1 |
            | name                  | Test multiple grade items |
            | intro                 | Test multiple grade items |
        #:Section

        #Section: Add view
        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |
        #:Section

        #Section: Add entries
        And the following dataform "entries" exist:
            | dataform  | user          | group | timecreated   | timemodified  |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student1      |       |               |               |
            | dataform1 | student2      |       |               |               |
            | dataform1 | student2      |       |               |               |
            | dataform1 | student3      |       |               |               |
        #:Section

        Then I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test multiple grade items"

        #Section: Add default grade item
        And I follow "Edit settings"
        And I set the following fields to these values:
        | Type              | Point     |
        | Maximum points    | 4        |
        | Grade calculation | ##numentries##    |
        And I press "Save and display"
        #:Section

        #Section: Add second grade item
        And I follow "Grade items"
        And I see "Grade item 0: Test multiple grade items"
        And I see "Grade item 1:"
        And I set the following fields to these values:
        | gradeitem[1][itemname]        | Second grade item     |
        | gradeitem[1][modgrade_type]   | Point                 |
        | gradeitem[1][modgrade_point]  | 32        |
        | gradeitem[1][gradecalc]       | ##numentries## * 2   |
        And I press "Save changes"
        #:Section

        And I log out

        #Section: Student 1 can see grades for items.
        Then I log in as "student1"
        And I follow "Grades" in the user menu
        And I follow "Course 1"
        Then the following should exist in the "user-grade" table:
            | Grade item                | Grade | Range | Percentage |
            | Test multiple grade items | 4.00  | 0–4   | 100.00 %   |
            | Second grade item         | 8.00  | 0–32  | 25.00 %   |
            | Course total              | 12.00 | 0–36  | 33.33 %   |
        And I log out
        #:Section
    #:Section

