@core
Feature: Expand single fieldset in Behat tests
  In order to expand all fieldsets when there is only one
  As a developer
  I need Behat to successfully expand that fieldset

  @javascript
  Scenario: Test expand all fieldsets when there is only one fieldset
    Given I log in as "admin"
    # This page was selected because it only has one fieldset.
    When I navigate to "Users > Accounts > Upload users" in site administration
    # Close the fieldset manually...
    And I click on "//a[@data-toggle='collapse']" "xpath_element"
    And I should not see "Example text file"
    # Expand using 'expand all' step.
    And I expand all fieldsets
    Then I should see "Example text file"
