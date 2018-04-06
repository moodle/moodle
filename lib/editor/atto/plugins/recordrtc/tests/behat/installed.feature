@editor @editor_atto @atto @recordrtc @atto_recordrtc @atto_recordrtc_installed
Feature: Installation succeeds
  In order to use this plugin
  As a user
  I need the installation to work

  Scenario: Check the Plugins overview for the name of this plugin
    Given I log in as "admin"
    And I navigate to "Plugins overview" node in "Site administration > Plugins"
    Then the following should exist in the "plugins-control-panel" table:
      |RecordRTC|
      |atto_recordrtc|