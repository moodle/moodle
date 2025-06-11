@core @core_admin @core_course @javascript
Feature: Reset course indentation
  In order to reset indentation in course modules
  As a admin
  I want change indent value for all the modules of a course format courses in one go

  Background:
    Given the following "courses" exist:
      | fullname        | shortname | format |
      | Sections Course 1 | T1      | topics |
      | Sections Course 2 | T2      | topics |
      | Weekly Course 1 | W1        | weeks  |
      | Weekly Course 2 | W2        | weeks  |
    And the following "activities" exist:
      | activity | name                   | intro                         | course | idnumber |
      | forum    | Sections forum name    | Sections forum description    | T1     | forum1   |
      | data     | Sections database name | Sections database description | T1     | data1    |
      | wiki     | Sections wiki name     | Sections wiki description     | T2     | wiki1    |
      | forum    | Weekly forum name      | Weekly forum description      | W1     | forum2   |
      | data     | Weekly database name   | Weekly database description   | W1     | data2    |
      | wiki     | Weekly wiki name       | Weekly wiki description       | W2     | wiki2    |
    And I log in as "admin"
    And I am on "Sections Course 1" course homepage with editing mode on
    And I open "Sections forum name" actions menu
    And I click on "Move right" "link" in the "Sections forum name" activity
    And I open "Sections forum name" actions menu
    And "Move right" "link" in the "Sections forum name" "activity" should not be visible
    And "Move left" "link" in the "Sections forum name" "activity" should be visible
    And I press the escape key
    And I open "Sections database name" actions menu
    And "Move right" "link" in the "Sections database name" "activity" should be visible
    And "Move left" "link" in the "Sections database name" "activity" should not be visible
    And I am on "Sections Course 2" course homepage with editing mode on
    And I open "Sections wiki name" actions menu
    And I click on "Move right" "link" in the "Sections wiki name" activity
    And I open "Sections wiki name" actions menu
    And "Move right" "link" in the "Sections wiki name" "activity" should not be visible
    And "Move left" "link" in the "Sections wiki name" "activity" should be visible
    And I am on "Weekly Course 1" course homepage with editing mode on
    And I open "Weekly forum name" actions menu
    And I click on "Move right" "link" in the "Weekly forum name" activity
    And I open "Weekly forum name" actions menu
    And "Move right" "link" in the "Weekly forum name" "activity" should not be visible
    And "Move left" "link" in the "Weekly forum name" "activity" should be visible
    And I press the escape key
    And I open "Weekly database name" actions menu
    And "Move right" "link" in the "Weekly database name" "activity" should be visible
    And "Move left" "link" in the "Weekly database name" "activity" should not be visible
    And I am on "Weekly Course 2" course homepage with editing mode on
    And I open "Weekly wiki name" actions menu
    And I click on "Move right" "link" in the "Weekly wiki name" activity
    And I open "Weekly wiki name" actions menu
    And "Move right" "link" in the "Weekly wiki name" "activity" should not be visible
    And "Move left" "link" in the "Weekly wiki name" "activity" should be visible

  Scenario Outline: Apply course indentation reset
    Given I navigate to "Plugins > Course formats > <format>" in site administration
    And I wait "5" seconds
    And "Reset indentation sitewide" "link" should exist
    When I click on "Reset indentation sitewide" "link"
    And I should see "Reset indentation sitewide"
    And "Reset indentation sitewide" "button" should exist
    And I click on "Reset indentation sitewide" "button"
    Then I should see "Indentation reset."
    And I am on "<prefix> Course 1" course homepage with editing mode on
    And I open "<prefix> forum name" actions menu
    And "Move right" "link" in the "<prefix> forum name" "activity" should be visible
    And "Move left" "link" in the "<prefix> forum name" "activity" should not be visible
    And I press the escape key
    And I open "<prefix> database name" actions menu
    And "Move right" "link" in the "<prefix> database name" "activity" should be visible
    And "Move left" "link" in the "<prefix> database name" "activity" should not be visible
    And I am on "<prefix> Course 2" course homepage with editing mode on
    And I open "<prefix> wiki name" actions menu
    And "Move right" "link" in the "<prefix> wiki name" "activity" should be visible
    And "Move left" "link" in the "<prefix> wiki name" "activity" should not be visible
    # Check other course formats had not been reset
    And I am on "<other> Course 1" course homepage with editing mode on
    And I open "<other> forum name" actions menu
    And "Move right" "link" in the "<other> forum name" "activity" should not be visible
    And "Move left" "link" in the "<other> forum name" "activity" should be visible

    Examples:
      | format          | prefix    | other    |
      | Custom sections | Sections  | Weekly   |
      | Weekly sections | Weekly    | Sections |

  Scenario Outline: Cancel course indentation reset
    Given I navigate to "Plugins > Course formats > <format>" in site administration
    And "Reset indentation sitewide" "link" should exist
    When I click on "Reset indentation sitewide" "link"
    And I should see "Reset indentation sitewide"
    And "Reset indentation sitewide" "button" should exist
    And "Cancel" "button" should exist
    And I click on "Cancel" "button"
    Then I should not see "Indentation reset."
    And I am on "<prefix> Course 1" course homepage with editing mode on
    And I open "<prefix> forum name" actions menu
    And "Move right" "link" in the "<prefix> forum name" "activity" should not be visible
    And "Move left" "link" in the "<prefix> forum name" "activity" should be visible
    And I press the escape key
    And I open "<prefix> database name" actions menu
    And "Move right" "link" in the "<prefix> database name" "activity" should be visible
    And "Move left" "link" in the "<prefix> database name" "activity" should not be visible
    And I am on "<prefix> Course 2" course homepage with editing mode on
    And I open "<prefix> wiki name" actions menu
    And "Move right" "link" in the "<prefix> wiki name" "activity" should not be visible
    And "Move left" "link" in the "<prefix> wiki name" "activity" should be visible

    Examples:
      | format          | prefix    |
      | Custom sections | Sections  |
      | Weekly sections | Weekly    |
