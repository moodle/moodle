@auth @auth_iomadsaml2 @javascript
Feature: Automatically log in
  In order to have correct Moodle access on pages that allow public access
  As a user
  I should be automatically logged in if I am logged into the IdP

  Scenario: Autologin on first request in session (logged in)
    Given the authentication plugin iomadsaml2 is enabled # auth_iomadsaml2
    And the mock SAML IdP is configured # auth_iomadsaml2
    And the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | iomadsaml2 | Eigh      | Person   |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | auth      | iomadsaml2 |            |
      | autologin | 1     | auth_iomadsaml2 |
    When I am on site homepage
    And the mock SAML IdP allows passive login with the following attributes: # auth_iomadsaml2
      | uid | student1 |
    Then I should see "Course 1"
    And I should see "Eigh Person"

    # Future requests should not contact the IdP (obviously, because logged in).
    When I follow "Course 1"
    Then I should see "Course 1"
    And I should see "Participants"

  Scenario: Autologin on first request in session (not logged in)
    Given the authentication plugin iomadsaml2 is enabled # auth_iomadsaml2
    And the mock SAML IdP is configured # auth_iomadsaml2
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following config values are set as admin:
      | auth      | iomadsaml2 |            |
      | autologin | 1     | auth_iomadsaml2 |
    When I am on site homepage
    And the mock SAML IdP does not allow passive login # auth_iomadsaml2
    Then I should see "You are not logged in."

    # Future requests should not contact the IdP.
    When I follow "Course 1"
    Then I should see "Log in"

  Scenario: Autologin on cookie change
    Given the authentication plugin iomadsaml2 is enabled # auth_iomadsaml2
    And the mock SAML IdP is configured # auth_iomadsaml2
    And the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | iomadsaml2 | Eigh      | Person   |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "course enrolments" exist:
      | user     | course | role |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | auth            | iomadsaml2 |            |
      | autologin       | 2     | auth_iomadsaml2 |
      | autologincookie | frog  | auth_iomadsaml2 |

    # No login attempt initially.
    When I am on site homepage
    Then I should see "You are not logged in."

    # Changing the cookies results in a login attempt.
    When the cookie "frog" is set to "Kermit" # auth_iomadsaml2
    And I am on site homepage
    And the mock SAML IdP does not allow passive login # auth_iomadsaml2
    Then I should see "You are not logged in."

    # No login attempt on another page request.
    When I am on site homepage
    Then I should see "You are not logged in."

    # Changing cookies again, there will be another login attempt.
    When the cookie "frog" is set to "Mr Toad" # auth_iomadsaml2
    And I am on site homepage
    And the mock SAML IdP allows passive login with the following attributes: # auth_iomadsaml2
      | uid | student1 |
    Then I should see "Eigh Person"

    # No login attempt on another page request, even if the cookie changes
    # or is removed, because the user is logged in now.
    When the cookie "frog" is set to "Kermit" # auth_iomadsaml2
    And I am on site homepage
    Then I should see "Eigh Person"
    When the cookie "frog" is removed # auth_iomadsaml2
    And I am on site homepage
    Then I should see "Eigh Person"

  Scenario: Autologin to activity page within a course
    Given the authentication plugin iomadsaml2 is enabled # auth_iomadsaml2
    And the mock SAML IdP is configured # auth_iomadsaml2
    And the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | iomadsaml2 | Eigh      | Person   |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "activities" exist:
      | activity | course | idnumber | name           | content               |
      | page     | C1     | page1    | Test page name | Test page description |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
    And the following config values are set as admin:
      | auth            | iomadsaml2 |            |
      | autologinguests | 1     |            |
      | autologin       | 1     | auth_iomadsaml2 |
    When I am on the "page1" "Activity" page
    And the mock SAML IdP allows passive login with the following attributes: # auth_iomadsaml2
      | uid | student1 |
    Then I should see "Test page description"
    And I should see "Eigh Person"

  Scenario: Situations which are excluded from autologin
    Given the authentication plugin iomadsaml2 is enabled # auth_iomadsaml2
    And the mock SAML IdP is configured # auth_iomadsaml2
    And the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | iomadsaml2 | Eigh      | Person   |
    And the following config values are set as admin:
      | auth            | iomadsaml2 |            |
      | autologin       | 2     | auth_iomadsaml2 |
      | autologincookie | frog  | auth_iomadsaml2 |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | html | System       | 1         | site-index       | side-post     |
    And I am on site homepage

    # With this config, changing the cookie would usually result in an autologin attempt.
    When the cookie "frog" is set to "Kermit" # auth_iomadsaml2

    # Situation 1: Autologin does not run on login screens.
    And I follow "Log in"
    Then I should see "You are not logged in."

    # Situation 2: Autologin does not run if turned off (obviously).
    When the following config values are set as admin:
      | autologin | 0 | auth_iomadsaml2 |
    And I am on site homepage
    Then I should see "You are not logged in."

    # Situation 3: Autologin does not run if the plugin is not enabled.
    When the following config values are set as admin:
      | autologin | 2      | auth_iomadsaml2 |
      | auth      | manual |            |
    And I am on site homepage
    Then I should see "You are not logged in."

    # Set up the homepage so that we can test POST requests
    When I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I configure the "block_html" block
    And I set the field "Content" to "<form method='post' action='.'><div><button type='submit'>PostTest</button></div></form>"
    And I press "Save changes"
    And I click on "Log out" "link" in the "#page-footer" "css_element"

    # Situation 4: Autologin does not run on POST requests.
    When the following config values are set as admin:
      | auth | iomadsaml2 |
    And I press "PostTest"
    Then I should see "You are not logged in."

    # Finally, just confirm we have things set up right by trying a normal GET request.
    When I am on site homepage
    And the mock SAML IdP allows passive login with the following attributes: # auth_iomadsaml2
      | uid | student1 |
    Then I should see "Eigh Person"
