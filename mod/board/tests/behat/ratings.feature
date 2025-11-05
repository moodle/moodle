@mod @mod_board @javascript
Feature: Rating of mod_board posts

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
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

  Scenario: With RATINGBYALLE everybody may rate mod_board posts
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
      | addrating      | 3                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"
    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "2" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    And I am on homepage

  Scenario: With RATINGBYTEACHERS only teachers may rate mod_board posts
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
      | addrating      | 2                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"
    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    When I am on the "Sample board" "board activity" page logged in as "student1"
    # Note: this is wrong, there should be different label, for now just make sure click does nothing
    And I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I should not see "Confirm"
    And I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I should not see "Confirm"

    And I am on homepage

  Scenario: With RATINGBYSTUDENTS only teachers may rate mod_board posts
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
      | addrating      | 1                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |
      | Sample board | 1           | Heading S1 | Content S1  | student1 |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    # Note: this is wrong, there should be different label, for now just make sure click does nothing
    And I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I should not see "Confirm"
    And I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I should not see "Confirm"

    When I am on the "Sample board" "board activity" page logged in as "student1"
    And I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "0" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    And I am on homepage

  Scenario: Students may rate in own group only and teachers all mod_board posts in visible groups mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 2                      |
      | singleusermode | 0                      |
      | addrating      | 3                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     | group |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |       |
      | Sample board | 1           | Heading S1 | Content S1  | student1 | GA    |
      | Sample board | 1           | Heading S2 | Content S2  | student2 | GB    |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading T1 from column First Column" "mod_board > button"

    And I select "Group A" from the "Visible groups" singleselect

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    When I am on the "Sample board" "board activity" page logged in as "student2"

    And I select "Group A" from the "Visible groups" singleselect

    # Note: this is wrong, there should be different label, for now just make sure click does nothing
    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    Then I should not see "Confirm"

    And I select "Group B" from the "Visible groups" singleselect

    When I click on "Rate post Heading S2 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S2 from column First Column" "mod_board > button"

    And I select "All participants" from the "Visible groups" singleselect

    # Note: this is wrong, there should be different label, for now just make sure click does nothing
    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    Then I should not see "Confirm"

    And I am on homepage

  Scenario: Students may rate in own group only and teachers all group mod_board posts in separate groups mode
    Given the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 1                      |
      | singleusermode | 0                      |
      | addrating      | 3                      |
    And the following "mod_board > notes" exist:
      | board        | column      | heading    | content     | user     | group |
      | Sample board | 1           | Heading T1 | Content T1  | teacher1 |       |
      | Sample board | 1           | Heading S1 | Content S1  | student1 | GA    |
      | Sample board | 1           | Heading S2 | Content S2  | student2 | GB    |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    # Note: this is wrong, there should be different label, for now just make sure click does nothing
    When I click on "Rate post Heading T1 from column First Column" "mod_board > button"
    Then I should not see "Confirm"

    And I select "Group A" from the "Separate groups" singleselect

    When I click on "Rate post Heading S1 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S1 from column First Column" "mod_board > button"

    When I am on the "Sample board" "board activity" page logged in as "student2"

    When I click on "Rate post Heading S2 from column First Column" "mod_board > button"
    And I click on "Ok" "button" in the "Confirm" "dialogue"
    Then I should see "1" in the "Rate post Heading S2 from column First Column" "mod_board > button"

    And I am on homepage
