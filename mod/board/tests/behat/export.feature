@mod @mod_board @javascript
Feature: Export of mod_board data

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
      | student4 | Fourth    | Student  | student4@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group A | C1     | GA       |
      | Group B | C1     | GB       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "group members" exist:
      | user     | group |
      | student1 | GA    |
      | student2 | GB    |
      | student3 | GA    |
      | student3 | GB    |

  Scenario: Teachers may export all mod_board data when single user mode disabled and no groupmode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     |
      | Sample board | 1           | Heading T1  | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1  | Content S1  | student1 |
      | Sample board | 2           | Heading S2  | Content S2  | student2 |
      | Sample board | 3           | Heading S4  | Content S4  | student4 |
    And the following "mod_board > comments" exist:
      | note        | content      | user     |
      | Heading S1  | Comment T1x1 | teacher1 |
      | Heading S2  | Comment T1x2 | teacher1 |
      | Heading S4  | Comment T1x4 | teacher1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Export" "link" in the ".secondary-navigation" "css_element"
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading T1   | Heading S2    | Heading S4   |
      | Heading S1   |               |              |

    When I click on "Export Submissions" "link"
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Second    | Student  | student2@example.com | Heading S2   | Content S2 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
      | First     | Teacher  | teacher1@example.com | Heading T1   | Content T1 |             |           | No      |

    When I click on "Export Comments" "link"
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S4   | First     | Teacher  | Comment T1x4 | No      |
      | Heading S2   | First     | Teacher  | Comment T1x2 | No      |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |

  Scenario: Teachers may export all mod_board data when single user mode disabled and visible group mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 2                      |
      | singleusermode | 0                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | group |
      | Sample board | 1           | Heading T1  | Content T1  | teacher1 |       |
      | Sample board | 1           | Heading S1  | Content S1  | student1 | GA    |
      | Sample board | 2           | Heading S2  | Content S2  | student2 | GB    |
      | Sample board | 3           | Heading S4  | Content S4  | student4 | GA    |
    And the following "mod_board > comments" exist:
      | note        | content      | user     |
      | Heading S1  | Comment T1x1 | teacher1 |
      | Heading S2  | Comment T1x2 | teacher1 |
      | Heading S4  | Comment T1x4 | teacher1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Export" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Visible groups | All participants |
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading T1   | Heading S2    | Heading S4   |
      | Heading S1   |               |              |

    When I click on "Export Submissions" "link"
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Second    | Student  | student2@example.com | Heading S2   | Content S2 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
      | First     | Teacher  | teacher1@example.com | Heading T1   | Content T1 |             |           | No      |

    When I click on "Export Comments" "link"
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S4   | First     | Teacher  | Comment T1x4 | No      |
      | Heading S2   | First     | Teacher  | Comment T1x2 | No      |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |

    When I click on "Export Board" "link"
    And I select "Group A" from the "Visible groups" singleselect
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading S1   | -             | Heading S4   |
    And I should not see "Heading S2"
    And I should not see "Heading T1"

    When I click on "Export Submissions" "link"
    And the following fields match these values:
      | Visible groups | Group A |
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
    And I should not see "Heading S2"
    And I should not see "Heading T1"

  Scenario: Teachers may export all mod_board data in private single user mode and no group node
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 1                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | owner    |
      | Sample board | 1           | Heading T1  | Content T1  | teacher1 | teacher1 |
      | Sample board | 1           | Heading S1  | Content S1  | student1 | student1 |
      | Sample board | 2           | Heading S2  | Content S2  | teacher1 | student2 |
      | Sample board | 3           | Heading S4  | Content S4  | student4 | student4 |
    And the following "mod_board > comments" exist:
      | note        | content      | user     |
      | Heading S1  | Comment T1x1 | teacher1 |
      | Heading S2  | Comment T1x2 | teacher1 |
      | Heading S4  | Comment T1x4 | teacher1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Export" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Select user | All |
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading T1   | Heading S2    | Heading S4   |
      | Heading S1   |               |              |

    When I click on "Export Submissions" "link"
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Second    | Student  | student2@example.com | Heading S2   | Content S2 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
      | First     | Teacher  | teacher1@example.com | Heading T1   | Content T1 |             |           | No      |

    When I click on "Export Comments" "link"
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S4   | First     | Teacher  | Comment T1x4 | No      |
      | Heading S2   | First     | Teacher  | Comment T1x2 | No      |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |

    When I click on "Export Board" "link"
    And I select "First Student" from the "Select user" singleselect
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading S1   | -             | -   |
    And I should not see "Heading S2"
    And I should not see "Heading S4"
    And I should not see "Heading T1"

    When I click on "Export Submissions" "link"
    And the following fields match these values:
      | Select user | First Student |
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
    And I should not see "Heading S2"
    And I should not see "Heading S4"
    And I should not see "Heading T1"

    When I click on "Export Comments" "link"
    And the following fields match these values:
      | Select user | First Student |
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |
    And I should not see "Comment T1x4"
    And I should not see "Comment T1x2"

  Scenario: Teachers may export all mod_board data in public single user mode and separate groups node
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 1                      |
      | singleusermode | 2                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading     | content     | user     | owner    |
      | Sample board | 1           | Heading T1  | Content T1  | teacher1 | teacher1 |
      | Sample board | 1           | Heading S1  | Content S1  | student1 | student1 |
      | Sample board | 2           | Heading S2  | Content S2  | teacher1 | student2 |
      | Sample board | 3           | Heading S4  | Content S4  | student4 | student4 |
    And the following "mod_board > comments" exist:
      | note        | content      | user     |
      | Heading S1  | Comment T1x1 | teacher1 |
      | Heading S2  | Comment T1x2 | teacher1 |
      | Heading S4  | Comment T1x4 | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student4 | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | student4 | GA    |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Export" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Separate groups | All participants |
      | Select user     | All              |
    And the "Select user" select box should contain "First Student"
    And the "Select user" select box should contain "Second Student"
    And the "Select user" select box should contain "Fourth Student"
    And the "Select user" select box should contain "First Teacher"
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading T1   | Heading S2    | Heading S4   |
      | Heading S1   |               |              |

    When I click on "Export Submissions" "link"
    And the following fields match these values:
      | Separate groups | All participants |
      | Select user     | All              |
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Second    | Student  | student2@example.com | Heading S2   | Content S2 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
      | First     | Teacher  | teacher1@example.com | Heading T1   | Content T1 |             |           | No      |

    When I click on "Export Comments" "link"
    And the following fields match these values:
      | Separate groups | All participants |
      | Select user     | All              |
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S4   | First     | Teacher  | Comment T1x4 | No      |
      | Heading S2   | First     | Teacher  | Comment T1x2 | No      |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |

    When I click on "Export Board" "link"
    And I select "Group A" from the "Separate groups" singleselect
    And the following fields match these values:
      | Select user     | All              |
    And the "Select user" select box should contain "First Student"
    And the "Select user" select box should not contain "Second Student"
    And the "Select user" select box should contain "Fourth Student"
    And the "Select user" select box should not contain "First Teacher"
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading S1   | -             | Heading S4   |
    And I should not see "Heading S2"
    And I should not see "Heading T1"

    When I click on "Export Submissions" "link"
    And the following fields match these values:
      | Separate groups | Group A          |
      | Select user     | All              |
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
      | Fourth    | Student  | student4@example.com | Heading S4   | Content S4 |             |           | No      |
    And I should not see "Heading S2"
    And I should not see "Heading T1"

    When I click on "Export Comments" "link"
    And the following fields match these values:
      | Separate groups | Group A          |
      | Select user     | All              |
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |
      | Heading S4   | First     | Teacher  | Comment T1x4 | No      |
    And I should not see "Comment T1x2"

    When I click on "Export Board" "link"
    And I select "First Student" from the "Select user" singleselect
    And the "Select user" select box should contain "First Student"
    And the "Select user" select box should not contain "Second Student"
    And the "Select user" select box should contain "Fourth Student"
    And the "Select user" select box should not contain "First Teacher"
    Then the following should exist in the "flexible" table:
      | First Column | Second Column | Third Column |
      | Heading S1   | -             | -            |
    And I should not see "Heading S2"
    And I should not see "Heading S4"
    And I should not see "Heading T1"

    When I click on "Export Submissions" "link"
    And the following fields match these values:
      | Separate groups | Group A          |
      | Select user     | First Student    |
    Then the following should exist in the "flexible" table:
      | Firstname | Lastname | Email                | Post Heading | Text       | Media Title | Media URL | Deleted |
      | First     | Student  | student1@example.com | Heading S1   | Content S1 |             |           | No      |
    And I should not see "Heading S2"
    And I should not see "Heading S4"
    And I should not see "Heading T1"

    When I click on "Export Comments" "link"
    And the following fields match these values:
      | Separate groups | Group A          |
      | Select user     | First Student    |
    Then the following should exist in the "flexible" table:
      | Post Heading | Firstname | Lastname | Text         | Deleted |
      | Heading S1   | First     | Teacher  | Comment T1x1 | No      |
    And I should not see "Comment T1x2"
    And I should not see "Comment T1x4"
