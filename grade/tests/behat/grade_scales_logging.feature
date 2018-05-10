@core @core_grades
Feature: We can view the logs for any changes to grade scales.
  In order to view changes grade scales
  As an administrator
  I need to add make changes and then view the logs.

  Scenario: I edit scales and then view the logs.
    Given I log in as "admin"
    And I navigate to "Scales" node in "Site administration > Grades"
    # Add a scale
    And I press "Add a new scale"
    And I set the following fields to these values:
      | Name  | Letterscale |
      | Scale | F,D,C,B,A   |
    And I press "Save changes"
    # Delete first scale
    And I follow "Delete"
    And I press "Continue"
    # Edit first scale
    And I follow "Edit"
    And I set the following fields to these values:
      | id_scale | ONE,TWO,THREE |
    And I press "Save changes"
    When I navigate to "Live logs" node in "Site administration > Reports"
    Then I should see "Scale created"
    And I should see "Scale updated"
    And I should see "Scale deleted"
