@block @block_mhaairs @block_mhaairs-site_administration @javascript
Feature: Configuration settings

    ##/:
    ## Default settings
    ##:/
    Scenario: Default settings
        Given I log in as "admin"
        And I follow "Site administration"
        And I follow "Plugins"
        And I follow "McGraw-Hill AAIRS"
        Then "input[id=id_s__block_mhaairs_sslonly]:not([checked])" "css_element" should exist
        And "input[name=s__block_mhaairs_customer_number]:empty" "css_element" should exist
        And "input[name=s__block_mhaairs_shared_secret]:empty" "css_element" should exist
        And "input[id=id_s__block_mhaairs_display_services_MHCampus]" "css_element" should not exist
        And "input[id=id_s__block_mhaairs_display_helplinks][checked]" "css_element" should not exist
        And "input[id=id_s__block_mhaairs_sync_gradebook][checked]" "css_element" should exist
        And "input[name=s__block_mhaairs_locktype]" "css_element" should not exist
        And "input[id=id_s__block_mhaairs_gradelog]:not([checked])" "css_element" should exist
    #:Scenario

    ##/:
    ## Settings after adding customer number
    ##:/
    Scenario: Settings after adding customer number and shared secret
        Given I log in as "admin"
        And the mhaairs customer number and shared secret are set
        And I follow "Site administration"
        And I follow "Plugins"
        And I follow "McGraw-Hill AAIRS"
        Then "input[id=id_s__block_mhaairs_sslonly]:not([checked])" "css_element" should exist
        And "input[name=s__block_mhaairs_customer_number]:not(empty)" "css_element" should exist
        And "input[name=s__block_mhaairs_shared_secret]:not(empty)" "css_element" should exist
        And "input[id=id_s__block_mhaairs_display_services_MHCampus]:not([checked])" "css_element" should exist
        And "input[id=id_s__block_mhaairs_display_helplinks][checked]" "css_element" should not exist
        And "input[id=id_s__block_mhaairs_sync_gradebook][checked]" "css_element" should exist
        And "input[name=s__block_mhaairs_locktype]" "css_element" should not exist
        And "input[id=id_s__block_mhaairs_gradelog]:not([checked])" "css_element" should exist
    #:Scenario
