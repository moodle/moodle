@core @core_admin @core_admin_roles
Feature: Highlight non-inherited permissions
  In order that the status of capabilities can be more easily seen
  As an admin
  I need altered permissions to be highlighted

  Background:
    Given the following "courses" exist:
      | fullname        | shortname   |
      | Course fullname | C_shortname |
    And I log in as "admin"
    And I am on site homepage

  @javascript
  Scenario: Override a permission
    Given I follow "Course fullname"
    When I expand "Users" node
    And I follow "Permissions"
    And I select "1" from the "roleid" singleselect
    And I click on "Prohibit" "radio" in the "View added and updated modules in recent activity block" "table_row"
    And I press "Save changes"
    And I select "1" from the "roleid" singleselect
    Then the "class" attribute of "View added and updated modules in recent activity block" "table_row" should contain "overriddenpermission"
