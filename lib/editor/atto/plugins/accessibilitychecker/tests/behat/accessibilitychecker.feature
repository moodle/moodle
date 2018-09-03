@editor @editor_atto @atto @atto_accessibilitychecker
Feature: Atto accessibility checker
  To write accessible text in Atto, I need to check for accessibility warnings.

  @javascript
  Scenario: Images with no alt
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Some plain text</p><img src='/broken-image' width='1' height='1'/><p>Some more text</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Images require alternative text."
    And I follow "/broken-image"
    And I wait "2" seconds
    And I click on "Insert or edit image" "button"
    And the field "Enter URL" matches value "/broken-image"
    And I set the field "Describe this image for someone who cannot see it" to "No more warning!"
    And I press "Save image"
    And I press "Accessibility checker"
    And I should see "Congratulations, no accessibility problems found!"
    And I click on ".moodle-dialogue-focused .closebutton" "css_element"
    And I select the text in the "Description" Atto editor
    And I click on "Insert or edit image" "button"
    And I set the field "Describe this image for someone who cannot see it" to ""
    And I set the field "Description not necessary" to "1"
    And I press "Save image"
    And I press "Accessibility checker"
    And I should see "Congratulations, no accessibility problems found!"

  @javascript
  Scenario: Low contrast
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p style='color: #7c7cff; background-color: #ffffff;'>Hard to read</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "The colours of the foreground and background text do not have enough contrast."

  @javascript
  Scenario: No headings
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Sweet roll oat cake jelly-o macaroon donut oat cake. Caramels macaroon cookie sweet roll croissant cheesecake candy jelly-o. Gummies sugar plum sugar plum gingerbread dessert. Tiramisu bonbon jujubes danish marshmallow cookie chocolate cake cupcake tiramisu. Bear claw oat cake chocolate bar croissant. Lollipop cookie topping liquorice croissant. Brownie cookie cupcake lollipop cupcake cupcake. Fruitcake dessert sweet biscuit dragée caramels marzipan brownie. Chupa chups gingerbread apple pie cookie liquorice caramels carrot cake cookie gingerbread. Croissant candy jelly beans. Tiramisu apple pie dessert apple pie macaroon soufflé. Brownie powder carrot cake chocolate. Tart applicake croissant dragée macaroon chocolate donut.</p><p>Jelly beans gingerbread tootsie roll. Sugar plum tiramisu cotton candy toffee pie cotton candy tiramisu. Carrot cake chocolate bar sesame snaps cupcake cake dessert sweet fruitcake wafer. Marshmallow cupcake gingerbread pie sweet candy canes powder gummi bears. Jujubes cake muffin marshmallow candy jelly beans tootsie roll pie. Gummi bears applicake chocolate cake sweet jelly sesame snaps lollipop lollipop carrot cake. Marshmallow cake jelly beans. Jelly beans sesame snaps muffin halvah cookie ice cream candy canes carrot cake. Halvah donut marshmallow tiramisu. Cookie dessert gummi bears. Sugar plum apple pie jelly beans gummi bears tart chupa chups. Liquorice macaroon gummi bears gummies macaroon marshmallow sweet roll cake topping. Lemon drops caramels pie icing danish. Chocolate cake oat cake dessert halvah danish carrot cake apple pie.</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "There is a lot of text with no headings."

  @javascript
  Scenario: Merged cells
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<table><caption>Dogs that look good in pants</caption><tr><th>Breed</th><th>Coolness</th></tr><tr><td>Poodle</td><td rowspan='2'>NOT COOL</td></tr><tr><td>Doberman</td></tr></table>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Tables should not contain merged cells."

  @javascript
  Scenario: Table missing row/column headers
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<table><caption>Dogs that look good in pants</caption><tr><th>Breed</th><td>Coolness</td></tr><tr><td>Poodle</td><td>NOT COOL</td></tr><tr><td>Doberman</td><td>COOL</td></tr></table>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Tables should use row and/or column headers."

  @javascript
  Scenario: Table missing caption
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<table><tr><th>Breed</th><th>Coolness</th></tr><tr><td>Poodle</td><td>NOT COOL</td></tr><tr><td>Doberman</td><td>COOL</td></tr></table>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Tables should have captions."
