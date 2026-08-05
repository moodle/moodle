@javascript
Feature: Site participants link in Site Administration
  In order to manage site-wide participants
  As an administrator
  I need to access the site participants page from Site Administration

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | manager1 | Manager   | One      |
    And the following "system role assigns" exist:
      | user     | role    |
      | manager1 | manager |

  Scenario: Admin can see and navigate to the Site participants link
    Given I log in as "admin"
    When I navigate to "Courses > Site participants" in site administration
    Then I should see "Participants"
    And I should see "Admin User"

  Scenario: Manager with viewparticipants capability can access Site participants
    Given I log in as "manager1"
    When I navigate to "Courses > Site participants" in site administration
    Then I should see "Participants"
    And I should see "Manager One"

  Scenario: Regular user cannot access Site administration
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | One      |
    And I log in as "student1"
    Then "Site administration" "link" should not exist

  Scenario: User holding only the viewparticipants capability, without category:manage, can still reach the link
    Given the following "roles" exist:
      | name             | shortname     | archetype |
      | Participant viewer | participantviewer | |
    And the following "users" exist:
      | username | firstname | lastname |
      | limited1 | Limited   | One      |
    And the following "permission overrides" exist:
      | capability                    | permission | role              | contextlevel | reference |
      | moodle/site:configview        | Allow      | participantviewer | System       | System    |
      | moodle/site:viewparticipants  | Allow      | participantviewer | System       | System    |
    And the following "system role assigns" exist:
      | user     | role              |
      | limited1 | participantviewer |
    And I log in as "limited1"
    When I navigate to "Courses > Site participants" in site administration
    Then I should see "Participants"
    And I should see "Limited One"
