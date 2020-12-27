@core @core_admin
Feature: Configure language settings for the site
  In order to configure language settings for the site
  As an admin
  I want to set language settings relevant to my site users

  Scenario: Set languages on language menu
    Given I log in as "admin"
    And I navigate to "Language > Language settings" in site administration
    When I set the field "Languages on language menu" to "en"
    And I press "Save changes"
    Then I should not see "Invalid language code"

  Scenario: Reset languages on language menu
    Given I log in as "admin"
    And I navigate to "Language > Language settings" in site administration
    When I set the field "Languages on language menu" to ""
    And I press "Save changes"
    Then I should not see "Invalid language code"

  Scenario Outline: Set languages on language menu with invalid language
    Given I log in as "admin"
    And I navigate to "Language > Language settings" in site administration
    When I set the field "Languages on language menu" to "<fieldvalue>"
    And I press "Save changes"
    Then I should see "Invalid language code: <invalidlang>"
    Examples:
      | fieldvalue | invalidlang |
      | xx         | xx          |
      | xx\|Bad    | xx          |
      | en,qq      | qq          |
      | en,qq\|Bad | qq          |
      | en$$       | en$$        |
      | en$$\|Bad  | en$$        |
