@block @block_mhaairs @block_mhaairs_webservices
Feature: Web service - get environment info

  Background:
    Given the following config values are set as admin:
        | timezone              | America/Toronto   |
        | enablewebservices     | 1                 |
        | auth                  | webservice        |
        | webserviceprotocols   | rest              |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following web services are enabled:
        | component     | shortname         | name  |
        | block_mhaairs | mhaairs_gradebook |       |
        | block_mhaairs | mhaairs_util      |       |
    And the following tokens exist:
        | service           | user      | validuntil    | iprestriction |
        | mhaairs_gradebook | admin     |               |               |
        | mhaairs_util      | admin     |               |               |

    #And the mhaairs customer number and shared secret are set


    @javascript
    Scenario: Get environment info.
        And I log in as "admin"

        And I follow "Site administration"
        And I follow "Plugins"
        And I follow "McGraw-Hill AAIRS"
        And I follow "Web service test client"
        And I set the field with xpath "//form[@id='function-selector']//select" to "block_mhaairs_get_environment_info"
        And I set the token field to "admin" token for "mhaairs_util" service

        When I press "Execute"
        Then I should see "'{\"system\":\""
