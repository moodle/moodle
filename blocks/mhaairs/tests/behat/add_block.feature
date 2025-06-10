@block @block_mhaairs @block_mhaairs-add-block @javascript @_switch_window
Feature: Add block

    Background:
        Given the following "courses" exist:
            | fullname | shortname | category |
            | Course 1 | C1        | 0        |

        And the following "users" exist:
            | username | firstname | lastname | email               |
            | teacher1 | Teacher   | One      | teacher1@example.com|
            | student1 | Student   | One      | student1@example.com|

        And the following "course enrolments" exist:
            | user      | course| role          |
            | teacher1  | C1    | editingteacher|
            | student1  | C1    | student       |

    ##/:
    ## Add block 001
    ## When site level customer number and secret are not configured
    ## Then the block in a course should display a warning message
    ##:/
    Scenario: Add block 001
        Given I log in as "admin"
        And I am on "Course 1" course homepage

        When I turn editing mode on
        And I add the "McGraw-Hill AAIRS" block
        Then I should see "The site requires further configuration. Please contact your site admin."
        And I log out

        ## Teacher.
        Given I log in as "teacher1"
        And I am on "Course 1" course homepage
        Then I should see "The site requires further configuration. Please contact your site admin."
        And I log out

        ## Student.
        Given I log in as "student1"
        And I am on "Course 1" course homepage
        Then ".block.block_mhaairs" "css_element" does not exist
    #:Scenario

    ##/:
    ## Add block 002
    ## When site level customer number and secret are configured
    ## And no services are enabled
    ## Then the block in a course should display a warning message
    ## And no services should be available for configuration in block
    ##:/
    Scenario: Add block 002
        Given the mhaairs customer number and shared secret are set

        Given I log in as "admin"
        And I am on "Course 1" course homepage

        When I turn editing mode on
        And I add the "McGraw-Hill AAIRS" block
        And I configure the "McGraw-Hill AAIRS" block
        Then "id_config_MHCampus" "checkbox" should not exist
        And I should see "Available Services have not yet been configured for this site. Please contact your site admin."
    #:Scenario

    ##/:
    ## Add block 003
    ## When site level customer number and secret are configured
    ## And services are enabled
    ## Then the block in a course should display the enabled services
    ##:/
    @block_mhaairs-add-block-003
    Scenario: Add block 003
        Given the mhaairs customer number and shared secret are set

        Given I log in as "admin"
        And I follow "Site administration"
        And I follow "Plugins"
        And I follow "McGraw-Hill AAIRS"
        And I set the field "McGraw-Hill Campus" to "checked"
        And I press "Save changes"

        And I follow "Site home"
        And I follow "Course 1"

        When I turn editing mode on
        And I add the "McGraw-Hill AAIRS" block

        Then I should see "McGraw-Hill Campus" in the ".block.block_mhaairs div.servicelink" "css_element"
    #:Scenario

    ##/:
    ## Add block 004
    ## When site level customer number and secret are configured
    ## And services are enabled
    ## And the block in a course is configured to display no services
    ## Then the block should display a warning message
    ##:/
    Scenario: Add block 004
        Given the mhaairs customer number and shared secret are set

        Given I log in as "admin"
        And I follow "Site administration"
        And I follow "Plugins"
        And I follow "McGraw-Hill AAIRS"
        And I set the field "McGraw-Hill Campus" to "checked"
        And I press "Save changes"

        And I follow "Site home"
        And I follow "Course 1"

        When I turn editing mode on
        And I add the "McGraw-Hill AAIRS" block
        And I configure the "McGraw-Hill AAIRS" block
        And I set the following fields to these values:
          | id_config_MHCampus | 0 |
        And I press "Save changes"

        Then I should see "The block requires further configuration. Please configure the block."
    #:Scenario
