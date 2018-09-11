@block @block_messageteacher
Feature: List Teachers
    In order to easily message my teacher
    As a student
    I need to see a list of my teachers in a block on my course page

    Background:
        Given the following "users" exist:
            | username     | email                    | firstname | lastname |
            | teststudent  | teststudent@example.com  | Test      | Student  |
            | testteacher1 | testteacher1@example.com | Test      | Teacher1 |
            | testteacher2 | testteacher2@example.com | Test      | Teacher2 |
            | testteacher3 | testteacher3@example.com | Test      | Teacher3 |
        And the following "categories" exist:
            | name       | category | idnumber |
            | Category 1 | 0        | CAT1     |
        And the following "courses" exist:
            | fullname | shortname | category | format |
            | Course 1 | course1   | CAT1     | topics |
            | Course 2 | course2   | CAT1     | topics |

    Scenario: There are no teachers on the course
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should not see "Message My Teacher"

    Scenario: There is one teacher on the course
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    Scenario: There is are two teachers on the course
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    Scenario: There is more than one teacher role, and a teacher exist with each role
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
            | testteacher3 | course1 | teacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3,4 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_grouping
    Scenario: Grouping is enabled, but the course has no groups
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_grouping
    Scenario: Grouping is enabled, but the student isn't part of a group
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
        And the following "groups" exist:
            | name    | description | course  | idnumber |
            | Group 1 | Anything    | course1 | group1   |
        And the following "group members" exist:
            | user         | group  |
            | testteacher2 | group1 |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "You're not a member of any group" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher2" in the ".block_messageteacher" "css_element"


    @block_messageteacher_grouping
    Scenario: Grouping is enabled, and the student is part of a group, but the teachers aren't in the group
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
        And the following "groups" exist:
            | name    | description | course  | idnumber |
            | Group 1 | Anything    | course1 | group1   |
        And the following "group members" exist:
            | user         | group  |
            | teststudent  | group1 |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Teacher not yet assigned to your group" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher2" in the ".block_messageteacher" "css_element"

    @block_messageteacher_grouping
    Scenario: Grouping is enabled, and the student is part of a group, and one teacher is in the group, the other teacher is in a different group
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following "groups" exist:
            | name    | description | course  | idnumber |
            | Group 1 | Anything    | course1 | group1   |
            | Group 2 | Anything    | course1 | group2   |
        And the following "group members" exist:
            | user         | group  |
            | teststudent  | group1 |
            | testteacher2 | group1 |
            | testteacher3 | group2 |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher3" in the ".block_messageteacher" "css_element"
        And I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_grouping
    Scenario: Grouping is enabled, and the student is part of a group, and both teachers are in the group
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following "groups" exist:
            | name    | description | course  | idnumber |
            | Group 1 | Anything    | course1 | group1   |
            | Group 2 | Anything    | course1 | group2   |
        And the following "group members" exist:
            | user         | group  |
            | teststudent  | group1 |
            | testteacher2 | group1 |
            | testteacher3 | group1 |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_grouping
    Scenario: Grouping is disabled, and the student is part of a group, and one teacher is in the group, the other teacher is in a different group
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher2 | course1 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following "groups" exist:
            | name    | description | course  | idnumber |
            | Group 1 | Anything    | course1 | group1   |
            | Group 2 | Anything    | course1 | group2   |
        And the following "group members" exist:
            | user         | group  |
            | teststudent  | group1 |
            | testteacher2 | group1 |
            | testteacher3 | group2 |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles | 3 |
            | groups | 0 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_categories
    Scenario: Category teachers are enabled, but there are no teachers in the category or on the course
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3 |
            | includecoursecat | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should not see "Message My Teacher"

    @block_messageteacher_categories
    Scenario: Category teachers are enabled, and there is one teacher in the category but none on the course
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
        And the following category enrolments exist:
            | user         | category | role           |
            | testteacher2 | CAT1     | editingteacher |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3 |
            | includecoursecat | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"
        And I should not see "Test Teacher3" in the ".block_messageteacher" "css_element"

    @block_messageteacher_categories
    Scenario: Category teachers are enabled, and there is one teacher on the course but none in the category
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3 |
            | includecoursecat | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"
        And I should not see "Test Teacher2" in the ".block_messageteacher" "css_element"

    @block_messageteacher_categories
    Scenario: Category teachers are enabled, and there is one teacher on the course and one in the category
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following category enrolments exist:
            | user         | category | role           |
            | testteacher2 | CAT1     | editingteacher |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3 |
            | includecoursecat | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_categories
    Scenario: Category teachers are enabled, and there is one teacher on the course and one in the category, with different roles
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following category enrolments exist:
            | user         | category | role    |
            | testteacher2 | CAT1     | teacher |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3,4 |
            | includecoursecat | 1 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        And I should see "Test Teacher2" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"

    @block_messageteacher_categories
    Scenario: Category teachers are disabled, and there is one teacher on the course and one in the category
        Given the following "course enrolments" exist:
            | user         | course  | role           | enrol  |
            | teststudent  | course1 | student        | manual |
            | testteacher1 | course2 | editingteacher | manual |
            | testteacher3 | course1 | editingteacher | manual |
        And the following category enrolments exist:
            | user         | category | role           |
            | testteacher2 | CAT1     | editingteacher |
        And there is an instance of messageteacher on "Course 1"
        And messageteacher has the following settings:
            | roles            | 3 |
            | includecoursecat | 0 |
        And I log in as "teststudent"
        When I follow "Course 1"
        Then I should see "Message My Teacher"
        And I should see "Test Teacher3" in the ".block_messageteacher" "css_element"
        But I should not see "Test Teacher1" in the ".block_messageteacher" "css_element"
        And I should not see "Test Teacher2" in the ".block_messageteacher" "css_element"

