@tool @tool_dataprivacy
Feature: Authorized users can request others personal data
  In order to export or access another users data
  As a designated role
  I need the correct permissions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | user1    | User1     | One      | user1@example.com    |
      | user2    | User2     | Two      | user2@example.com    |
      | officer1 | Officer1  | One      | officer1@example.com |
    # Create Privacy Officer Role.
    And the following "role" exists:
      | shortname                                    | privacyofficer  |
      | name                                         | Privacy Officer |
      | context_system                               | 1               |
      | tool/dataprivacy:managedataregistry          | allow           |
      | tool/dataprivacy:managedatarequests          | allow           |
      | tool/dataprivacy:makedatarequestsforchildren | allow           |
      | moodle/site:configview                       | allow           |
      | moodle/category:viewhiddencategories         | allow           |
      | moodle/course:viewhiddencourses              | allow           |
      | moodle/course:viewhiddenactivities           | allow           |
      | moodle/course:view                           | allow           |
    # Create Parent Role.
    And the following "role" exists:
      | shortname                                    | parentrole  |
      | name                                         | Parent Role |
      | context_user                                 | 1           |
      | moodle/user:viewdetails                      | allow       |
      | moodle/user:viewalldetails                   | allow       |
      | moodle/user:readuserblogs                    | allow       |
      | moodle/user:readuserposts                    | allow       |
      | moodle/user:viewuseractivitiesreport         | allow       |
      | moodle/user:editprofile                      | allow       |
      | tool/policy:acceptbehalf                     | allow       |
      | tool/dataprivacy:makedatarequestsforchildren | allow       |
    # Add permission to allow parent to make requests on behalf of child user.
    And the following config values are set as admin:
      | contactdataprotectionofficer | 1 | tool_dataprivacy |
    And I log in as "admin"

  @javascript
  Scenario: Privacy officer can request for other user's personal data
    Given I navigate to "Users > Permissions > Assign system roles" in site administration
    # Assign Privacy Officer role to officer1.
    And I follow "Privacy Officer"
    And I set the field "addselect_searchtext" to "Officer1"
    And I set the field "addselect" to "Officer1 One (officer1@example.com)"
    And I press "Add"
    # Navigate to home in order to navigate properly to Privacy settings.
    And I am on site homepage
    # Select Privacy officer in the Orivacy officer role mapping setting.
    And I navigate to "Users > Privacy and policies > Privacy settings" in site administration
    And I click on "Privacy Officer" "checkbox"
    And I press "Save changes"
    And I log in as "officer1"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    # Create a new request as the designated privacy officer.
    When I follow "New request"
    And I set the field "User" to "User1 One"
    And I set the field "Comment" to "User One data"
    And I press "Save changes"
    # Confirm that the new data request is successfully created for selected user with status "Awaiting approval".
    Then the following should exist in the "generaltable" table:
      | Type   | User      | Requested by | Status            | Message       |
      | Export | User1 One | Officer1 One | Awaiting approval | User One data |

  @javascript
  Scenario: Parent user can request data on behalf of child user
    Given I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "User1 One"
    And I click on "Preferences" "link" in the ".profile_tree" "css_element"
    # Assign user2 as parent for user1.
    And I follow "Assign roles relative to this user"
    And I follow "Parent"
    And I set the field "Potential users" to "User2 Two (user2@example.com)"
    And I click on "Add" "button" in the "#page-content" "css_element"
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I follow "Data requests"
    # As parent, create a data request for a child user.
    And I follow "New request"
    And I click on "User" "field"
    When I type "User1 One"
    # Confirm that only the parent's child users can be searched and selected.
    Then I should see "User1 One"
    And I type "User2 Two"
    And I should see "No suggestions"
    And I type "Officer1 One"
    And I should see "No suggestions"
    And I set the field "Search" to "User1"
    And I set the field "Comment" to "This is a comment"
    And I press "Save changes"
    # Confirm that data request was successfully made by parent on behalf of child user.
    And I should see "Your request has been submitted to the privacy officer"
    And the following should exist in the "generaltable" table:
      | Type                                       | Requested by | Status            | Message           |
      | Export all of my personal data (User1 One) | User2 Two    | Awaiting approval | This is a comment |
