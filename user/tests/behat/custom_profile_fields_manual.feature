@core @core_user
Feature: Custom profile fields creation using UI

  @javascript
  Scenario Outline: Manual creation of basic custom profile fields
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "<name>" "link"
    And I set the following fields to these values:
      | Short name                    | <shortname>  |
      | Name                          | <name>  |
    When I click on "Save changes" "button"
    Then I should see "<name>"

    Examples:
      | shortname | name       |
      | datetime  | Date/Time  |
      | textarea  | Text area  |
      | textinput | Text input |

  @javascript
  Scenario: Manual creation of drop-down menu custom profile field type
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Drop-down menu" "link"
    And I set the following fields to these values:
      | Short name  | dropdownmenu   |
      | Name        | Drop-down menu field |
    And I set the field "Menu options (one per line)" to multiline:
    """
    a
    b
    """
    When I click on "Save changes" "button"
    Then I should see "Drop-down menu field"

  @javascript
  Scenario: Manual creation of checkbox and social custom profile field type
    Given I log in as "admin"
    And I navigate to "Users > Accounts > User profile fields" in site administration
    And I click on "Create a new profile field" "link"
    And I click on "Social" "link"
    And I set the following fields to these values:
      | Network type  | Web page  |
      | Short name    | social    |
    When I click on "Save changes" "button"
    Then I should see "Web page"
    And I click on "Create a new profile field" "link"
    And I click on "Checkbox" "link"
    And I set the following fields to these values:
      | Short name                    | checkbox  |
      | Name                          | Checkbox  |
    When I click on "Save changes" "button"
    Then I should see "Checkbox"
