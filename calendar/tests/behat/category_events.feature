@core @core_calendar
Feature: Course Category Events
  In order to inform multiple courses of shared events
  As a manager
  I need to create catgory events

  Background:
    Given the following "users" exist:
      | username    | firstname     | lastname      | email                     |
      | managera    | Manager       | A             | managera@example.com      |
      | managera1   | Manager       | A1            | managera1@example.com     |
      | managera2   | Manager       | A2            | managera2@example.com     |
      | teachera1i  | Teacher       | A1i           | teachera1i@example.com    |
      | managerb    | Manager       | B             | managerb@example.com      |
      | managerb1   | Manager       | B1            | managerb1@example.com     |
      | managerb2   | Manager       | B2            | managerb2@example.com     |
      | teacherb1i  | Teacher       | B1i           | teacherb1i@example.com    |
      | student1    | Student       | 1             | student1@example.com      |
      | student2    | Student       | 2             | student2@example.com      |
    And the following "categories" exist:
      | name            | idnumber      | category  |
      | Year            | year          |           |
      | Faculty A       | faculty-a     | year      |
      | Faculty B       | faculty-b     | year      |
      | Department A1   | department-a1 | faculty-a |
      | Department A2   | department-a2 | faculty-a |
      | Department B1   | department-b1 | faculty-b |
      | Department B2   | department-b2 | faculty-b |
    And the following "courses" exist:
      | fullname    | shortname | idnumber     | format        | category          |
      | Course A1i  | A1i       | A1i          | topics        | department-a1     |
      | Course A2i  | A2i       | A2i          | topics        | department-a2     |
      | Course B1i  | B1i       | B1i          | topics        | department-b1     |
      | Course B2i  | B2i       | B2i          | topics        | department-b2     |
    And the following "role assigns" exist:
      | user        | role      | contextlevel  | reference         |
      | managera    | manager   | Category      | faculty-a         |
      | managera1   | manager   | Category      | department-a1     |
      | managerb    | manager   | Category      | faculty-b         |
      | managerb1   | manager   | Category      | department-b1     |
    And the following "course enrolments" exist:
      | user        | course    | role              |
      | teachera1i  | A1i       | editingteacher    |
      | teacherb1i  | B1i       | editingteacher    |
      | student1    | A1i       | student           |
      | student1    | A2i       | student           |
      | student2    | B1i       | student           |
      | student2    | B2i       | student           |
    And the following "events" exist:
      | name        | eventtype |
      | Site event  | site    |
    And the following "events" exist:
      | name        | eventtype | course |
      | CA1i event  | course    | A1i    |
      | CA2i event  | course    | A2i    |
      | CB1i event  | course    | B1i    |
      | CB2i event  | course    | B2i    |
    And the following "events" exist:
      | name        | eventtype | category          |
      | FA event    | category  | faculty-a         |
      | DA1 event   | category  | department-a1     |
      | DA2 event   | category  | department-a1     |
      | FB event    | category  | faculty-b         |
      | DB1 event   | category  | department-b1     |
      | DB2 event   | category  | department-b1     |

  @javascript
  Scenario: Manager of a Category can see all child and parent events in their category
    Given I log in as "managera"
    And I press "Customise this page"
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    When I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Calendar" "link" in the "Navigation" "block"
    Then I should see "FA event"
    And  I should see "DA1 event"
    And  I should see "DA2 event"
    And  I should not see "FB event"
    And  I should not see "DB1 event"
    And  I should not see "DB2 event"
    And  I log out
    Given I log in as "managerb"
    And I press "Customise this page"
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    When I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Calendar" "link" in the "Navigation" "block"
    Then I should see "FB event"
    And  I should see "DB1 event"
    And  I should see "DB2 event"
    And  I should not see "FA event"
    And  I should not see "DA1 event"
    And  I should not see "DA2 event"

  @javascript
  Scenario: Users enrolled in a course can see all child and parent events in their category
    Given I log in as "student1"
    And I press "Customise this page"
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    When I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Calendar" "link" in the "Navigation" "block"
    Then I should see "FA event"
    And  I should see "DA1 event"
    And  I should see "DA2 event"
    And  I should see "CA1i event"
    And  I should see "CA2i event"
    And  I should not see "FB event"
    And  I should not see "DB1 event"
    And  I should not see "DB2 event"
    And  I should not see "CB1i event"
    And  I should not see "CB2i event"
