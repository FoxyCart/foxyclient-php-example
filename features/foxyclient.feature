Feature: FoxyClient
  In order to test foxyclient
  As a developer
  I want to be able to run foxyclient features

  Scenario: Create an OAuth client
    Given I am on the homepage.
    When I register my application
    Then my application should be registered

  Scenario: New user should not already exist
    Given I am on the homepage.
    And I register my application
    And I return to the homepage
    When I check if the new user already exists
    Then I should see "This user was not found"

  Scenario: Old user should already exist
    Given I am on the homepage.
    And I register my application
    And I return to the homepage
    When I check if an old user already exists
    Then I should see "User Exists"

  Scenario: Create a Foxy user
    Given I am on the homepage.
    And I register my application
    And I return to the homepage
    When I create a foxy user
    Then the user should be created successfully

  Scenario: New Foxy Store should not already exist
    Given I am on the homepage.
    And I register my application
    And I return to the homepage
    When I check if the new foxy store already exist
    Then I should see "This store was not found."

  Scenario: Create a Foxy store
    Given I am on the homepage.
    And I register my application
    And I return to the homepage
    When I create a foxy store
    Then the store should be created successfully
