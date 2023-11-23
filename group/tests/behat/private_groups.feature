@core @core_group
Feature: Private groups
  As a teacher
  In order to organise students into groups while protecting their privacy
  I want to define groups that are not visible to all students

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 3           |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | Teacher  |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
      | student4 | Student   | 4        |
      | student5 | Student   | 5        |
      | student6 | Student   | 6        |
      | student7 | Student   | 7        |
      | student8 | Student   | 8        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | student6 | C1     | student        |
      | student7 | C1     | student        |
      | student8 | C1     | student        |
    And the following "groups" exist:
      | name                                      | course | idnumber | visibility | participation |
      | Visible/Participation                     | C1     | VP       | 0          | 1             |
      | Only visible to members/Participation     | C1     | MP       | 1          | 1             |
      | Only see own membership                   | C1     | O        | 2          | 0             |
      | Not visible                               | C1     | N        | 3          | 0             |
      | Visible/Non-Participation                 | C1     | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | C1     | MN       | 1          | 0             |
    And the following "group members" exist:
      | user     | group |
      | student1 | VP    |
      | student1 | VN    |
      | student2 | MP    |
      | student2 | MN    |
      | student3 | O     |
      | student4 | N     |
      | student5 | VP    |
      | student5 | VN    |
      | student6 | MP    |
      | student6 | MN    |
      | student7 | O     |
      | student8 | N     |

  Scenario: Participants in "Visible" groups see their membership and other members:
    Given I am on the "C1" "enrolled users" page logged in as "student1"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                         |
      | Student 1            | Visible/Non-Participation, Visible/Participation               |
      | Student 2            | No groups                                                      |
      | Student 3            | No groups                                                      |
      | Student 4            | No groups                                                      |
      | Student 5            | Visible/Non-Participation, Visible/Participation               |
      | Student 6            | No groups                                                      |
      | Student 7            | No groups                                                      |
      | Student 8            | No groups                                                      |

  Scenario: Participants in "Only visible to members" groups see their membership and other members, plus "Visible"
    Given I am on the "C1" "enrolled users" page logged in as "student2"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                 |
      | Student 1            | Visible/Non-Participation, Visible/Participation                       |
      | Student 2            | Only visible to members/Non-Participation, Only visible to members/Participation |
      | Student 3            | No groups                                                              |
      | Student 4            | No groups                                                              |
      | Student 5            | Visible/Non-Participation, Visible/Participation                       |
      | Student 6            | Only visible to members/Non-Participation, Only visible to members/Participation |
      | Student 7            | No groups                                                              |
      | Student 8            | No groups                                                              |

  Scenario: Participants in "Only see own membership" groups see their membership but not other members, plus "Visible"
    Given I am on the "C1" "enrolled users" page logged in as "student3"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                 |
      | Student 1            | Visible/Non-Participation, Visible/Participation                       |
      | Student 2            | No groups                                                              |
      | Student 3            | Only see own membership                                                     |
      | Student 4            | No groups                                                              |
      | Student 5            | Visible/Non-Participation, Visible/Participation                       |
      | Student 6            | No groups                                                              |
      | Student 7            | No groups                                                              |
      | Student 8            | No groups                                                              |

  Scenario: Participants in "Not visible" groups do not see that group, do see "Visible"
    Given I am on the "C1" "enrolled users" page logged in as "student4"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                 |
      | Student 1            | Visible/Non-Participation, Visible/Participation                       |
      | Student 2            | No groups                                                              |
      | Student 3            | No groups                                                              |
      | Student 4            | No groups                                                              |
      | Student 5            | Visible/Non-Participation, Visible/Participation                       |
      | Student 6            | No groups                                                              |
      | Student 7            | No groups                                                              |
      | Student 8            | No groups                                                              |

  Scenario: View participants list as a teacher:
    Given I am on the "C1" "enrolled users" page logged in as "teacher1"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                 |
      | Student 1            | Visible/Non-Participation, Visible/Participation                       |
      | Student 2            | Only visible to members/Non-Participation, Only visible to members/Participation |
      | Student 3            | Only see own membership                                                     |
      | Student 4            | Not visible                                                            |
      | Student 5            | Visible/Non-Participation, Visible/Participation                       |
      | Student 6            | Only visible to members/Non-Participation, Only visible to members/Participation |
      | Student 7            | Only see own membership                                                     |
      | Student 8            | Not visible                                                            |

  @javascript
  Scenario: Filtering by "Only see own membership" groups should not show other members.
    Given I am on the "C1" "enrolled users" page logged in as "student3"
    When I set the field "type" to "Groups"
    And I set the field "Type or select..." to "Only see own membership"
    And I click on "Apply filters" "button"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                  |
      | Student 3            | Only see own membership |
    And the following should not exist in the "participants" table:
      | First name / Surname | Groups                  |
      | Student 7            | No groups               |

  @javascript
  Scenario: Filtering by "No group" should show all users whose memberships I cannot see
    Given I am on the "C1" "enrolled users" page logged in as "student3"
    When I set the field "type" to "Groups"
    And I set the field "Type or select..." to "No group"
    And I click on "Apply filters" "button"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups     |
      | Student 2            | No groups  |
      | Student 4            | No groups  |
      | Student 6            | No groups  |
      | Student 7            | No groups  |
      | Student 8            | No groups  |

  @javascript
  Scenario: Filtering by not a member of "Only see own membership" groups I am a member of should show everyone except me
    Given I am on the "C1" "enrolled users" page logged in as "student3"
    When I set the field "Match" in the "Filter 1" "fieldset" to "None"
    And I set the field "type" to "Groups"
    And I set the field "Type or select..." to "Only see own membership"
    And I click on "Apply filters" "button"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                   |
      | Student 1            | Visible/Non-Participation, Visible/Participation                         |
      | Student 2            | No groups                                                                |
      | Student 4            | No groups                                                                |
      | Student 5            | Visible/Non-Participation, Visible/Participation                         |
      | Student 6            | No groups                                                                |
      | Student 7            | No groups                                                                |
      | Student 8            | No groups                                                                |

  @javascript
  Scenario: Filtering by not a member of "No group" should only show users whose memberships I can see
    Given I am on the "C1" "enrolled users" page logged in as "student3"
    When I set the field "Match" in the "Filter 1" "fieldset" to "None"
    And I set the field "type" to "Groups"
    And I set the field "Type or select..." to "No group"
    And I click on "Apply filters" "button"
    Then the following should exist in the "participants" table:
      | First name / Surname | Groups                                                                   |
      | Student 1            | Visible/Non-Participation, Visible/Participation                         |
      | Student 3            | Only see own membership                                                  |
      | Student 5            | Visible/Non-Participation, Visible/Participation                         |
