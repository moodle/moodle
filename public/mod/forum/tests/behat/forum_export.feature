@mod @mod_forum @javascript
Feature: Export forum
  In order to parse forum data for linguistic analysis
  As a teacher
  I need to export the forum data for select users

  Background: Add a forum and a discussion
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name         | type      | course | idnumber |
      | forum      | Test forum 1 | general   | C1     | 123      |

  Scenario: Teacher can export forum
    Given I am on the "Test forum 1" "forum activity" page logged in as teacher1
    And I navigate to "Export" in current page administration
    And I open the autocomplete suggestions list
    And I should see "Student 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Teacher 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Student 2" in the ".form-autocomplete-suggestions" "css_element"
    # This will fail if an exception is thrown. This is the best we can do without the ability to use the download. Hence, there is no "Then" step.
    And I click on "Export" "button"

  Scenario: Students cannot export forum by default
    Given I am on the "Test forum 1" "forum activity" page logged in as student1
    Then "Export" "link" should not exist in current page administration

  Scenario: User with the capability can export
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | mod/forum:exportforum       | Allow      | student        | Course       | C1        |
    When I am on the "Test forum 1" "forum activity" page logged in as student1
    And I navigate to "Export" in current page administration
    And I open the autocomplete suggestions list
    And I should see "Student 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Teacher 1" in the ".form-autocomplete-suggestions" "css_element"
    And I should not see "Student 2" in the ".form-autocomplete-suggestions" "css_element"
    # This will fail if an exception is thrown. This is the best we can do without the ability to use the download. Hence, there is no "Then" step.
    And I click on "Export" "button"
    And I log out

  Scenario: Group mode is respected when exporting discussions
    Given the following "groups" exist:
      | name | course | idnumber |
      | G1   | C1     | G1       |
      | G2   | C1     | G2       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teachera | Teacher   | A | teacherA@example.com |
      | teacherb | Teacher   | B | teacherB@example.com |
      | teacherc | Teacher   | C | teacherC@example.com |
      | teacherd | Teacher   | D | teacherD@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teachera | C1     | teacher        |
      | teacherb | C1     | teacher        |
      | teacherc | C1     | teacher        |
      | teacherd | C1     | teacher        |
    And the following "group members" exist:
      | user        | group |
      | teachera    | G1  |
      | teachera    | G2  |
      | teacherb    | G1  |
      | teacherc    | G2  |
    And the following "activities" exist:
      | activity | course | idnumber | name                    | intro                      | type    | section | groupmode |
      | forum    | C1     | 00001    | Separate groups forum   | Standard forum description | general | 1       | 1         |
    And the following "mod_forum > discussions" exist:
      | user     | forum                 | name                 | message           | group |
      | teachera | Separate groups forum | Discussion 1 Group 1 | Test post message | G1    |
      | teacherb | Separate groups forum | Discussion 2 Group 1 | Test post message | G1    |
      | teachera | Separate groups forum | Discussion 1 Group 2 | Test post message | G2    |
      | teacherc | Separate groups forum | Discussion 2 Group 2 | Test post message | G2    |
    And I am on the "Separate groups forum" "forum activity" page logged in as teacher1
    And I navigate to "Export" in current page administration
    When I expand the "Users" autocomplete
    # Editing teacher can see all users and discussions.
    Then I should see "Teacher A" in the "Users" "autocomplete"
    And I should see "Teacher B" in the "Users" "autocomplete"
    And I should see "Teacher C" in the "Users" "autocomplete"
    And I should see "Teacher D" in the "Users" "autocomplete"
    And I should see "Teacher 1" in the "Users" "autocomplete"
    And I should see "Student 1" in the "Users" "autocomplete"
    And I press the escape key
    And I expand the "Discussions" autocomplete
    And I should see "Discussion 1 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 1 Group 2" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 2" in the "Discussions" "autocomplete"
    And I click on "Export" "button"

    And I am on the "Separate groups forum" "forum activity" page logged in as teachera
    And I navigate to "Export" in current page administration
    When I expand the "Users" autocomplete
    # Teacher A is is in both groups.
    Then I should see "Teacher A" in the "Users" "autocomplete"
    And I should see "Teacher B" in the "Users" "autocomplete"
    And I should see "Teacher C" in the "Users" "autocomplete"
    And I should not see "Teacher D" in the "Users" "autocomplete"
    And I should not see "Teacher 1" in the "Users" "autocomplete"
    And I should not see "Student 1" in the "Users" "autocomplete"
    And I press the escape key
    And I expand the "Discussions" autocomplete
    And I should see "Discussion 1 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 1 Group 2" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 2" in the "Discussions" "autocomplete"
    And I click on "Export" "button"

    And I am on the "Separate groups forum" "forum activity" page logged in as teacherb
    And I navigate to "Export" in current page administration
    When I expand the "Users" autocomplete
    # Teacher B is in group 1.
    Then I should see "Teacher A" in the "Users" "autocomplete"
    And I should see "Teacher B" in the "Users" "autocomplete"
    And I should not see "Teacher C" in the "Users" "autocomplete"
    And I should not see "Teacher D" in the "Users" "autocomplete"
    And I should not see "Teacher 1" in the "Users" "autocomplete"
    And I should not see "Student 1" in the "Users" "autocomplete"
    And I press the escape key
    And I expand the "Discussions" autocomplete
    And I should see "Discussion 1 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 1" in the "Discussions" "autocomplete"
    And I should not see "Discussion 1 Group 2" in the "Discussions" "autocomplete"
    And I should not see "Discussion 2 Group 2" in the "Discussions" "autocomplete"
    And I click on "Export" "button"

    And I am on the "Separate groups forum" "forum activity" page logged in as teacherc
    And I navigate to "Export" in current page administration
    When I expand the "Users" autocomplete
    # Teacher C is in group 2.
    Then I should see "Teacher A" in the "Users" "autocomplete"
    And I should not see "Teacher B" in the "Users" "autocomplete"
    And I should see "Teacher C" in the "Users" "autocomplete"
    And I should not see "Teacher D" in the "Users" "autocomplete"
    And I should not see "Teacher 1" in the "Users" "autocomplete"
    And I should not see "Student 1" in the "Users" "autocomplete"
    And I press the escape key
    And I expand the "Discussions" autocomplete
    And I should not see "Discussion 1 Group 1" in the "Discussions" "autocomplete"
    And I should not see "Discussion 2 Group 1" in the "Discussions" "autocomplete"
    And I should see "Discussion 1 Group 2" in the "Discussions" "autocomplete"
    And I should see "Discussion 2 Group 2" in the "Discussions" "autocomplete"
    And I click on "Export" "button"

    And I am on the "Separate groups forum" "forum activity" page logged in as teacherd
    And I navigate to "Export" in current page administration
    When I expand the "Users" autocomplete
    # Teacher D is in no group.
    Then I should not see "Teacher A" in the "Users" "autocomplete"
    And I should not see "Teacher B" in the "Users" "autocomplete"
    And I should not see "Teacher C" in the "Users" "autocomplete"
    And I should not see "Teacher D" in the "Users" "autocomplete"
    And I should not see "Teacher 1" in the "Users" "autocomplete"
    And I should not see "Student 1" in the "Users" "autocomplete"
    And I press the escape key
    And I expand the "Discussions" autocomplete
    And I should not see "Discussion 1 Group 1" in the "Discussions" "autocomplete"
    And I should not see "Discussion 2 Group 1" in the "Discussions" "autocomplete"
    And I should not see "Discussion 1 Group 2" in the "Discussions" "autocomplete"
    And I should not see "Discussion 2 Group 2" in the "Discussions" "autocomplete"
    And I click on "Export" "button"
