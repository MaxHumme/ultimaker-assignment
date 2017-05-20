Feature: Delete a print
  In order delete a print of mine
  As a user
  I need to be able to delete a specific print of mine

  Background:
    Given there are users:
      | username  | apiToken       |
      | ultimaker | my-api-token   |
      | bob       | bobs-api-token |
    And there are prints:
      | user      | publicId | title          | description     | image      |
      | ultimaker | 12345678 | My first print | How nice it is. | image1.png |

  Scenario: Deleting a print
    Given I am authenticated as user "ultimaker"
    When I delete print "12345678"
    Then the print should be deleted
    And the print should not be returned when I view my prints

  Scenario: Deleting a print that is not mine
    Given I am authenticated as user "bob"
    When I delete print "12345678"
    Then the print should not be deleted
