@core @core_course @core_courseformat @format_topics @format_weeks
Feature: Enable course linear navigation setting
  In order to navigate through the course activities in a linear way
  As a course creator
  I need some course formats to support course linear navigation

  @format_singleactivity
  Scenario Outline: The option to enable course linear navigation is shown or hidden based on course format
    Given the following "courses" exist:
      | fullname | shortname | format   |
      | Course1  | c1        | <format> |
    When I am on the "Course1" "Course" page logged in as "admin"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And <shouldsee> "Enable linear navigation"

    Examples:
      | format         | shouldsee        |
      | topics         | I should see     |
      | weeks          | I should see     |
      | singleactivity | I should not see |

  @javascript
  Scenario Outline: The default linear navigation value for new course is applied based on default global settings
    Given the following config values are set as admin:
      | enablelinearnav | <defaulttopics> | format_topics |
    And the following config values are set as admin:
      | enablelinearnav | <defaultweeks>  | format_weeks  |
    When I am on the "Homepage" page logged in as "admin"
    And I click on "Create course" "button" in the "Course overview" "block"
    And I expand all fieldsets
    Then the field "Enable linear navigation" matches value "<expectedtopics>"
    # For new courses, the linear navigation setting should be pre-filled based on the course format default.
    But I set the field "Format" to "Weekly sections"
    And the field "Enable linear navigation" matches value "<expectedweeks>"

    Examples:
      | defaulttopics | defaultweeks | expectedtopics | expectedweeks |
      | 0             | 0            | No             | No            |
      | 1             | 0            | Yes            | No            |
      | 0             | 1            | No             | Yes           |
      | 1             | 1            | Yes            | Yes           |

  Scenario Outline: The default linear navigation value for existing courses is not changed despite global defaults
    Given the following config values are set as admin:
      | enablelinearnav | <defaulttopics> | format_topics |
    And the following config values are set as admin:
      | enablelinearnav | <defaultweeks>  | format_weeks  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course1  | c1        | topics |
    When I am on the "Course1" "course" page logged in as admin
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "Enable linear navigation" matches value "<expectedtopics>"
    # For existing courses, the linear navigation setting should not be changed based on global defaults.
    But I set the field "Format" to "Weekly sections"
    And the field "Enable linear navigation" matches value "<expectedweeks>"

    Examples:
      | defaulttopics | defaultweeks | expectedtopics | expectedweeks |
      | 0             | 0            | No             | No            |
      | 1             | 0            | Yes            | Yes           |
      | 0             | 1            | No             | No            |
      | 1             | 1            | Yes            | Yes           |

  Scenario Outline: The linear navigation value can be changed when editing a course
    Given the following "courses" exist:
      | fullname | shortname | format   |
      | Course1  | c1        | <format> |
    And I am on the "Course1" "course" page logged in as admin
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Enable linear navigation" matches value "Yes"
    When I set the field "Enable linear navigation" to "No"
    And I click on "Save and display" "button"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    Then the field "Enable linear navigation" matches value "No"

    Examples:
      | format |
      | topics |
      | weeks  |
