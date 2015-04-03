<?php
require __DIR__ . '/bootstrap.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

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
    </style>
  </head>
  <body>

<nav class="navbar navbar-default">
  <div class="container">
    <a class="navbar-brand" href="/">Foxy hAPI Example</a>
    <ul class="nav navbar-nav">
      <li><a href="?action=">Home</a></li>
      <li><a href="?action=logout">Logout</a></li>
    </ul>
  </div>
</nav>
    <div class="container">
<?php

// update our session/client if needed.
// NOTE: This example uses the session, but you could also be using a database or some other persistance layer.
if (isset($_SESSION['access_token']) && $fc->getAccessToken() != $_SESSION['access_token']) {
    if ($fc->getAccessToken() == '') {
        $fc->setAccessToken($_SESSION['access_token']);
    } else {
        $_SESSION['access_token'] = $fc->getAccessToken();
    }
}
if (isset($_SESSION['refresh_token']) && $fc->getRefreshToken() != $_SESSION['refresh_token']) {
    if ($fc->getRefreshToken() == '') {
        $fc->setRefreshToken($_SESSION['refresh_token']);
    } else {
        $_SESSION['refresh_token'] = $fc->getRefreshToken();
    }
}
if (isset($_SESSION['client_id']) && $fc->getClientId() != $_SESSION['client_id']) {
    if ($fc->getClientId() == '') {
        $fc->setClientId($_SESSION['client_id']);
    } else {
        $_SESSION['client_id'] = $fc->getClientId();
    }
}
if (isset($_SESSION['client_secret']) && $fc->getClientSecret() != $_SESSION['client_secret']) {
    if ($fc->getClientSecret() == '') {
        $fc->setClientSecret($_SESSION['client_secret']);
    } else {
        $_SESSION['client_secret'] = $fc->getClientSecret();
    }
}
if (isset($_SESSION['token_expires']) && $fc->getAccessTokenExpires() != $_SESSION['token_expires']) {
    if ($fc->getAccessTokenExpires() == '') {
        $fc->setAccessTokenExpires($_SESSION['token_expires']);
    } else {
        $_SESSION['token_expires'] = $fc->getAccessTokenExpires();
    }
}

// BEGIN HERE
if ($action == '') {
?>
    <h1>Welcome to the Foxy Hypermedia API example!</h1>
    <p>
        If you haven't already, please check out the <a href="https://api-sandbox.foxycart.com/docs">Foxy hAPI documentation</a> to better understand the purpose of this library.
    </p>
    <p>
        This exmaple will walk through using FoxyClient.php to:
        <ol>
            <li><a href="/?action=register_client_form">Register your application</a> by creating an OAuth client</li>
            <li><a href="/?action=authenticate_client_form">Authenticate</a> client</li>
            <li><a href="/?action=check_user_exists_form">Check if a Foxy user exists</a></li>
            <li><a href="/?action=create_user_form">Create a Foxy user</a></li>
            <li><a href="/?action=check_store_exists_form">Check if a Foxy store exists</a></li>
            <li>Create a Foxy store</li>
            <li>OAuth Authorization Code grant</li>
        </ol>
    </p>
<?php
}

if ($action == 'register_client_form') {
    ?>
    <h2><a href="https://tools.ietf.org/html/rfc6749#section-2">Register</a> your OAuth Client</h2>
    <form role="form" action="/?action=register_client" method="post" class="form-horizontal">
        <input type="hidden" name="act" value="create_client">
        <div class="form-group">
            <label for="project_name" class="col-sm-2 control-label">Project Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="project_name" name="project_name" maxlength="50" value="<?php echo isset($_POST['project_name']) ? htmlspecialchars($_POST['project_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="project_description" class="col-sm-2 control-label">Project Description</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="project_description" name="project_description" maxlength="50" value="<?php echo isset($_POST['project_description']) ? htmlspecialchars($_POST['project_description']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="company_name" class="col-sm-2 control-label">Company Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_name" name="company_name" maxlength="50" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="company_url" class="col-sm-2 control-label">Company URL</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_url" name="company_url" maxlength="50" value="<?php echo isset($_POST['company_url']) ? htmlspecialchars($_POST['company_url']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="company_logo" class="col-sm-2 control-label">Company Logo</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="company_logo" name="company_logo" maxlength="50" value="<?php echo isset($_POST['company_logo']) ? htmlspecialchars($_POST['company_logo']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_name" class="col-sm-2 control-label">Contact Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="contact_name" name="contact_name" maxlength="50" value="<?php echo isset($_POST['contact_name']) ? htmlspecialchars($_POST['contact_name']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_email" class="col-sm-2 control-label">Contact Email<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="email" class="form-control" id="contact_email" name="contact_email" maxlength="50" value="<?php echo isset($_POST['contact_email']) ? htmlspecialchars($_POST['contact_email']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="contact_phone" class="col-sm-2 control-label">Contact Phone<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="contact_phone" name="contact_phone" maxlength="50" value="<?php echo isset($_POST['contact_phone']) ? htmlspecialchars($_POST['contact_phone']) : ""; ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="redirect_uri" class="col-sm-2 control-label">Redirect URI<span class="text-danger">*</span></label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="redirect_uri" name="redirect_uri" maxlength="50" value="<?php echo isset($_POST['redirect_uri']) ? htmlspecialchars($_POST['redirect_uri']) : ""; ?>">
                <small class="muted">This should be the current page's URL</small>
            </div>
        </div>

        <div class="form-group">
            <label for="javascript_origin_uri" class="col-sm-2 control-label">Javascript Origin URI</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="javascript_origin_uri" name="javascript_origin_uri" maxlength="50" value="<?php echo isset($_POST['javascript_origin_uri']) ? htmlspecialchars($_POST['javascript_origin_uri']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')?>" />
        <button type="submit" class="btn btn-primary">Create Client</button>
    </form>

<?php
}

if ($action == 'register_client') {
    ?>
    <h2>Client Registered</h2>
    <h3>Code:</h3>
    <pre>
    $fc->get();
    $create_client_uri = $fc->getLink('fx:create_client');
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
    $result = $fc->post($create_client_uri,$data);
    print_r($result);
    $fc->setAccessToken($result['access_token']);
    $fc->setRefreshToken($result['refresh_token']);
    $fc->setAccessTokenExpires($result['token_expires']);
    $fc->get();
    $client_uri = $fc->getLink('fx:client');
    $result = $fc->get($client_uri);
    print_r($result);
    </pre>
    <h3>Result:</h3>
    <?php
    $fc->setClientId($data['client_id']);
    $fc->setClientSecret($data['client_secret']);
    $fc->setAccessToken($data['access_token']);
    $fc->setRefreshToken($data['refresh_token']);
    $fc->setAccessTokenExpires($data['token_expires']);

    $fc->get();
    $create_client_uri = $fc->getLink('fx:create_client');
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
    $result = $fc->post($create_client_uri,$data);
    if ($errors = $fc->checkForErrors($result)) {
        die("<pre>" . print_r($errors, 1) . "</pre>");
    }
    ?>
    <h1><?php print $result['message']; ?></h1>
    <pre><?php print_r($result['token']); ?></pre>
    <?php
    $_SESSION['access_token'] = $result['token']['access_token'];
    $_SESSION['refresh_token'] = $result['token']['refresh_token'];
    $_SESSION['token_expires'] = time() + $result['token']['expires_in'];

    $fc->setAccessToken($_SESSION['access_token']);
    $fc->setRefreshToken($_SESSION['refresh_token']);
    $fc->setAccessTokenExpires($_SESSION['token_expires']);
    $fc->get();
    $client_uri = $fc->getLink('fx:client');
    $result = $fc->get($client_uri);

    $_SESSION['client_id'] = $result['client_id'];
    $_SESSION['client_secret'] = $result['client_secret'];
    $fc->setClientId($_SESSION['client_id']);
    $fc->setClientSecret($_SESSION['client_secret']);
    ?>
    <pre><?php print_r($result); ?></pre>
    <?php
}

if ($action == 'authenticate_client_form') {
    ?>
    <h2>Authenticate your OAuth Client</h2>
    <form role="form" action="/?action=authenticate_client" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="access_token" class="col-sm-2 control-label">Access Token<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="access_token" name="access_token" maxlength="50" value="<?php echo isset($_POST['access_token']) ? htmlspecialchars($_POST['access_token']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="access_token" class="col-sm-2 control-label">Refresh Token<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="access_token" name="refresh_token" maxlength="50" value="<?php echo isset($_POST['refresh_token']) ? htmlspecialchars($_POST['refresh_token']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="token_expires" class="col-sm-2 control-label">Token Expires</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="token_expires" name="token_expires" maxlength="50" value="<?php echo isset($_POST['token_expires']) ? htmlspecialchars($_POST['token_expires']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="client_id" class="col-sm-2 control-label">Client ID<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="client_id" name="client_id" maxlength="50" value="<?php echo isset($_POST['client_id']) ? htmlspecialchars($_POST['client_id']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="client_secret" class="col-sm-2 control-label">Client Secret<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="client_secret" name="client_secret" maxlength="50" value="<?php echo isset($_POST['client_secret']) ? htmlspecialchars($_POST['client_secret']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')?>" />
        <button type="submit" class="btn btn-primary">Authenticate Client</button>
    </form>
<?php
}

if ($action == 'authenticate_client') {
    ?>
    <h2>Client Authenticated</h2>
    <h3>Code:</h3>
    <pre>
    $data = array(
        'access_token' => $_POST['access_token'],
        'refresh_token' => $_POST['refresh_token'],
        'token_expires' => $_POST['token_expires'],
        'client_id' => $_POST['client_id'],
        'client_secret' => $_POST['client_secret'],
    );
    $fc->setClientId($data['client_id']);
    $fc->setClientSecret($data['client_secret']);
    $fc->setAccessToken($data['access_token']);
    $fc->setRefreshToken($data['refresh_token']);
    $fc->setAccessTokenExpires($data['token_expires']);
    $result = $fc->get();
    print_r($result);
    </pre>
    <h3>Result:</h3>
    <?php
    $data = array(
        'access_token' => $_POST['access_token'],
        'refresh_token' => $_POST['refresh_token'],
        'token_expires' => $_POST['token_expires'],
        'client_id' => $_POST['client_id'],
        'client_secret' => $_POST['client_secret'],
    );
    $_SESSION['client_id'] = $data['client_id'];
    $_SESSION['client_secret'] = $data['client_secret'];
    $_SESSION['access_token'] = $data['access_token'];
    $_SESSION['refresh_token'] = $data['refresh_token'];
    $_SESSION['token_expires'] = $data['token_expires'];
    $fc->setClientId($_SESSION['client_id']);
    $fc->setClientSecret($_SESSION['client_secret']);
    $fc->setAccessToken($_SESSION['access_token']);
    $fc->setRefreshToken($_SESSION['refresh_token']);
    $fc->setAccessTokenExpires($_SESSION['token_expires']);
    $result = $fc->get();
    ?>
    <pre><?php print_r($result); ?></pre>
    <?php
}

if ($action == 'check_user_exists_form') {
    ?>
    <h2>Check User Exists</h2>
    <form role="form" action="/?action=check_user_exists" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">User Email Address<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="email" name="email" maxlength="50" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')?>" />
        <button type="submit" class="btn btn-primary">Check User</button>
    </form>
<?php
}

if ($action == 'check_user_exists') {
    ?>
    <h2>User Exists Results</h2>
    <h3>Code:</h3>
    <pre>
    $data = array(
        'email' => $_POST['email'],
    );
    $result = $fc->get();
    $reporting_uri = $fc->getLink('fx:reporting');
    $result = $fc->get($reporting_uri);
    $email_exists_uri = $fc->getLink('fx:reporting_email_exists');
    $result = $fc->get($email_exists_uri, $data);
    print_r($result);
    </pre>
    <h3>Result:</h3>
    <?php
    $data = array(
        'email' => $_POST['email'],
    );
    $result = $fc->get();
    $reporting_uri = $fc->getLink('fx:reporting');
    $result = $fc->get($reporting_uri);
    $email_exists_uri = $fc->getLink('fx:reporting_email_exists');
    $result = $fc->get($email_exists_uri, $data);
    ?>
    <pre><?php print_r($result); ?></pre>
    <?php
}


if ($action == 'create_user_form') {
    ?>
    <h2>Create User</h2>
    <form role="form" action="/?action=create_user" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="first_name" class="col-sm-2 control-label">First Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="first_name" name="first_name" maxlength="50" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="last_name" class="col-sm-2 control-label">Last Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="last_name" name="last_name" maxlength="50" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="col-sm-2 control-label">Email<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="email" name="email" maxlength="50" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="phone" class="col-sm-2 control-label">Phone<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="phone" name="phone" maxlength="50" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="is_programmer" class="col-sm-2 control-label">is_programmer</label>
            <div class="col-sm-3">
                <input type="checkbox" class="form-control" id="is_programmer" name="is_programmer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_front_end_developer" class="col-sm-2 control-label">is_front_end_developer</label>
            <div class="col-sm-3">
                <input type="checkbox" class="form-control" id="is_front_end_developer" name="is_front_end_developer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_designer" class="col-sm-2 control-label">is_designer</label>
            <div class="col-sm-3">
                <input type="checkbox" class="form-control" id="is_designer" name="is_designer">
            </div>
        </div>
        <div class="form-group">
            <label for="is_merchant" class="col-sm-2 control-label">is_merchant</label>
            <div class="col-sm-3">
                <input type="checkbox" class="form-control" id="is_merchant" name="is_merchant">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')?>" />
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
<?php
}

if ($action == 'create_user') {
    ?>
    <h2>Create User</h2>
    <h3>Code:</h3>
    <pre>
    print_r($result);
    </pre>
    <h3>Result:</h3>
    <?php
    $data = array(
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'is_programmer' => $_POST['is_programmer'],
        'is_front_end_developer' => isset($_POST['is_front_end_developer']),
        'is_designer' => isset($_POST['is_designer']),
        'is_merchant' => isset($_POST['is_merchant']),
    );
    $result = $fc->get();
    $create_user_uri = $fc->getLink('fx:create_user');
    $result = $fc->get($create_user_uri, $data);
    ?>
    <pre><?php print_r($result); ?></pre>
    <?php
}

if ($action == 'check_store_exists_form') {
    ?>
    <h2>Check Store Exists</h2>
    <form role="form" action="/?action=check_store_exists" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="project_name" class="col-sm-2 control-label">Foxy Store Domain<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="store_domain" name="store_domain" maxlength="50" value="<?php echo isset($_POST['store_domain']) ? htmlspecialchars($_POST['store_domain']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')?>" />
        <button type="submit" class="btn btn-primary">Check Store</button>
    </form>
<?php
}

if ($action == 'check_store_exists') {
    ?>
    <h2>Store Exists Results</h2>
    <h3>Code:</h3>
    <pre>
    $data = array(
        'store_domain' => $_POST['store_domain'],
    );
    $result = $fc->get();
    $reporting_uri = $fc->getLink('fx:reporting');
    $result = $fc->get($reporting_uri);
    $store_exists_uri = $fc->getLink('fx:reporting_store_domain_exists');
    $result = $fc->get($store_exists_uri, $data);
    print_r($result);
    </pre>
    <h3>Result:</h3>
    <?php
    $data = array(
        'store_domain' => $_POST['store_domain'],
    );
    $result = $fc->get();
    $reporting_uri = $fc->getLink('fx:reporting');
    $result = $fc->get($reporting_uri);
    $store_exists_uri = $fc->getLink('fx:reporting_store_domain_exists');
    $result = $fc->get($store_exists_uri, $data);
    ?>
    <pre><?php print_r($result); ?></pre>
    <?php
}

if ($action == 'logout') {
    session_destroy();
    $fc->setClientId('');
    $fc->setClientSecret('');
    $fc->setAccessToken('');
    $fc->setRefreshToken('');
    $fc->setAccessTokenExpires('');
    print '<h1>You are Logged out</h1>';
}

if ($action != 'logout' && $fc->getAccessToken() != '') {
    print '<footer class="text-muted">Authenticated: ';
    print '<ul>';
    print '<li>client_id: ' . $fc->getClientId() . '</li>';
    print '<li>client_secret: (view source) <!--' . $fc->getClientSecret() . '--></li>';
    print '<li>access_token: ' . $fc->getAccessToken() . '</li>';
    print '<li>refresh_token: ' . $fc->getRefreshToken() . '</li>';
    if ($fc->getAccessTokenExpires() != '') {
        print '<li>token_expires: ' . $fc->getAccessTokenExpires() . '</li>';
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