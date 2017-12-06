@availability @availability_dataformcontent @availability_dataformcontent_duplication @mod_dataform
Feature: Condition stands activity duplication
    In order to control student access to activities from a Dataform dashboard
    As a teacher
    I need to set dataformcontent conditions which prevent student access

    Background:
    Given a fresh site for dataform scenario
    And the following config values are set as admin:
        | enableavailability | 1 |
    
    #Section: Duplicate a restricted dataform activity via dataform preset.
    @javascript
    Scenario: Duplicate a restricted dataform activity via dataform preset.

        #Section: Set up.
        And the following "activities" exist:
            | activity | course | idnumber  | name            | section | individualized |
            | dataform | C1     | dataform1 | Dashboard       | 1       | 1              |

        And the following dataform "fields" exist:
            | name                  | type          | dataform  |
            | Conditional Activity  | text          | dataform1 |
            | From                  | time          | dataform1 |
            | To                    | time          | dataform1 |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   |
            | List     | aligned   | dataform1 | 1         |
        #:Section

        #Section: Add a conditional Dataform.
        And the following "activities" exist:
            | activity | course | idnumber  | name                      | section   |
            | dataform | C1     | dataform2 | Restricted Activity       | 2         |
            | dataform | C1     | dataform3 | Restricted New Activity   | 3         |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Restricted Activity"

        And I follow "Edit settings"        
        And I expand all fieldsets
        And I click on "Add restriction..." "button"
        And I click on "Dataform content" "button" in the "Add restriction..." "dialogue"
        And I click on ".availability-item .availability-eye img" "css_element"
        And I set the field "Dataform content" to "Dashboard"
        And I press "Save and return to course"
        
        And I log out
        #:Section

        #Section: Admin duplicates the restricted activity.
        Then I log in as "admin"
        And I follow "Courses"
        And I follow "Course 1"
        And I should not see "Not available unless: this activity is listed in" in the "Topic 3" "section"

        And I follow "Restricted Activity"        
        And I go to manage dataform "presets"
        
        # Add a preset without user data.
        And I expand all fieldsets
        And I press "id_add"
        
        # Share the preset.
        #And I click on "img[title=Share]" "css_element" in the "-without-user-data" "table_row"
        
        # Apply in another Dataform.
        And I follow "Course 1"
        And I follow "Restricted New Activity"        
        And I go to manage dataform "presets"
        And I click on "img[title=Apply]" "css_element" in the "-without-user-data" "table_row"

        And I follow "Course 1"
        Then I should see "Not available unless: this activity is listed in Dashboard" in the "Topic 3" "section"
        
        And I log out
        #:Section
    #:Section
    
    #Section: Duplicate a restricted activity in the same course.
    @javascript
    Scenario: Duplicate a restricted activity in the same course

        #Section: Set up.
        And the following "activities" exist:
            | activity | course | idnumber  | name            | section | individualized |
            | dataform | C1     | dataform1 | Dashboard       | 1       | 1              |

        And the following dataform "fields" exist:
            | name                  | type          | dataform  |
            | Conditional Activity  | text          | dataform1 |
            | From                  | time          | dataform1 |
            | To                    | time          | dataform1 |

        And the following dataform "views" exist:
            | name     | type      | dataform  | default   | submission |
            | List     | aligned   | dataform1 | 1         |            |

        And view "List" in dataform "1" has the following view template:
            """
            <div>
            ##entries##
            </div>
            """

        And the following dataform "views" exist:
            | name            | type      | dataform  |
            | Manage List     | aligned   | dataform1 |

        And view "Manage List" in dataform "1" has the following entry template:
            """
            [[EAU:edit]]
            [[Conditional Activity]]
            [[From]]
            [[To]]
            """
        #:Section

        #Section: Add a conditional Dataform.
        And the following "activities" exist:
            | activity | course | idnumber  | name                  | section   |
            | dataform | C1     | dataform2 | Restricted Activity   | 2         |

        And I log in as "teacher1"
        And I follow "Course 1"
        And I follow "Restricted Activity"

        And I follow "Edit settings"        
        And I expand all fieldsets
        And I click on "Add restriction..." "button"
        And I click on "Dataform content" "button" in the "Add restriction..." "dialogue"
        And I click on ".availability-item .availability-eye img" "css_element"
        And I set the field "Dataform content" to "Dashboard"
        And I press "Save and return to course"
        
        And I log out
        #:Section

        #Section: Student 1 cannot yet see the restricted activity.
        When I log in as "student1"
        And I follow "Course 1"
        Then I should not see "Restricted Activity" in the "region-main" "region"
        And I log out
        #:Section

        #Section: Teacher grants access to Student 1.
        And the following dataform "entries" exist:
            | dataform  | user           | Conditional Activity         |
            | dataform1 | student1       | Restricted Activity          |
            | dataform1 | student2       | Restricted Original Activity |
        #:Section

        #Section: Student 1 can now see the restricted activity.
        When I log in as "student1"
        And I follow "Course 1"
        Then I should see "Restricted Activity" in the "Topic 2" "section"
        And I log out
        #:Section

        #Section: Student 2 cannot see the restricted activity.
        When I log in as "student2"
        And I follow "Course 1"
        Then I should not see "Restricted Activity" in the "Topic 2" "section"
        And I log out
        #:Section

        #Section: Teacher duplicates the restricted activity.
        Then I log in as "teacher1"
        And I follow "Course 1"
        And I turn editing mode on

        And I duplicate "Restricted Activity" activity
        And I wait until section "2" is available

        And I follow "Restricted Activity"
        And I follow "Edit settings"
        And I set the field "Name" to "Restricted Original Activity"
        And I press "Save and return to course"        
        
        And I log out
        #:Section

        #Section: Student 1 can see the restricted activity.
        When I log in as "student1"
        And I follow "Course 1"
        Then I should see "Restricted Activity" in the "Topic 2" "section"
        And I should not see "Restricted Original Activity" in the "Topic 2" "section"
        And I log out
        #:Section

        #Section: Student 2 cannot see the restricted activity.
        When I log in as "student2"
        And I follow "Course 1"
        Then I should not see "Restricted Activity" in the "Topic 2" "section"
        And I should see "Restricted Original Activity" in the "Topic 2" "section"
        And I log out
        #:Section

    #:Section
    
