<?php
require __DIR__ . '/bootstrap.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

// update our session/client if needed.
// NOTE: This example uses the session, but you could also be using a database or some other persistance layer.
if (isset($_SESSION['access_token']) && $fc->getAccessToken() != $_SESSION['access_token']) {
    if ($fc->getAccessToken() == '') {
        $fc->setAccessToken($_SESSION['access_token']);
    }
}
if (isset($_SESSION['refresh_token']) && $fc->getRefreshToken() != $_SESSION['refresh_token']) {
    if ($fc->getRefreshToken() == '') {
        $fc->setRefreshToken($_SESSION['refresh_token']);
    }
}
if (isset($_SESSION['client_id']) && $fc->getClientId() != $_SESSION['client_id']) {
    if ($fc->getClientId() == '') {
        $fc->setClientId($_SESSION['client_id']);
    }
}
if (isset($_SESSION['client_secret']) && $fc->getClientSecret() != $_SESSION['client_secret']) {
    if ($fc->getClientSecret() == '') {
        $fc->setClientSecret($_SESSION['client_secret']);
    }
}
if (isset($_SESSION['access_token_expires']) && $fc->getAccessTokenExpires() != $_SESSION['access_token_expires']) {
    if ($fc->getAccessTokenExpires() == '') {
        $fc->setAccessTokenExpires($_SESSION['access_token_expires']);
    }
}

$fc->refreshTokenAsNeeded();

// let's see if we have access to a store... if so, bookmark some stuff
if ($fc->getAccessToken() != '') {
    if (!isset($_SESSION['store_uri']) || $_SESSION['store_uri'] == ''
        || !isset($_SESSION['store_name']) || $_SESSION['store_name'] == '') {
        $result = $fc->get();
        $store_uri = $fc->getLink('fx:store');
        //$user_uri = $fc->getLink('fx:user');
        //$client_uri = $fc->getLink('fx:client');
        if ($store_uri != '') {
            $_SESSION['store_uri'] = $store_uri;
            $result = $fc->get($store_uri);
            $errors = $fc->getErrors($result);
            if (!count($errors)) {
                $_SESSION['store_name'] = $result['store_name'];
                $_SESSION['item_categories_uri'] = $result['_links']['fx:item_categories']['href'];
                $_SESSION['coupons_uri'] = $result['_links']['fx:coupons']['href'];
            }
        //} elseif ($user_uri != '') {
        //    $_SESSION['user_uri'] = $user_uri;
        //} elseif ($client_uri != '') {
        //    $_SESSION['client_uri'] = $client_uri;
        }
    }
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Example Requests for the Foxy Hypermedia API</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <style>
        body { padding-bottom: 70px; }
        footer { padding-top: 50px; clear:both; }
    </style>
  </head>
  <body>

<nav class="navbar navbar-default">
  <div class="container">
    <a class="navbar-brand" href="/">Foxy hAPI Example</a>
    <ul class="nav navbar-nav">
      <li><a href="/?action=">Home</a></li>
      <li><a target="_blank" href="https://api<?php print ($fc->getUseSandbox() ? '-sandbox' : ''); ?>.foxycart.com/hal-browser/browser.html">HAL Browser</a></li>
        <?php
        if (isset($_SESSION['store_name'])) {
            ?>
            <li class="divider"></li>
            <li class="navbar-text">STORE: <?php print $_SESSION['store_name']; ?></li>
            <?php
        }
        if ($fc->getClientId() != '') {
        ?>
            <li><a href="/?action=logout">Logout</a></li>
        <?php
        }
        ?>
    </ul>
    <ul class="nav navbar-nav navbar-right">
       <li><p class="navbar-text"><?php print ($fc->getUseSandbox() ? '<span class="text-success">SANDBOX</span>' : '<span class="text-danger">PRODUCTION</span>'); ?></p></li>
    </ul>
  </div>
</nav>
    <div class="container">
<?php

// BEGIN HERE
if ($action == '') {
    ?>
    <h1>Welcome to the Foxy Hypermedia API example!</h1>
    <p>
        If you haven't already, please check out the <a href="https://api-sandbox.foxycart.com/docs">Foxy hAPI documentation</a> to better understand the purpose of this library.
    </p>
    <?php
    if (isset($_SESSION['store_name'])) {
    ?>
    <p>The following are examples of interacting with the Hypermedia API using the FoxyClient PHP library to perform CRUD operations on store elements. This is just a subset of what is possible with the API and is provided to give a practical overview of how it can function.</p>
    <h3>Store: <?php print $_SESSION['store_name']; ?></h3>
    <h4>Coupons</h4>
    <ul>
        <li><a href="/?action=view_coupons">View all coupons</a></li>
        <li><a href="/?action=add_coupon_form">Add a new coupon</a></li>
    </ul>
    <h4>Categories</h4>
    <ul>
        <li><a href="/?action=view_item_categories">View all item categories</a></li>
        <li><a href="/?action=add_item_category_form">Add a new item category</a></li>
    </ul>
    <?php
    } else {
    ?>
    <h3>Getting started</h3>
    <p>The Foxy Hypermedia API uses OAuth 2.0 to authenticate access, so to make use of this example set up, you first need to create an OAuth Integration client. There are two ways you can go about this:</p>

    <h4>Quick start</h4>
    <p>If you want to jump in and play with the API rather than stepping through creating a client, user and store manually, you can quickly set up an integration for an existing FoxyCart store. Simply log in to your <a href="https://admin.foxycart.com" target="_blank">FoxyCart store administration</a>, navigate to the "Integrations" page and click the "Get Token" button. After you specify a name for your integration, you'll be presented with the <code>client_id</code>, <code>client_secret</code> and <code>refresh_token</code> you'll need to access that store using the API. To connect that newly created client into this example code, use the "Authenticate client" option in the below "OAuth Interactions".</p>

    <h4>Manual steps</h4>
    <p>You can also run through each step manually - creating a client through the API, and then manually creating a user and store as well. Follow the steps below for doing that.</p>
    <p>Alternatively, you can also just complete step 1 below to create an OAuth Integration client, and then use the "OAuth Authorization Code Grant" to connect this new client to your existing FoxyCart user or store.</p>
    <ol>
        <li><a href="/?action=register_client_form">Register your application</a> by creating an OAuth client</li>
        <li><a href="/?action=check_user_exists_form">Check if a Foxy user exists</a></li>
        <li><a href="/?action=create_user_form">Create a Foxy user</a></li>
        <li><a href="/?action=check_store_exists_form">Check if a Foxy store exists</a></li>
        <li><a href="/?action=create_store_form">Create a Foxy store</a></li>
    </ol>

    <h3>OAuth Interactions</h3>
    <ul>
        <li><a href="/?action=authenticate_client_form">Authenticate client</a><br/>If you have a <code>client_id</code>, <code>client_secret</code>, and OAuth <code>refresh_token</code> and want to connect using those credentials</li>
        <li><a href="/?action=client_credentials_grant">OAuth Client Credentials grant</a><br/>If you want to use the <code>client_id</code> and <code>client_secret</code> to get the <code>client_full_access</code> scoped refresh token for modifying your client</li>
        <li><a href="/?action=authorization_code_grant_form">OAuth Authorization Code grant</a><br/>If you have a <code>client_id</code> and <code>client_secret</code> and you want to get access to your store or user</li>
    </ul>
    <?php
    }
}

include 'includes/coupons.php';
include 'includes/item_categories.php';


if ($action == 'register_client') {
    ?>
    <h2>Register Client</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Clear FoxyClient credentials <code>$fc->clearCredentials();</code>.</li>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:create_client</code> link.</li>
        <li>Check for errors.</li>
        <li>Post data to create a client resource <code>$fc->post($create_client_uri,$data);</code>.</li>
        <li>Check for errors.</li>
        <li>Configure FoxyClient with the new OAuth token from the response.</li>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:client</code> link (authenticated with a <code>client_full_access</code> scope).</li>
        <li>Check for errors.</li>
        <li>Get the client <code>$fc->get($client_uri);</code> and save the <code>client_id</code> and <code>client_secret</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $fc->clearCredentials();
    $result = $fc->get();
    $errors = array_merge($errors,$fc->getErrors($result));
    $create_client_uri = $fc->getLink('fx:create_client');
    if ($create_client_uri == '') {
        $errors[] = 'Unable to obtain fx:create_client href';
    }
    $required_fields = array(
        'redirect_uri',
        'project_name',
        'company_name',
        'contact_name',
        'contact_email',
        'contact_phone'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
        'redirect_uri' => $_POST['redirect_uri'],
        'project_name' => $_POST['project_name'],
        'project_description' => $_POST['project_description'],
        'company_name' => $_POST['company_name'],
        'company_url' => $_POST['company_url'],
        'company_logo' => $_POST['company_logo'],
        'contact_name' => $_POST['contact_name'],
        'contact_email' => $_POST['contact_email'],
        'contact_phone' => $_POST['contact_phone'],
    );
    if (!count($errors)) {
        $result = $fc->post($create_client_uri,$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            ?>
            <h3 class="alert alert-success" role="alert"><?php print $result['message']; ?></h3>
            <pre><?php print_r($result['token']); ?></pre>
            <?php
            $_SESSION['access_token'] = $result['token']['access_token'];
            $_SESSION['refresh_token'] = $result['token']['refresh_token'];
            $_SESSION['access_token_expires'] = time() + $result['token']['expires_in'];
            $fc->setAccessToken($_SESSION['access_token']);
            $fc->setRefreshToken($_SESSION['refresh_token']);
            $fc->setAccessTokenExpires($_SESSION['access_token_expires']);
            $result = $fc->get();
            $errors = array_merge($errors,$fc->getErrors($result));
            $client_uri = $fc->getLink('fx:client');
            if ($client_uri == '') {
                $errors[] = 'Unable to obtain fx:client href';
            }
            if (!count($errors)) {
                $result = $fc->get($client_uri);
                $errors = array_merge($errors,$fc->getErrors($result));
                if (!count($errors)) {
                    $_SESSION['client_id'] = $result['client_id'];
                    $_SESSION['client_secret'] = $result['client_secret'];
                    $fc->setClientId($_SESSION['client_id']);
                    $fc->setClientSecret($_SESSION['client_secret']);
                    ?>
                    <h3 class="alert alert-success" role="alert">Client Registered</h3>
                    <h3>Result:</h3>
                    <pre><?php print_r($result); ?></pre>
                    <?php
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'register_client_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'register_client_form') {
    $redirect_uri = 'http' . (($_SERVER['SERVER_PORT'] == 443) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $redirect_uri .= "?action=authorization_code_grant";
    if (isset($_POST['redirect_uri'])) {
        $redirect_uri = htmlspecialchars($_POST['redirect_uri']);
    }
    ?>
    <h2><a href="https://tools.ietf.org/html/rfc6749#section-2">Register</a> your OAuth Client</h2>
    <form role="form" action="/?action=register_client" method="post" class="form-horizontal">
        <input type="hidden" name="act" value="create_client">
        <div class="form-group">
            <label for="project_name" class="col-sm-2 control-label">Project Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="project_name" name="project_name" maxlength="200" value="<?php echo isset($_POST['project_name']) ? htmlspecialchars($_POST['project_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="project_description" class="col-sm-2 control-label">Project Description</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="project_description" name="project_description" maxlength="200" value="<?php echo isset($_POST['project_description']) ? htmlspecialchars($_POST['project_description']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="company_name" class="col-sm-2 control-label">Company Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_name" name="company_name" maxlength="200" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="company_url" class="col-sm-2 control-label">Company URL</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_url" name="company_url" maxlength="200" value="<?php echo isset($_POST['company_url']) ? htmlspecialchars($_POST['company_url']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="company_logo" class="col-sm-2 control-label">Company Logo</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_logo" name="company_logo" maxlength="200" value="<?php echo isset($_POST['company_logo']) ? htmlspecialchars($_POST['company_logo']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_name" class="col-sm-2 control-label">Contact Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="contact_name" name="contact_name" maxlength="200" value="<?php echo isset($_POST['contact_name']) ? htmlspecialchars($_POST['contact_name']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_email" class="col-sm-2 control-label">Contact Email<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="email" class="form-control" id="contact_email" name="contact_email" maxlength="200" value="<?php echo isset($_POST['contact_email']) ? htmlspecialchars($_POST['contact_email']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_phone" class="col-sm-2 control-label">Contact Phone<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="contact_phone" name="contact_phone" maxlength="200" value="<?php echo isset($_POST['contact_phone']) ? htmlspecialchars($_POST['contact_phone']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="redirect_uri" class="col-sm-2 control-label">Redirect URI<span class="text-danger">*</span></label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="redirect_uri" name="redirect_uri" maxlength="200" value="<?php echo $redirect_uri; ?>">
                <small class="muted">Put your application's OAuth Code Grant endpoint here.</small>
            </div>
        </div>

        <div class="form-group">
            <label for="javascript_origin_uri" class="col-sm-2 control-label">Javascript Origin URI</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="javascript_origin_uri" name="javascript_origin_uri" maxlength="200" value="<?php echo isset($_POST['javascript_origin_uri']) ? htmlspecialchars($_POST['javascript_origin_uri']) : ""; ?>">
                <small class="muted">This is used by public OAuth clients (like a mobile browser only app where you can't secure the credentials).</small>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Create Client</button>
    </form>

<?php
}

if ($action == 'authenticate_client') {
    ?>
    <h2>Authenticate Client</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Update FoxyClient credentials <code>$fc->updateFromConfig($data);</code>.</li>
        <li>Get the homepage <code>$fc->get();</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $required_fields = array(
        'client_id',
        'client_secret'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
        'access_token' => $_POST['access_token'],
        'refresh_token' => $_POST['refresh_token'],
        'access_token_expires' => $_POST['access_token_expires'],
        'client_id' => $_POST['client_id'],
        'client_secret' => $_POST['client_secret'],
    );
    if (!count($errors)) {
        $fc->updateFromConfig($data);
        $result = $fc->get();
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            $_SESSION['client_id'] = $data['client_id'];
            $_SESSION['client_secret'] = $data['client_secret'];
            $_SESSION['access_token'] = $data['access_token'];
            $_SESSION['refresh_token'] = $data['refresh_token'];
            $_SESSION['access_token_expires'] = $data['access_token_expires'];
            ?>
            <h3 class="alert alert-success" role="alert">Client Authenticated</h3>
            <h3>Result:</h3>
            <pre><?php print_r($result); ?></pre>
            <?php
            print '<br /><a href="/?action=check_user_exists_form">Check if User Exists</a>';
            print '<br /><a href="/?action=create_user_form">Create User</a> <span class="muted">(client_full_access scope only)</span>';
            print '<br /><a href="/?action=check_store_exists_form">Check if Store Exists</a>';
            print '<br /><a href="/?action=create_store_form">Create Store</a> <span class="muted">(user_full_access scope only)</span>';
        }
    }
    if (count($errors)) {
        $action = 'authenticate_client_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'authenticate_client_form') {
    ?>
    <h2>Authenticate your OAuth Client</h2>
    <form role="form" action="/?action=authenticate_client" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="client_id" class="col-sm-2 control-label">Client ID<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="client_id" name="client_id" maxlength="200" value="<?php echo isset($_POST['client_id']) ? htmlspecialchars($_POST['client_id']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="client_secret" class="col-sm-2 control-label">Client Secret<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="client_secret" name="client_secret" maxlength="200" value="<?php echo isset($_POST['client_secret']) ? htmlspecialchars($_POST['client_secret']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="access_token" class="col-sm-2 control-label">Access Token</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="access_token" name="access_token" maxlength="200" value="<?php echo isset($_POST['access_token']) ? htmlspecialchars($_POST['access_token']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="access_token" class="col-sm-2 control-label">Refresh Token</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="access_token" name="refresh_token" maxlength="200" value="<?php echo isset($_POST['refresh_token']) ? htmlspecialchars($_POST['refresh_token']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="access_token_expires" class="col-sm-2 control-label">Token Expires</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="access_token_expires" name="access_token_expires" maxlength="200" value="<?php echo isset($_POST['access_token_expires']) ? htmlspecialchars($_POST['access_token_expires']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Authenticate Client</button>
    </form>
    <?php
}

if ($action == 'authorization_code_grant_form') {
    ?>
    <h2>Authorize Your Application</h2>
    <form role="form" action="<?php print $fc->getAuthorizationEndpoint(); ?>" method="GET" class="form-horizontal">
        <input type="hidden" name="state" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="response_type" value="code" />
        <input type="hidden" name="client_id" value="<?php print $fc->getClientId(); ?>" />
        <div class="form-group">
            <label for="client_id" class="col-sm-2 control-label">Scope<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <select name="scope" class="form-control">
                    <option value="store_full_access">store_full_access</option>
                    <option value="user_full_access">user_full_access</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Authorize Application</button>
    </form>
    <?php
}

if ($action == 'authorization_code_grant') {
    ?>
    <h2>OAuth Authorization Code grant</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Request an access_token using the Authorization Code <code>$fc->getAccessTokenFromAuthorizationCode($code);</code>.</li>
        <li>Check for errors.</li>
        <li>Update locally stored <code>access_token</code> and <code>refresh_token</code>.</li>
    </ol>
    <?php
    $errors = array();
    if (!array_key_exists('code', $_GET)) {
        $errors[] = 'Missing code from Authorization endpoint';
    }
    if (!count($errors)) {
        $result = $fc->getAccessTokenFromAuthorizationCode($_GET['code']);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            $_SESSION['access_token'] = $result['access_token'];
            $_SESSION['access_token_expires'] = time() + $result['expires_in'];
            $_SESSION['refresh_token'] = $result['refresh_token'];
            ?>
            <h3 class="alert alert-success" role="alert">Access Token Obtained</h3>
            <h3>Result:</h3>
            <pre><?php print_r($result); ?></pre>
            <?php
        }
    }
    if (count($errors)) {
        $action = '';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'client_credentials_grant') {
    ?>
    <h2>OAuth Client Credentials grant</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Request an access_token via Client Credentials grant <code>$fc->getAccessTokenFromClientCredentials();</code>.</li>
        <li>Check for errors.</li>
        <li>Update locally stored <code>access_token</code> and <code>refresh_token</code>.</li>
    </ol>
    <?php
    $errors = array();
    $result = $fc->getAccessTokenFromClientCredentials();
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        $_SESSION['access_token'] = $result['access_token'];
        $_SESSION['access_token_expires'] = time() + $result['expires_in'];
        ?>
        <h3 class="alert alert-success" role="alert">Access Token Obtained</h3>
        <h3>Result:</h3>
        <pre><?php print_r($result); ?></pre>
        <?php
    }
    if (count($errors)) {
        $action = '';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'check_user_exists') {
    ?>
    <h2>Check User Exists</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:reporting</code> link.</li>
        <li>Check for errors.</li>
        <li>Go to the reporting homepage <code>$fc->get($reporting_uri);</code> so we can get the <code>fx:reporting_email_exists</code> link.</li>
        <li>Check for errors.</li>
        <li>Check if the email exists as a user <code>$fc->get($email_exists_uri, $data);</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $required_fields = array(
        'email'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
        'email' => $_POST['email'],
    );
    if (!count($errors)) {
        $result = $fc->get();
        $errors = array_merge($errors,$fc->getErrors($result));
        $reporting_uri = $fc->getLink('fx:reporting');
        if ($reporting_uri == '') {
            $errors[] = 'Unable to obtain fx:reporting href';
        }
        if (!count($errors)) {
            $result = $fc->get($reporting_uri);
            $errors = array_merge($errors,$fc->getErrors($result));
            $email_exists_uri = $fc->getLink('fx:reporting_email_exists');
            if ($email_exists_uri == '') {
                $errors[] = 'Unable to obtain fx:reporting_email_exists href';
            }
            if (!count($errors)) {
                $result = $fc->get($email_exists_uri, $data);
                $errors = array_merge($errors,$fc->getErrors($result));
                if (!count($errors)) {
                    ?>
                    <h3 class="alert alert-success" role="alert">User Exists</h3>
                    <h3>Result:</h3>
                    <pre><?php print_r($result); ?></pre>
                    <?php
                    print '<br /><a href="/?action=create_user_form">Create User</a>';
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'check_user_exists_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'check_user_exists_form') {
    ?>
    <h2>Check User Exists</h2>
    <form role="form" action="/?action=check_user_exists" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">User Email Address<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="email" class="form-control" id="email" name="email" maxlength="200" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Check User</button>
    </form>
<?php
}


if ($action == 'create_user') {
    ?>
    <h2>Create User</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:create_user</code> link (authenticated with a <code>client_full_access</code> scope).</li>
        <li>Check for errors.</li>
        <li>Post data to create a user resource <code>$fc->post($create_user_uri, $data);</code>.</li>
        <li>Check for errors.</li>
        <li>Configure FoxyClient with the new OAuth token from the response.</li>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:user</code> link (authenticated with a <code>user_full_accesss</code> scope).</li>
        <li>Check for errors.</li>
        <li>Get the user <code>$fc->get($user_uri);</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $required_fields = array(
        'first_name',
        'last_name',
        'email',
        'phone'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'is_programmer' => isset($_POST['is_programmer']),
        'is_front_end_developer' => isset($_POST['is_front_end_developer']),
        'is_designer' => isset($_POST['is_designer']),
        'is_merchant' => isset($_POST['is_merchant']),
    );
    if (!count($errors)) {
        $result = $fc->get();
        $errors = array_merge($errors,$fc->getErrors($result));
        $create_user_uri = $fc->getLink('fx:create_user');
        if ($create_user_uri == '') {
            $errors[] = 'Unable to obtain fx:create_user href';
        }
        if (!count($errors)) {
            $result = $fc->post($create_user_uri, $data);
            $errors = array_merge($errors,$fc->getErrors($result));
            if (!count($errors)) {
                ?>
                <h3 class="alert alert-success" role="alert"><?php print $result['message']; ?></h3>
                <pre><?php print_r($result['token']); ?></pre>
                <?php
                $_SESSION['access_token'] = $result['token']['access_token'];
                $_SESSION['refresh_token'] = $result['token']['refresh_token'];
                $_SESSION['access_token_expires'] = time() + $result['token']['expires_in'];
                $fc->setAccessToken($_SESSION['access_token']);
                $fc->setRefreshToken($_SESSION['refresh_token']);
                $fc->setAccessTokenExpires($_SESSION['access_token_expires']);
                $result = $fc->get();
                $errors = array_merge($errors,$fc->getErrors($result));
                $user_uri = $fc->getLink('fx:user');
                if ($user_uri == '') {
                    $errors[] = 'Unable to obtain fx:user href';
                }
                if (!count($errors)) {
                    $result = $fc->get($user_uri);
                    $errors = array_merge($errors,$fc->getErrors($result));
                    if (!count($errors)) {
                        ?>
                        <h3>Result:</h3>
                        <pre><?php print_r($result); ?></pre>
                        <?php
                        print '<br /><a href="/?action=check_store_exists">Check if Store Exists</a>';
                    }
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'create_user_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'create_user_form') {
    ?>
    <h2>Create User</h2>
    <form role="form" action="/?action=create_user" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="first_name" class="col-sm-2 control-label">First Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="first_name" name="first_name" maxlength="200" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="last_name" class="col-sm-2 control-label">Last Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="last_name" name="last_name" maxlength="200" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">Email<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="email" name="email" maxlength="200" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="phone" class="col-sm-2 control-label">Phone<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="phone" name="phone" maxlength="200" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="is_programmer" class="col-sm-2 control-label">is_programmer</label>
            <div class="col-sm-3">
                <?php $checked = (isset($_POST['is_programmer']) ? ' checked' : ''); ?>
                <input type="checkbox"<?php print $checked; ?> class="form-control" id="is_programmer" name="is_programmer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_front_end_developer" class="col-sm-2 control-label">is_front_end_developer</label>
            <div class="col-sm-3">
                <?php $checked = (isset($_POST['is_front_end_developer']) ? ' checked' : ''); ?>
                <input type="checkbox"<?php print $checked; ?> class="form-control" id="is_front_end_developer" name="is_front_end_developer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_designer" class="col-sm-2 control-label">is_designer</label>
            <div class="col-sm-3">
                <?php $checked = (isset($_POST['is_designer']) ? ' checked' : ''); ?>
                <input type="checkbox"<?php print $checked; ?> class="form-control" id="is_designer" name="is_designer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_merchant" class="col-sm-2 control-label">is_merchant</label>
            <div class="col-sm-3">
                <?php $checked = (isset($_POST['is_merchant']) ? ' checked' : ''); ?>
                <input type="checkbox"<?php print $checked; ?> class="form-control" id="is_merchant" name="is_merchant">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
<?php
}


if ($action == 'check_store_exists') {
    ?>
    <h3>Check Store Exists</h3>
    <h3>Code Steps:</h3>
    <ol>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:reporting</code> link.</li>
        <li>Check for errors.</li>
        <li>Go to the reporting homepage <code>$fc->get($reporting_uri);</code> so we can get the <code>fx:reporting_store_domain_exists</code> link.</li>
        <li>Check for errors.</li>
        <li>Check if the store_domain exists as a store <code>$fc->get($store_exists_uri, $data);</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $required_fields = array(
        'store_domain'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
        'store_domain' => $_POST['store_domain'],
    );
    if (!count($errors)) {
        $result = $fc->get();
        $errors = array_merge($errors,$fc->getErrors($result));
        $reporting_uri = $fc->getLink('fx:reporting');
        if ($reporting_uri == '') {
            $errors[] = 'Unable to obtain fx:reporting href';
        }
        if (!count($errors)) {
            $result = $fc->get($reporting_uri);
            $errors = array_merge($errors,$fc->getErrors($result));
            $store_exists_uri = $fc->getLink('fx:reporting_store_domain_exists');
            if ($store_exists_uri == '') {
                $errors[] = 'Unable to obtain fx:reporting_store_domain_exists href';
            }
            if (!count($errors)) {
                $result = $fc->get($store_exists_uri, $data);
                $errors = array_merge($errors,$fc->getErrors($result));
                if (!count($errors)) {
                    ?>
                    <h3 class="alert alert-success" role="alert">Store Exists</h3>
                    <h3>Result:</h3>
                    <pre><?php print_r($result); ?></pre>
                    <?php
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'check_store_exists_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
    print '<br /><a href="/?action=create_store_form">Create Store</a>';
}


if ($action == 'check_store_exists_form') {
    ?>
    <h2>Check Store Exists</h2>
    <form role="form" action="/?action=check_store_exists" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="project_name" class="col-sm-2 control-label">Foxy Store Domain<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_domain" name="store_domain" maxlength="200" value="<?php echo isset($_POST['store_domain']) ? htmlspecialchars($_POST['store_domain']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Check Store</button>
    </form>
<?php
}

if ($action == 'create_store') {
    ?>
    <h2>Create Store</h2>
    <h3>Code Steps:</h3>
    <ol>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:stores</code> link (authenticated with a <code>user_full_access</code> scope).</li>
        <li>Check for errors.</li>
        <li>Post data to create a store resource <code>$fc->post($stores_uri, $data);</code>.</li>
        <li>Check for errors.</li>
        <li>Configure FoxyClient with the new OAuth token from the response.</li>
        <li>Get the homepage <code>$fc->get();</code> so we can get the <code>fx:store</code> link  (authenticated with a <code>store_full_accesss</code> scope).</li>
        <li>Check for errors.</li>
        <li>Get the store <code>$fc->get($store_uri);</code>.</li>
        <li>Check for errors.</li>
    </ol>
    <?php
    $errors = array();
    $required_fields = array(
        'store_name',
        'store_domain',
        'store_url',
        'store_email',
        'store_postal_code',
        'store_country',
        'store_state'
    );
    foreach($required_fields as $required_field) {
        if ($_POST[$required_field] == '') {
            $errors[] = $required_field . ' can not be blank';
        }
    }
    $data = array(
            'store_name' => $_POST['store_name'],
            'store_domain' => $_POST['store_domain'],
            'store_url' => $_POST['store_url'],
            'store_email' => $_POST['store_email'],
            'store_postal_code' => $_POST['store_postal_code'],
            'store_country' => $_POST['store_country'],
            'store_state' => $_POST['store_state']
    );
    if (!count($errors)) {
        $result = $fc->get();
        $errors = array_merge($errors,$fc->getErrors($result));
        $stores_uri = $fc->getLink('fx:stores');
        if ($stores_uri == '') {
            $errors[] = 'Unable to obtain fx:stores href';
        }
        if (!count($errors)) {
            $result = $fc->post($stores_uri, $data);
            $errors = array_merge($errors,$fc->getErrors($result));
            if (!count($errors)) {
                ?>
                <h3 class="alert alert-success" role="alert"><?php print $result['message']; ?></h3>
                <pre><?php print_r($result['token']); ?></pre>
                <?php
                $_SESSION['access_token'] = $result['token']['access_token'];
                $_SESSION['refresh_token'] = $result['token']['refresh_token'];
                $_SESSION['access_token_expires'] = time() + $result['token']['expires_in'];
                $fc->setAccessToken($_SESSION['access_token']);
                $fc->setRefreshToken($_SESSION['refresh_token']);
                $fc->setAccessTokenExpires($_SESSION['access_token_expires']);
                $result = $fc->get();
                $errors = array_merge($errors,$fc->getErrors($result));
                $store_uri = $fc->getLink('fx:store');
                if ($store_uri == '') {
                    $errors[] = 'Unable to obtain fx:store href';
                }
                if (!count($errors)) {
                    $result = $fc->get($store_uri);
                    $errors = array_merge($errors,$fc->getErrors($result));
                    if (!count($errors)) {
                        ?>
                        <h3>Result:</h3>
                        <pre><?php print_r($result); ?></pre>
                        <?php
                        print '<br /><a href="/?action=">Home</a>';
                    }
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'create_store_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'create_store_form') {
    ?>
    <h2>Create Store</h2>
    <form role="form" action="/?action=create_store" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="store_name" class="col-sm-2 control-label">Store Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_name" name="store_name" maxlength="200" value="<?php echo isset($_POST['store_name']) ? htmlspecialchars($_POST['store_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_domain" class="col-sm-2 control-label">Store Domain<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_domain" name="store_domain" maxlength="200" value="<?php echo isset($_POST['store_domain']) ? htmlspecialchars($_POST['store_domain']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_url" class="col-sm-2 control-label">Store URL<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_url" name="store_url" maxlength="200" value="<?php echo isset($_POST['store_url']) ? htmlspecialchars($_POST['store_url']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_email" class="col-sm-2 control-label">Store Email<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_email" name="store_email" maxlength="200" value="<?php echo isset($_POST['store_email']) ? htmlspecialchars($_POST['store_email']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_postal_code" class="col-sm-2 control-label">Store Postal Code<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_postal_code" name="store_postal_code" maxlength="200" value="<?php echo isset($_POST['store_postal_code']) ? htmlspecialchars($_POST['store_postal_code']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_country" class="col-sm-2 control-label">Store Country (ISO 3166-1 alpha-2)<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_country" name="store_country" maxlength="200" value="<?php echo isset($_POST['store_country']) ? htmlspecialchars($_POST['store_country']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="store_state" class="col-sm-2 control-label">Store State (ISO 3166-2)<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_state" name="store_state" maxlength="200" value="<?php echo isset($_POST['store_state']) ? htmlspecialchars($_POST['store_state']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Create Store</button>
    </form>
<?php
}

if ($action == 'logout') {
    session_destroy();
    $fc->clearCredentials();
    print '<h2>You are Logged out</h2>';
    print '<br /><a href="/?action=">Home</a>';
}


// NOTE: This example uses the session, but you could also be using a database or some other persistance layer.
if (isset($_SESSION['access_token']) && $fc->getAccessToken() != $_SESSION['access_token']) {
    // This can happen after a token refresh.
    if ($fc->getAccessToken() != '') {
        $_SESSION['access_token'] = $fc->getAccessToken();
    }
}
if (isset($_SESSION['access_token_expires']) && $fc->getAccessTokenExpires() != $_SESSION['access_token_expires']) {
    // This can happen after a token refresh.
    if ($fc->getAccessTokenExpires() != '') {
        $_SESSION['access_token_expires'] = $fc->getAccessTokenExpires();
    }
}

if ($action != 'logout' && $fc->getClientId() != '') {
    print '<footer class="text-muted">Authenticated: ';
    print '<ul>';
    print '<li>client_id: ' . $fc->getClientId() . '</li>';
    print '<li>client_secret (select text to view): <span style="color:white">' . $fc->getClientSecret() . '</span></li>';
    print '<li>access_token: ' . $fc->getAccessToken() . '</li>';
    print '<li>refresh_token (select text to view): <span style="color:white">' . $fc->getRefreshToken() . '</span></li>';
    if ($fc->getAccessTokenExpires() != '') {
        print '<li>access_token_expires: ' . $fc->getAccessTokenExpires() . '</li>';
        print '<li>now: ' . time() . '</li>';
        print '<li>next token refresh: ' . ($fc->getAccessTokenExpires() - time()) . '</li>';
    }
    print '</ul>';
    print '</footer>';
}

?>
</div>
</body>
</html>
