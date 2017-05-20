Feature: Update a print
  In order change a print of mine
  As a user
  I need to be able to update a specific print of mine

  Background:
    Given there are users:
      | username  | apiToken       |
      | ultimaker | my-api-token   |
      | bob       | bobs-api-token |
    And there are prints:
      | user      | publicId | title          | description     | image      |
      | ultimaker | 12345678 | My first print | How nice it is. | image1.png |

  Scenario: Updating a print
    Given I am authenticated as user "ultimaker"
    When I update print "12345678" and set the title to "Print it baby!", the description to "Just do it." and the image to "image4.png"
    Then the print should be updated
    And I should get the updated print returned when I view print "12345678"

  Scenario: Updating a print that is not mine
    Given I am authenticated as user "bob"
    When I update print "12345678" and set the title to "Print it baby!", the description to "Just do it." and the image to "image4.png"
    Then the print should not be updated
