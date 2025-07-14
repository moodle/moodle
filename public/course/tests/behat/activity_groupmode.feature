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
    Then "Separate groups" "icon" should not exist in the "No groups forum" "activity"
    And "Visible groups" "icon" should not exist in the "No groups forum" "activity"
    And "Separate groups" "icon" should not exist in the "Visible groups database" "activity"
    And "Visible groups" "icon" should exist in the "Visible groups database" "activity"
    And "Separate groups" "icon" should exist in the "Separate groups assignment" "activity"
    And "Visible groups" "icon" should not exist in the "Separate groups assignment" "activity"

    Examples:
      | editmode | courseformat |
      | off      | topics       |
      | on       | topics       |
      | off      | weeks        |
      | on       | weeks        |

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
    Then "Separate groups" "icon" should not exist in the "No groups forum" "activity"
    And "Visible groups" "icon" should not exist in the "No groups forum" "activity"
    And "Separate groups" "icon" should not exist in the "Visible groups database" "activity"
    And "Visible groups" "icon" should not exist in the "Visible groups database" "activity"
    And "Separate groups" "icon" should not exist in the "Separate groups assignment" "activity"
    And "Visible groups" "icon" should not exist in the "Separate groups assignment" "activity"
    # Giving moodle/course:manageactivities capability would let them see the icons.
    And the following "role capability" exists:
      | role                           | student |
      | moodle/course:manageactivities | allow   |
    And I am on the "C1" "Course" page
    And "Visible groups" "icon" should exist in the "Visible groups database" "activity"
    And "Separate groups" "icon" should exist in the "Separate groups assignment" "activity"

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
    Then "Separate groups" "icon" should not exist in the "No groups page" "activity"
    And "Visible groups" "icon" should not exist in the "No groups page" "activity"
    And "Separate groups" "icon" should not exist in the "Visible groups page" "activity"
    And "Visible groups" "icon" should not exist in the "Visible groups page" "activity"
    And "Separate groups" "icon" should not exist in the "Separate groups page" "activity"
    And "Visible groups" "icon" should not exist in the "Separate groups page" "activity"

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
    Then "Separate groups" "icon" <separate> in the "No groups forum" "activity"
    And "Visible groups" "icon" <visible> in the "No groups forum" "activity"
    And "Separate groups" "icon" <separate> in the "Visible groups database" "activity"
    And "Visible groups" "icon" <visible> in the "Visible groups database" "activity"
    And "Separate groups" "icon" <separate> in the "Separate groups assignment" "activity"
    And "Visible groups" "icon" <visible> in the "Separate groups assignment" "activity"
    And "Separate groups" "icon" should not exist in the "No groups page" "activity"
    And "Visible groups" "icon" should not exist in the "No groups page" "activity"

    Examples:
      | courseformat | groupmode | separate         | visible          |
      | topics       | 0         | should not exist | should not exist |
      | topics       | 1         | should exist     | should not exist |
      | topics       | 2         | should not exist | should exist     |
      | weeks        | 0         | should not exist | should not exist |
      | weeks        | 1         | should exist     | should not exist |
      | weeks        | 2         | should not exist | should exist     |

  Scenario Outline: Group mode icon in the course page in small devices
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
    And I should not see "Separate groups"
    And I should not see "Visible groups"
    And I change viewport size to "480x800"
    And I should see "Separate groups"
    And I should see "Visible groups"

    Examples:
      | courseformat |
      | topics       |
      | weeks        |
