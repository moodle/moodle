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

  @javascript
  Scenario: Override a permission
    Given I am on "Course fullname" course homepage
    And I navigate to "Users > Permissions" in current page administration
    And I select "Manager (0)" from the "roleid" singleselect
    And I click on "Prohibit" "radio" in the "View added and updated modules in recent activity block" "table_row"
    And I press "Save changes"
    And I select "Manager (1)" from the "roleid" singleselect
    Then the "class" attribute of "View added and updated modules in recent activity block" "table_row" should contain "overriddenpermission"
