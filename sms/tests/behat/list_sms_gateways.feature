@sms
Feature: Access the SMS gateways page
  In order to maintain SMS functionality
  As an admin
  I need to be able to view and manage SMS gateways

  Background: Manage the SMS gateways
    Given the following "core_sms > sms_gateways" exist:
      | name    | classname                | enabled  | config                                     |
      | Default | smsgateway_aws\gateway   | 1        | {"countrycode":"8802","gateway":"aws_sns"} |
      | Europe  | smsgateway_aws\gateway   | 1        | {"countrycode":"+61","gateway":"aws_sns"}  |

  @javascript
  Scenario: An admin can manage the SMS gateways in the gateway management page
    Given I log in as "admin"
    And I navigate to "Plugins > SMS > Manage SMS gateways" in site administration
    And I should see "SMS gateways"
    And I should see "Create new SMS gateway"
    And I should see "Default" in the "sms_gateways_table" "table"
    And I should see "Europe" in the "sms_gateways_table" "table"
    And I should see "Europe" in the "sms_gateways_table" "table"
    And I should see "Disable Default" in the "Default" "table_row"
    And I should see "Disable Europe" in the "Europe" "table_row"
    When I toggle the "Disable Default" admin switch "off"
    Then I should see "Default disabled"
    And I should see "Enable Default" in the "Default" "table_row"
    And I should see "Edit" in the "Default" "table_row"
    And I should see "Delete" in the "Default" "table_row"
    And I click on "Delete" "link" in the "Default" "table_row"
    And I should see "This will delete the Default SMS gateway."
    And I click on "Delete" "button"
    And I should see "Default SMS gateway deleted"
    And I should not see "Default" in the "sms_gateways_table" "table"
