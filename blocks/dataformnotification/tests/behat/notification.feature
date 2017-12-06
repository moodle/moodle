@javascript @block @block_dataformnotification @mod_dataform @dataformrule @dataformnotificationtest
Feature: Dataform notifications
  In order to monitor events and receive Dataform notifications
  As a user
  I can create a new rule and receive notification

    Background:
        Given I start afresh with dataform "Test Dataform Notification Rules"

        And the following dataform "fields" exist:
            | name      | type      | dataform  |
            | Field 01  | text      | dataform1 |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | View 01  | aligned   | dataform1 | 1         |

        And the following dataform "views" exist:
            | name     | type      | dataform  | submission |
            | View 02  | grid   | dataform1 |            |

        And view "View 02" in dataform "1" has the following view template:
            """
            <div>Number of entries: ##numentriesfiltered##</div>
            ##entries##
            """

        And view "View 02" in dataform "1" has the following entry template:
            """
            <div class="entry">
                <h2>[[EAU:name]]</h2>
                This entry has been updated to [[Field 01]].
            </div>
            """

    #Section: Notify recipients on entry created
    Scenario: Notify recipients on entry created
        Given the following dataform notification rule exists:
            | name          | New entry                                 |
            | dataform      | dataform1                                 |
            | enabled       | 1                                         |
            | from          |                                           |
            | to            |                                           |
            | views         |                                           |
            | events        | entry_created                             |
            | messagetype   | 1                                         |
            | subject       | Test notification - New entry added       |
            | contenttext   | This entry has been created.              |
            | contentview   |                                           |
            | messageformat |                                           |
            | sender        |                                           |
            | recipientadmin    |  1                                        |
            | recipientsupport  |                                           |
            | recipientauthor   |  1                                        |
            | recipientrole     |                                           |
            | recipientusername |  student1,assistant1                      |
            | recipientemail    |                                           |
            #| permission1   | student Allow mod/dataform:viewaccess     |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test Dataform Notification Rules"

        When I follow "Add a new entry"
        And I press "Save"
        And I follow "Messages" in the user menu
        And I follow "Do not reply to this email (1)"
        Then I see "This entry has been created."
        And I log out

        When I log in as "student1"
        And I follow "Messages" in the user menu
        And I follow "Do not reply to this email (1)"
        Then I see "This entry has been created."
        And I log out

        When I log in as "assistant1"
        And I follow "Messages" in the user menu
        And I follow "Do not reply to this email (1)"
        Then I see "This entry has been created."
        And I log out

        When I log in as "admin"
        And I follow "Messages" in the user menu
        And I follow "Do not reply to this email (1)"
        Then I see "This entry has been created."
        And I log out

    #:Section

    #Section: Notify recipients on entry updated via a designated view
    Scenario: Notify recipients on entry updated via a designated view
        Given the following dataform "entries" exist:
            | dataform  | user           |
            | dataform1 | student2       |


        And the following dataform notification rule exists:
            | name          | Updated entry                             |
            | dataform      | dataform1                                 |
            | enabled       | 1                                         |
            | from          |                                           |
            | to            |                                           |
            | views         |                                           |
            | events        | entry_updated                             |
            | messagetype   | 1                                         |
            | subject       | Test notification - entry updated         |
            | contenttext   |                                           |
            | contentview   | View 02                                   |
            | messageformat | 1                                          |
            | sender        | author                                     |
            | recipientadmin    |                                           |
            | recipientsupport  |                                           |
            | recipientauthor   |  1                                        |
            | recipientrole     |                                           |
            | recipientusername |  student1,teacher1                        |
            | recipientemail    |                                           |
            #| permission1   | student Allow mod/dataform:viewaccess     |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test Dataform Notification Rules"
        
        When I follow "id_editentry1"
        And I set the field "field_1_1" to "the big bang theory"
        And I press "Save"

        And I follow "Messages" in the user menu
        And I follow "Student 2 (1)"
        Then I see "Number of entries: 1"
        And I see "This entry has been updated to the big bang theory."
        And I log out

        When I log in as "student1"
        And I follow "Messages" in the user menu
        And I follow "Student 2 (1)"
        Then I see "Number of entries: 1"
        And I see "This entry has been updated to the big bang theory."
        And I log out

    #:Section

    #Section: Notify recipients on entry created with specific content
    Scenario: Notify recipients on entry created
        Given the following dataform notification rule exists:
            | name          | New entry                                 |
            | dataform      | dataform1                                 |
            | enabled       | 1                                         |
            | from          |                                           |
            | to            |                                           |
            | views         |                                           |
            | events        | entry_created                             |
            | messagetype   | 1                                         |
            | subject       | Test notification - New entry added       |
            | contenttext   | This entry has been created.              |
            | contentview   |                                           |
            | messageformat |                                           |
            | sender        |                                           |
            | recipientadmin    |                                           |
            | recipientsupport  |                                           |
            | recipientauthor   |  1                                        |
            | recipientrole     |                                           |
            | recipientusername |                                           |
            | recipientemail    |                                           |
            #| permission1   | student Allow mod/dataform:viewaccess     |
            | search1           | AND#1,content##=#Choose me   |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Test Dataform Notification Rules"

        When I follow "Add a new entry"
        And I press "Save"
        And I follow "Messages" in the user menu
        Then I do not see "Do not reply to this email (1)"

        And I am on homepage
        And I follow "Course 1"
        And I follow "Test Dataform Notification Rules"
        When I follow "Add a new entry"
        And I set the field "field_1_-1" to "Choose me"
        And I press "Save"
        And I follow "Messages" in the user menu
        And I follow "Do not reply to this email (1)"
        Then I see "This entry has been created."

    #:Section
