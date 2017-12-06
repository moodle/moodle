@mod @mod_dataform @dataformactivity
Feature: Navigation tabs
    As a teacher
    I can use the navigation tabs to switch between activity browsing and management

    @javascript
    Scenario: Follow the navigation tabs
        Given I start afresh with dataform "Navigation tabs test"
        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Navigation tabs test"
        And I turn editing mode on

        Then I see "Browse"
        And I see "Manage"
        And "Views" "link" should not exist in the ".nav-tabs" "css_element"
        And "Fields" "link" should not exist in the ".nav-tabs" "css_element"

        Then I follow "Manage"
        And I follow "Fields"
        And I follow "Filters"
        And I follow "Access"
        And I follow "Notifications"
        And I follow "CSS"
        And I follow "JS"
        And I follow "Tools"
        And I follow "Presets"
        And I follow "Views"

        Then I follow "Browse"

        And I log out

        # Student cannot see tabs and access the management tabs

        Then I log in as "student1"
        And I follow "Course 1"
        And I follow "Navigation tabs test"

        And I do not see "Browse"
        And I do not see "Manage"
        And I do not see "Views"
        And I do not see "Fields"

        #Then I go to dataform page "view/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "field/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "filter/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "access/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "notification/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "css.php?d=1&cssedit=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "js.php?d=1&jsedit=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "tool/index.php?d=1"
        #And I see "error/accessdenied"

        #Then I go to dataform page "preset/index.php?d=1"
        #And I see "error/accessdenied"

