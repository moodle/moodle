@core @core_badges @javascript
Feature: Test tertiary navigation as various users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher  | Teacher | 1 | teacher@example.com |
      | nonediting  | Nonediting | 1 | nonediting@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher | C1     | editingteacher |
      | student1 | C1     | student        |
      | nonediting | C1     | teacher        |
    # Create a course badge in our test course.
    And the following "core_badges > Badge" exists:
      | name           | Testing course badge             |
      | status         | inactive                         |
      | type           | 2                                |
      | course         | C1                               |
      | version        | 1.1                              |
      | language       | ca                               |
      | description    | Testing course badge description |
      | image          | badges/tests/behat/badge.png     |
      | imageauthorurl | http://author.example.com        |
      | imagecaption   | Test caption image               |
    # Create a site badge.
    And the following "core_badges > Badge" exists:
      | name           | Testing site badge               |
      | status         | inactive                         |
      | version        | 1.1                              |
      | language       | ca                               |
      | description    | Testing site badge description   |
      | image          | badges/tests/behat/badge.png     |
      | imageauthorurl | http://author.example.com        |
      | imagecaption   | Test caption image               |
    And the following "core_badges > Criterias" exist:
      | badge                | role           |
      | Testing course badge | editingteacher |
      | Testing site badge   | editingteacher |

  Scenario Outline: Check navigation as different users in a course context
    Given I log in as "<user>"
    And I am on "Course 1" course homepage
    When I navigate to "Badges" in current page administration
    Then "Manage badges" "button" should exist
    And "Add a new badge" "button" <createbutton>
    And I should see "<activetab>" is active in secondary navigation
    And I click on "Manage badges" "button"
    And "Manage badges" "button" should not exist
    And "Back" "button" should exist
    And "Add a new badge" "button" <createbutton>
    And I should see "<activetab>" is active in secondary navigation
    And I click on "Back" "button"
    And "Back" "button" should not exist
    And "Manage badges" "button" should exist
    And "Add a new badge" "button" <createbutton>
    And I should see "<activetab>" is active in secondary navigation
    And I click on "Manage badges" "button"
    And I click on "Testing course badge" "link"
    And "Manage badges" "button" should not exist
    And "Add a new badge" "button" should not exist
    And "Back" "button" should exist
    And I should see "<activetab>" is active in secondary navigation
    And I click on "Back" "button"
    And "Back" "button" should exist
    And "Manage badges" "button" should not exist
    And "Add a new badge" "button" <createbutton>
    And I should see "<activetab>" is active in secondary navigation

    Examples:
      | user       | createbutton     | activetab |
      | admin      | should exist     | More      |
      | teacher    | should exist     | More      |
      | nonediting | should not exist | Badges    |

  Scenario: Check navigation as a student
    Given I am on the "C1" "Course" page logged in as "student1"
    And "Badges" "link" should not exist in current page administration
    And I log out
    # Enable one badge.
    When I am on the "C1" "Course" page logged in as "admin"
    And I navigate to "Badges" in current page administration
    And I click on "Manage badges" "button"
    And I click on "Enable access" "link" in the "Testing course badge" "table_row"
    And I press "Continue"
    And I log out
    # Now student should see the Badges link.
    And I am on the "C1" "Course" page logged in as "student1"
    Then "Badges" "link" should exist in current page administration
    And I navigate to "Badges" in current page administration
    And "Back" "button" should not exist
    And "Manage badges" "button" should not exist
    And "Add a new badge" "button" should not exist
    And I should see "Badges" is active in secondary navigation

  Scenario: Check navigation as an admin in a site context
    Given I log in as "admin"
    When I navigate to "Badges > Manage badges" in site administration
    Then "Manage badges" "button" should not exist
    And "Add a new badge" "button" should exist
    And I should see "General" is active in secondary navigation
    And I click on "Testing site badge" "link"
    And "Manage badges" "button" should not exist
    And "Add a new badge" "button" should not exist
    And "Back" "button" should exist
    And I should see "General" is active in secondary navigation
    And I click on "Back" "button"
    And "Back" "button" should not exist
    And "Manage badges" "button" should not exist
    And "Add a new badge" "button" should exist

  Scenario Outline: Check secondary navigation highlights after tertiary nav jumps in site admin
    Given I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration
    And I click on "Testing site badge" "link"
    When I select "<option>" from the "jump" singleselect
    Then I should see "General" is active in secondary navigation

    Examples:
      | option             |
      | Overview           |
      | Edit details       |
      | Criteria           |
      | Message            |
      | Recipients (0)     |
      | Endorsement        |
      | Related badges (0) |
      | Alignments (0)     |

  Scenario Outline: Check secondary navigation highlights after tertiary nav jumps in course as admin
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Badges" in current page administration
    And I click on "Manage badges" "button"
    And I click on "Testing course badge" "link"
    When I select "<option>" from the "jump" singleselect
    Then I should see "More" is active in secondary navigation

    Examples:
      | option             |
      | Overview           |
      | Edit details       |
      | Criteria           |
      | Message            |
      | Recipients (0)     |
      | Endorsement        |
      | Related badges (0) |
      | Alignments (0)     |
