@core @core_course @javascript
Feature: Activities group mode icons behavior in course page

  Scenario Outline: Teachers should see group mode icons in both view and edit mode
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         |
      | Course 1 | C1        | <courseformat> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                       | intro                         | course | idnumber | groupmode |
      | forum    | No groups forum            | Forum with groupmode = 0      | C1     | forum1   | 0         |
      | data     | Visible groups database    | Database with groupmode = 2   | C1     | data1    | 2         |
      | assign   | Separate groups assignment | Assignment with groupmode = 1 | C1     | assign1  | 1         |
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode <editmode>
    Then "No groups forum" "core_courseformat > Activity groupmode" <nogroups>
    And "Visible groups database" "core_courseformat > Activity groupmode" should exist
    And "Separate groups assignment" "core_courseformat > Activity groupmode" should exist

    Examples:
      | courseformat | editmode | nogroups         |
      | topics       | on       | should exist     |
      | topics       | off      | should not exist |
      | weeks        | on       | should exist     |
      | weeks        | off      | should not exist |

  Scenario Outline: Students should not see group mode icons in both view and edit mode
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         |
      | Course 1 | C1        | <courseformat> |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following "activities" exist:
      | activity | name                       | intro                         | course | idnumber | groupmode |
      | forum    | No groups forum            | Forum with groupmode = 0      | C1     | forum1   | 0         |
      | data     | Visible groups database    | Database with groupmode = 2   | C1     | data1    | 2         |
      | assign   | Separate groups assignment | Assignment with groupmode = 1 | C1     | assign1  | 1         |
    When I am on the "C1" "Course" page logged in as "student1"
    Then "No groups forum" "core_courseformat > Activity groupmode" should not exist
    And "Visible groups database" "core_courseformat > Activity groupmode" should not exist
    And "Separate groups assignment" "core_courseformat > Activity groupmode" should not exist
    # Giving moodle/course:manageactivities capability would let them see the indicators.
    And the following "role capability" exists:
      | role                           | student |
      | moodle/course:manageactivities | allow   |
    And I am on the "C1" "Course" page
    And I should see "Group mode: Visible groups" in the "Visible groups database" "core_courseformat > Activity groupmode indicator"
    And I should see "Group mode: Separate groups" in the "Separate groups assignment" "core_courseformat > Activity groupmode indicator"

    Examples:
      | courseformat |
      | topics       |
      | weeks        |

  Scenario Outline: Resources don't support group mode never show groupmode icon in the course page
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         |
      | Course 1 | C1        | <courseformat> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                 | intro                   | course | idnumber | groupmode |
      | page     | No groups page       | Page with groupmode = 0 | C1     | page0    | 0         |
      | page     | Visible groups page  | Page with groupmode = 2 | C1     | page2    | 2         |
      | page     | Separate groups page | Page with groupmode = 1 | C1     | page1    | 1         |
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    Then "No groups page" "core_courseformat > Activity groupmode" should not exist
    And "Visible groups page" "core_courseformat > Activity groupmode" should not exist
    And "Separate groups page" "core_courseformat > Activity groupmode" should not exist
    And I am on "Course 1" course homepage with editing mode off
    And "No groups page" "core_courseformat > Activity groupmode" should not exist
    And "Visible groups page" "core_courseformat > Activity groupmode" should not exist
    And "Separate groups page" "core_courseformat > Activity groupmode" should not exist

    Examples:
      | courseformat |
      | topics       |
      | weeks        |

  Scenario Outline: Group mode icon behavior in the course page when forcing group mode in course settings
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         | groupmodeforce | groupmode   |
      | Course 1 | C1        | <courseformat> | 1              | <groupmode> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                       | intro                         | course | idnumber | groupmode |
      | forum    | No groups forum            | Forum with groupmode = 0      | C1     | forum1   | 0         |
      | data     | Visible groups database    | Database with groupmode = 2   | C1     | data1    | 2         |
      | assign   | Separate groups assignment | Assignment with groupmode = 1 | C1     | assign1  | 1         |
      | page     | No groups page             | Doesn't support groupmode     | C1     | page     | 1         |
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    Then I should see "Group mode: <forcedname>" in the "No groups forum" "core_courseformat > Activity groupmode indicator"
    And I should see "Group mode: <forcedname>" in the "Visible groups database" "core_courseformat > Activity groupmode indicator"
    And I should see "Group mode: <forcedname>" in the "Separate groups assignment" "core_courseformat > Activity groupmode indicator"
    And "<shortname>" "button" should not exist in the "Separate groups assignment" "core_courseformat > Activity groupmode"
    And "No groups page" "core_courseformat > Activity groupmode" should not exist

    Examples:
      | courseformat | groupmode | forcedname      | shortname |
      | topics       | 0         | No groups       | No groups |
      | topics       | 1         | Separate groups | Separate  |
      | topics       | 2         | Visible groups  | Visible   |
      | weeks        | 0         | No groups       | No groups |
      | weeks        | 1         | Separate groups | Separate  |
      | weeks        | 2         | Visible groups  | Visible   |

  Scenario Outline: The group mode control is reachable at every viewport width
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format         |
      | Course 1 | C1        | <courseformat> |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name       | intro                         | course | idnumber | groupmode |
      | forum    | Forum      | Forum with groupmode = 0      | C1     | forum1   | 0         |
      | data     | Database   | Database with groupmode = 2   | C1     | data1    | 2         |
      | assign   | Assignment | Assignment with groupmode = 1 | C1     | assign1  | 1         |
      | page     | Page       | Doesn't support groupmode     | C1     | page     | 1         |
    And I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    Then "No groups" "button" in the "Forum" "core_courseformat > Activity groupmode" should be visible
    And "Separate" "button" in the "Assignment" "core_courseformat > Activity groupmode" should be visible
    And "Visible" "button" in the "Database" "core_courseformat > Activity groupmode" should be visible
    And I change viewport size to "800x1024"
    And "No groups" "button" in the "Forum" "core_courseformat > Activity groupmode" should be visible
    And "Separate" "button" in the "Assignment" "core_courseformat > Activity groupmode" should be visible
    And "Visible" "button" in the "Database" "core_courseformat > Activity groupmode" should be visible
    And I change viewport size to "480x800"
    And "No groups" "button" in the "Forum" "core_courseformat > Activity groupmode" should be visible
    And "Separate" "button" in the "Assignment" "core_courseformat > Activity groupmode" should be visible
    And "Visible" "button" in the "Database" "core_courseformat > Activity groupmode" should be visible
    And "Page" "core_courseformat > Activity groupmode" should not exist

    Examples:
      | courseformat |
      | topics       |
      | weeks        |
