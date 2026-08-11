@core @core_admin @core_contentbank
Feature: Site content bank link in Site Administration
  In order to manage the site-level content bank
  As an administrator or manager
  I need to access the site content bank page from Site Administration

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | manager1 | Manager   | One      |
      | limited1 | Limited   | One      |
    And the following "system role assigns" exist:
      | user     | role    |
      | manager1 | manager |

  Scenario: Admin can see and navigate to the Site content bank link
    Given I log in as "admin"
    When I navigate to "Courses > Site content bank" in site administration
    Then I should see "Content bank"

  Scenario: Manager with contentbank:access capability can access Site content bank
    Given I log in as "manager1"
    When I navigate to "Courses > Site content bank" in site administration
    Then I should see "Content bank"

  Scenario: User holding only the contentbank:access capability, without category:manage, can still reach the link
    Given the following "roles" exist:
      | name                | shortname          | archetype |
      | Content bank viewer | contentbankviewer  |           |
    And the following "users" exist:
      | username | firstname | lastname |
      | viewer1  | Viewer    | One      |
    And the following "permission overrides" exist:
      | capability                | permission | role              | contextlevel | reference |
      | moodle/site:configview    | Allow      | contentbankviewer | System       | System    |
      | moodle/contentbank:access | Allow      | contentbankviewer | System       | System    |
    And the following "system role assigns" exist:
      | user    | role              |
      | viewer1 | contentbankviewer |
    And I log in as "viewer1"
    When I navigate to "Courses > Site content bank" in site administration
    Then I should see "Content bank"

  Scenario: User without contentbank:access at system context cannot see the Site content bank link
    Given the following "roles" exist:
      | name                  | shortname           | archetype |
      | Course creator viewer | coursecreatorviewer |           |
    And the following "permission overrides" exist:
      | capability             | permission | role                | contextlevel | reference |
      | moodle/site:configview | Allow      | coursecreatorviewer | System       | System    |
      | moodle/course:create   | Allow      | coursecreatorviewer | System       | System    |
    And the following "system role assigns" exist:
      | user     | role                |
      | limited1 | coursecreatorviewer |
    And I log in as "limited1"
    When I navigate to "Courses" in site administration
    Then I should not see "Site content bank"

  Scenario: Site content bank link is distinct from the Plugins content bank custom fields entry
    Given I log in as "admin"
    When I navigate to "Plugins > Content bank > Content bank custom fields" in site administration
    Then I should see "Content bank custom fields"
    And I navigate to "Courses > Site content bank" in site administration
    Then I should see "Content bank"
