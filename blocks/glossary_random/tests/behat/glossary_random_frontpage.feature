@block @block_glossary_random
Feature: Random glossary entry block can be added to the frontpage
  In order to show the entries from glossary on the frontpage
  As a teacher
  I can add the random glossary entry to the frontpage

Scenario: Admin can add random glossary block to the frontpage
  Given the following "activities" exist:
    | activity   | name             | intro                          | course               | idnumber  |
    | glossary   | Tips and Tricks  | Frontpage glossary description | Acceptance test site | glossary0 |
  And I log in as "admin"
  And I click on "Turn editing on" "link" in the "Administration" "block"
  And I add the "Random glossary entry" block
  And I configure the "block_glossary_random" block
  And I set the following fields to these values:
    | Title                           | Tip of the day  |
    | Take entries from this glossary | Tips and Tricks |
  And I press "Save changes"
  And I click on "Add a new entry" "link" in the "Tip of the day" "block"
  And I set the following fields to these values:
    | Concept    | Never come late               |
    | Definition | Come in time for your classes |
  And I press "Save changes"
  When I log out
  Then I should see "Never come late" in the "Tip of the day" "block"
  And I should see "Come in time for your classes" in the "Tip of the day" "block"
