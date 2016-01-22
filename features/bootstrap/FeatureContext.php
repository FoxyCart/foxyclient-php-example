<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{

    /**
     * @var string URL of foxyclient homepage
     */
    private $homepage = 'http://localhost:8000/';

    /**
     * @var string Name of the Foxy store you want to create
     */
    private $new_foxy_store_domain = 'some-new-store';

    /**
     * @var string Name of already existed store
     */
    private $old_foxy_store_domain = 'some-old-store';

    /**
     * @var string Email of new Foxy user
     */
    private $new_user = 'example-user@example.com';

    /**
     * @var string Email of existing user
     */
    private $old_user = 'existing-user@example.com';

    /**
     * @var array Details of the new Foxy user you want to create
     */
    private $user = [
        'first_name'=>'John',
        'last_name' => 'Doe',
        'email' => 'johndoe@example.com',
        'phone' => '888888888',
        'is_developer' => true,
        'is_designer' => false,
        'is_merchant' => false
    ];

    /**
     * @var array Details of the new Foxy Store you want to create
     */
    private $store = [
        'store_name' => 'new behat test store',
        'store_domain' => 'test',
        'use_remote_domain' => false,
        'store_url' => 'http://example.com/',
        'store_email' => 'example@example.com',
        'postal_code' => '37211',
        'region' => 'TN',
        'country' => 'US'
    ];

    /**
     * @var array Details of the project you want to create
     */
    private $project = [
        'name' => 'My Project',
        'description' => 'This is description of my project.',
        'company_name' => 'Test Company LLC',
        'company_url' => 'www.example.com',
        'company_logo' => '',
        'contact_name' => 'John Doe',
        'contact_email' => 'johns@example.com',
        'contact_phone' => '888888888',
        'redirect_uri' => 'http://localhost:8000/index.php?action=authorization_code_grant',
        'js_origin_uri' => ''
    ];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    public function getRandomEmail()
    {
        return 'johndoe_' . rand() . '@example.com';
    }

    public function getRandomStoreDomain()
    {
        return 'teststore-' . rand();
    }

    /**
     * @AfterStep
     */
    public function failError(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $filtered = $this->getSession()->getPage()->find('css', '.alert-danger');
            if ($filtered) {
                echo $filtered->getText();
            }
        }
    }

    /**
     * @Given I am on the homepage.
     */
    public function iAmOnTheHomepage()
    {
        $this->visit($this->homepage);
        $this->assertPageContainsText('SANDBOX');
    }

    /**
     * @When I register my application
     */
    public function iRegisterMyApplication()
    {
        $this->clickLink('Register your application');

        $this->fillField('Project Name', $this->project['name']);
        $this->fillField('Project Description', $this->project['description']);
        $this->fillField('Company Name', $this->project['company_name']);
        $this->fillField('Company URL', $this->project['company_url']);
        $this->fillField('Company Logo', $this->project['company_logo']);

        $this->fillField('Contact Name', $this->project['contact_name']);
        $this->fillField('Contact Email', $this->project['contact_email']);
        $this->fillField('Contact Phone', $this->project['contact_phone']);
        $this->fillField('Redirect URI', $this->project['redirect_uri']);
        $this->fillField('Javascript Origin URI', $this->project['js_origin_uri']);

        $this->pressButton('Create Client');
    }

    /**
     * @Then my application should be registered
     */
    public function myApplicationShouldBeRegistered()
    {
        $this->assertPageContainsText('created successfully');
    }

    /**
     * @When I check if the new user already exists
     */
    public function iCheckIfTheANewUserAlreadyExists()
    {
        $this->clickLink('Check if a Foxy user exists');
        $this->fillField('User Email Address', $this->new_user);
        $this->pressButton('Check User');
    }

    /**
     * @When I check if an old user already exists
     */
    public function iCheckIfAnOldUserAlreadyExists()
    {
        $this->clickLink('Check if a Foxy user exists');
        $this->fillField('User Email Address', $this->old_user);
        $this->pressButton('Check User');
    }

    /**
     * @When I create a foxy user
     */
    public function iCreateAFoxyUser()
    {
        $this->clickLink('Create a Foxy user');
        $this->fillField('First Name', $this->user['first_name']);
        $this->fillField('Last Name', $this->user['last_name']);
        $this->fillField('Email', $this->getRandomEmail());
        $this->fillField('Phone', $this->user['phone']);
        $this->checkOption('is_programmer');
        $this->pressButton('Create User');
    }

    /**
     * @Then the user should be created successfully
     */
    public function theUserShouldBeCreatedSuccessfully()
    {
        $this->assertPageContainsText('created successfully');
    }

    /**
     * @When I check if an old foxy store already exist
     */
    public function iCheckIfAnOldFoxyStoreAlreadyExist()
    {
        $this->clickLink('Check if a Foxy store exists');
        $this->fillField('store_domain', $this->old_foxy_store_domain);
        $this->pressButton('Check Store');
    }

    /**
     * @When I check if the new foxy store already exist
     */
    public function iCheckIfTheNewFoxyStoreAlreadyExist()
    {
        $this->clickLink('Check if a Foxy store exists');
        $this->fillField('store_domain', $this->new_foxy_store_domain);
        $this->pressButton('Check Store');
    }

    /**
     * @Given I return to the homepage
     */
    public function iReturnToTheHomepage()
    {
        $this->iAmOnTheHomepage();
    }

    /**
     * @When I create a foxy store
     */
    public function iCreateAFoxyStore()
    {
        $this->iCreateAFoxyUser();
        $this->iReturnToTheHomepage();
        $this->clickLink('Create a Foxy store');
        $this->fillField('Store Name', $this->store['store_name']);
        $this->fillField('Store Domain', $this->getRandomStoreDomain());
        $this->fillField('Store URL', $this->store['store_url']);
        $this->fillField('Store Email', $this->store['store_email']);
        $this->fillField('Store Postal Code', $this->store['postal_code']);
        $this->fillField('Store Country', $this->store['country']);
        $this->fillField('Store State', $this->store['region']);
        $this->pressButton('Create Store');
    }

    /**
     * @Then the store should be created successfully
     */
    public function theStoreShouldBeCreatedSuccessfully()
    {
        $this->assertPageContainsText('created successfully');
    }
}
