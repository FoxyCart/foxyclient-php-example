<?php
if ($action == 'delete_coupon') {
    $errors = array();
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->delete($_REQUEST['resource_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        print '<h3 class="alert alert-success" role="alert">Coupon Deleted</h3>';
        $action = 'view_coupons';
    }
    if (count($errors)) {
        $action = 'edit_coupon_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'delete_coupon_form') {
    $errors = array();
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
            ?>
            <p>Are you sure you want to delete the <code><?php print $_REQUEST['resource_name']; ?></code> coupon?
            <form role="form" action="/?action=delete_coupon" method="post" class="form-horizontal">
            <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="submit" name="submit" class="btn btn-danger" value="Yes, Delete It" />
            </form>
            <?php
    }
    if (count($errors)) {
        $action = 'view_coupon';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'add_coupon') {
    $errors = array();
    $data = array(
        'name' => $_POST['name'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'number_of_uses_allowed' => $_POST['number_of_uses_allowed'],
        'number_of_uses_allowed_per_customer' => $_POST['number_of_uses_allowed_per_customer'],
        'number_of_uses_allowed_per_code' => $_POST['number_of_uses_allowed_per_code'],
        'product_code_restrictions' => $_POST['product_code_restrictions'],
        'coupon_discount_type' => $_POST['coupon_discount_type'],
        'coupon_discount_details' => $_POST['coupon_discount_details'],
        'combinable' => $_POST['combinable'],
        'multiple_codes_allowed' => $_POST['multiple_codes_allowed'],
        'exclude_category_discounts' => $_POST['exclude_category_discounts'],
        'exclude_line_item_discounts' => $_POST['exclude_line_item_discounts'],
        'is_taxable' => $_POST['is_taxable'],
    );
    $result = $fc->post($_SESSION['coupons_uri'],$data);
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        print '<div class="alert alert-success" role="alert">';
        print $result['message'];
        print '</div>';
        $_REQUEST['resource_uri'] = $result['_links']['self']['href'];
        $action = 'view_coupon';

        // add our item category restrictions
        $category_uris = array();
        foreach($_POST as $field => $value) {
            $prefix = strpos($field, 'category_uris_');
            if ($prefix !== false) {
                $category_uri = substr($field, strlen('category_uris_'));
                $category_uris[] = urldecode($category_uri);
            }
        }
        if (count($category_uris)) {
            // get the fx:coupon_item_categories href
            $result = $fc->get($_REQUEST['resource_uri']);
            $errors = array_merge($errors,$fc->getErrors($result));
            if (!count($errors)) {
                $coupon_item_categories_uri = $fc->getLink('fx:coupon_item_categories');
                if ($coupon_item_categories_uri == '') {
                    $errors[] = 'Unable to obtain fx:coupon_item_categories href';
                } else {
                    foreach($category_uris as $category_uri) {
                        $data = array(
                            'coupon_uri' => $_REQUEST['resource_uri'],
                            'item_category_uri' => $category_uri
                            );
                        $result = $fc->post($coupon_item_categories_uri, $data);
                        $errors = array_merge($errors,$fc->getErrors($result));
                    }
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'add_coupon_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'add_coupon_code') {
    $errors = array();
    if (!isset($_REQUEST['resource_creation_uri'])) {
        $errors[] = 'The required resource_creation_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $data = array(
            'code' => $_POST['code']
        );
        $result = $fc->post($_REQUEST['resource_creation_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            print '<h3 class="alert alert-success" role="alert">Coupon Code Created</h3>';
            $action = 'view_coupon';
        }
    }
    if (count($errors)) {
        $action = 'add_coupon_code_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'add_multiple_coupon_codes') {
    $errors = array();
    if (!isset($_REQUEST['resource_creation_uri'])) {
        $errors[] = 'The required resource_creation_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $codes = str_replace("\r", '', $_POST['codes']);
        $data = array(
            'coupon_codes' => explode("\n",$codes)
        );
        $result = $fc->post($_REQUEST['resource_creation_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            print '<h3 class="alert alert-success" role="alert">Coupon Codes Created</h3>';
            $action = 'view_coupon';
        }
    }
    if (count($errors)) {
        $action = 'add_coupon_code_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'generate_multiple_coupon_codes') {
    $errors = array();
    if (!isset($_REQUEST['generate_codes_uri'])) {
        $errors[] = 'The required generate_codes_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $data = array(
            'length' => $_POST['length'],
            'number_of_codes' => $_POST['number_of_codes'],
            'prefix' => $_POST['prefix'],
        );
        $result = $fc->post($_REQUEST['generate_codes_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            print '<h3 class="alert alert-success" role="alert">Coupon Codes Created</h3>';
            $action = 'view_coupon';
        }
    }
    if (count($errors)) {
        $action = 'add_coupon_code_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'add_coupon_code_form') {
    ?>
    <h2>Add Single Coupon Code</h2>
    <form role="form" action="/?action=add_coupon_code" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">Coupon Code<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="code" name="code" maxlength="200" value="<?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="resource_creation_uri" value="<?php print htmlspecialchars($_REQUEST['resource_creation_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="generate_codes_uri" value="<?php print htmlspecialchars($_REQUEST['generate_codes_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Add Coupon Code</button>
    </form>
    <hr />

    <h2>Add Multiple Coupon Codes</h2>
    <p>Add one code per line.</p>
    <form role="form" action="/?action=add_multiple_coupon_codes" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="codes" class="col-sm-2 control-label">Coupon Code<span class="text-danger">*</span></label>
            <div class="col-sm-3">
<textarea class="form-control" id="codes" name="codes" rows="10" cols="70">
<?php echo isset($_POST['codes']) ? htmlspecialchars($_POST['codes']) : ""; ?>
</textarea>
            </div>
        </div>
        <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="resource_creation_uri" value="<?php print htmlspecialchars($_REQUEST['resource_creation_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="generate_codes_uri" value="<?php print htmlspecialchars($_REQUEST['generate_codes_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Add Coupon Codes</button>
    </form>
    <hr />

    <h2>Automatically Generate Multiple Coupon Codes</h2>
    <form role="form" action="/?action=generate_multiple_coupon_codes" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="length" class="col-sm-2 control-label">Code Length</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="length" name="length" maxlength="200" value="<?php echo isset($_POST['length']) ? htmlspecialchars($_POST['length']) : "6"; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="number_of_codes" class="col-sm-2 control-label">Number of Codes</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="number_of_codes" name="number_of_codes" maxnumber_of_codes="200" value="<?php echo isset($_POST['number_of_codes']) ? htmlspecialchars($_POST['number_of_codes']) : "10"; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="prefix" class="col-sm-2 control-label">Prefix</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="prefix" name="prefix" maxprefix="200" value="<?php echo isset($_POST['prefix']) ? htmlspecialchars($_POST['prefix']) : ""; ?>">
            </div>
        </div>
        <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="resource_creation_uri" value="<?php print htmlspecialchars($_REQUEST['resource_creation_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="generate_codes_uri" value="<?php print htmlspecialchars($_REQUEST['generate_codes_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Add Coupon Codes</button>
    </form>
    <?php
}


if ($action == 'add_coupon_form') {
    // let's get the list of item categories
    $errors = array();
    $item_categories = array();
    $result = $fc->get($_SESSION['item_categories_uri']);
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        foreach($result['_embedded']['fx:item_categories'] as $item_category) {
            $item_categories[] = $item_category;
        }
    }
    ?>
    <h2>Add Coupon</h2>
    <form role="form" action="/?action=add_coupon" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Coupon Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="name" name="name" maxlength="200" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="start_date" class="col-sm-2 control-label">Start Date</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="start_date" name="start_date" maxlength="200" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="end_date" class="col-sm-2 control-label">End Date</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="end_date" name="end_date" maxlength="200" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="number_of_uses_allowed" class="col-sm-2 control-label">Number of Uses Allowed</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="number_of_uses_allowed" name="number_of_uses_allowed" maxlength="200" value="<?php echo isset($_POST['number_of_uses_allowed']) ? htmlspecialchars($_POST['number_of_uses_allowed']) : 0; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="number_of_uses_allowed_per_customer" class="col-sm-2 control-label">Number of Uses Allowed Per Customer</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="number_of_uses_allowed_per_customer" name="number_of_uses_allowed_per_customer" maxlength="200" value="<?php echo isset($_POST['number_of_uses_allowed_per_customer']) ? htmlspecialchars($_POST['number_of_uses_allowed_per_customer']) : 0; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="number_of_uses_allowed_per_code" class="col-sm-2 control-label">Number of Uses Allowed Per Code</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="number_of_uses_allowed_per_code" name="number_of_uses_allowed_per_code" maxlength="200" value="<?php echo isset($_POST['number_of_uses_allowed_per_code']) ? htmlspecialchars($_POST['number_of_uses_allowed_per_code']) : 0; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="product_code_restrictions" class="col-sm-2 control-label">Product Code Restrictions</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="product_code_restrictions" name="product_code_restrictions" maxlength="200" value="<?php echo isset($_POST['product_code_restrictions']) ? htmlspecialchars($_POST['product_code_restrictions']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="coupon_discount_type" class="col-sm-2 control-label">Coupon Discount Type</label>
            <div class="col-sm-3">
                <select name="coupon_discount_type">
                <?php $selected = (isset($_POST['coupon_discount_type']) && $_POST['coupon_discount_type'] == 'quantity_amount') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="quantity_amount">Amount Based on Quantity (quantity_amount)</option>
                <?php $selected = (isset($_POST['coupon_discount_type']) && $_POST['coupon_discount_type'] == 'quantity_percentage') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="quantity_percentage">Percentage Based on Quantity (quantity_percentage)</option>
                <?php $selected = (isset($_POST['coupon_discount_type']) && $_POST['coupon_discount_type'] == 'price_amount') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="price_amount">Amount Based on Price (price_amount)</option>
                <?php $selected = (isset($_POST['coupon_discount_type']) && $_POST['coupon_discount_type'] == 'price_percentage') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="price_percentage">Percentage Based on Price (price_percentage)</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="coupon_discount_details" class="col-sm-2 control-label">Coupon Discount Details<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="coupon_discount_details" name="coupon_discount_details" maxlength="200" value="<?php echo isset($_POST['coupon_discount_details']) ? htmlspecialchars($_POST['coupon_discount_details']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label for="combinable_yes" class="col-sm-2 control-label">Is Combinable?</label>
                <div class="col-sm-3">
                    <label class="radio-inline">
                        <?php $checked = (isset($_POST['combinable']) && $_POST['combinable'] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="combinable_true" name="combinable" value="true" /> Yes
                    </label>
                    <label class="radio-inline">
                        <?php $checked = (!isset($_POST['combinable']) || $_POST['combinable'] == 'false') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="combinable_false" name="combinable" value="false" /> No
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label for="multiple_codes_allowed_yes" class="col-sm-2 control-label">Multiple Codes Allowed?</label>
                <div class="col-sm-3">
                    <label class="radio-inline">
                        <?php $checked = (isset($_POST['multiple_codes_allowed']) && $_POST['multiple_codes_allowed'] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="multiple_codes_allowed_true" name="multiple_codes_allowed" value="true" /> Yes
                    </label>
                    <label class="radio-inline">
                        <?php $checked = (!isset($_POST['multiple_codes_allowed']) || $_POST['multiple_codes_allowed'] == 'false') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="multiple_codes_allowed_false" name="multiple_codes_allowed" value="false" /> No
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label for="exclude_category_discounts_yes" class="col-sm-2 control-label">Exclude Category Discounts?</label>
                <div class="col-sm-3">
                    <label class="radio-inline">
                        <?php $checked = (isset($_POST['exclude_category_discounts']) && $_POST['exclude_category_discounts'] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="exclude_category_discounts_true" name="exclude_category_discounts" value="true" /> Yes
                    </label>
                    <label class="radio-inline">
                        <?php $checked = (!isset($_POST['exclude_category_discounts']) || $_POST['exclude_category_discounts'] == 'false') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="exclude_category_discounts_false" name="exclude_category_discounts" value="false" /> No
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label for="exclude_line_item_discounts_yes" class="col-sm-2 control-label">Exclude Line Item Discounts?</label>
                <div class="col-sm-3">
                    <label class="radio-inline">
                        <?php $checked = (isset($_POST['exclude_line_item_discounts']) && $_POST['exclude_line_item_discounts'] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="exclude_line_item_discounts_true" name="exclude_line_item_discounts" value="true" /> Yes
                    </label>
                    <label class="radio-inline">
                        <?php $checked = (!isset($_POST['exclude_line_item_discounts']) || $_POST['exclude_line_item_discounts'] == 'false') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="exclude_line_item_discounts_false" name="exclude_line_item_discounts" value="false" /> No
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label for="is_taxable_yes" class="col-sm-2 control-label">Is Taxable?</label>
                <div class="col-sm-3">
                    <label class="radio-inline">
                        <?php $checked = (isset($_POST['is_taxable']) && $_POST['is_taxable'] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="is_taxable_true" name="is_taxable" value="true" /> Yes
                    </label>
                    <label class="radio-inline">
                        <?php $checked = (!isset($_POST['is_taxable']) || $_POST['is_taxable'] == 'false') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="radio" id="is_taxable_false" name="is_taxable" value="false" /> No
                    </label>
                </div>
            </div>
        </div>
        <h3>Restrict by Item Category</h3>
        <p>Leave everything unchecked to set no restrictions and make this coupon available to all categories.</p>
        <?php
        foreach($item_categories as $category) {
            $category_uri = urlencode($category['_links']['self']['href']);
            ?>
        <div class="form-group">
            <div class="checkbox">
                <div class="col-sm-3">
                    <label>
                        <?php $checked = (isset($_POST['category_uris_' . $category_uri]) && $_POST['category_uris_' . $category_uri] == 'true') ? ' checked="checked"' : ''; ?>
                        <input<?php print $checked; ?> type="checkbox" id="category_uris_<?php print $category_uri; ?>" name="category_uris_<?php print $category_uri; ?>" value="true" />
                        <?php print $category['code'] . ': ' . $category['name']; ?>
                    </label>
                </div>
            </div>
            </div>
            <?php
        }
        ?>
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Add Coupon</button>
    </form>
    <?php
}

if ($action == 'save_coupon') {
    $errors = array();
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $data = array(
            'name' => $_POST['name'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'number_of_uses_allowed' => $_POST['number_of_uses_allowed'],
            'number_of_uses_to_date' => $_POST['number_of_uses_to_date'],
            'number_of_uses_allowed_per_customer' => $_POST['number_of_uses_allowed_per_customer'],
            'number_of_uses_allowed_per_code' => $_POST['number_of_uses_allowed_per_code'],
            'product_code_restrictions' => $_POST['product_code_restrictions'],
            'coupon_discount_type' => $_POST['coupon_discount_type'],
            'coupon_discount_details' => $_POST['coupon_discount_details'],
            'combinable' => $_POST['combinable'],
            'multiple_codes_allowed' => $_POST['multiple_codes_allowed'],
            'exclude_category_discounts' => $_POST['exclude_category_discounts'],
            'exclude_line_item_discounts' => $_POST['exclude_line_item_discounts'],
            'is_taxable' => $_POST['is_taxable'],
        );
        $result = $fc->patch($_REQUEST['resource_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            $action = 'view_coupon';

            // add our item category restrictions
            $category_uris_to_add = array();
            $category_uris_to_remove = array();
            $original_values = array();
            foreach($_POST as $field => $value) {
                $prefix = strpos($field, 'category_uris_');
                if ($prefix !== false) {
                    $category_uri = substr($field, strlen('category_uris_'));
                    $category_uris_to_add[$category_uri] = $category_uri;
                }
                $prefix = strpos($field, 'original_c_uri_value_');
                if ($prefix !== false) {
                    $category_uri = substr($field, strlen('original_c_uri_value_'));
                    if ($value != '') {
                        $original_values[$category_uri] = $value;
                    }
                }
            }
            foreach($original_values as $category_uri => $coupon_item_category_uri) {
                if (!array_key_exists($category_uri, $category_uris_to_add)) {
                    $category_uris_to_remove[] = $coupon_item_category_uri;
                } else {
                    unset($category_uris_to_add[$category_uri]);
                }
            }
            if (count($category_uris_to_remove)) {
                foreach($category_uris_to_remove as $coupon_item_category_uri) {
                    $result = $fc->delete(urldecode($coupon_item_category_uri));
                    $errors = array_merge($errors,$fc->getErrors($result));
                }
            }
            if (count($category_uris_to_add)) {
                // get the fx:coupon_item_categories href
                $result = $fc->get($_REQUEST['resource_uri']);
                $errors = array_merge($errors,$fc->getErrors($result));
                if (!count($errors)) {
                    $coupon_item_categories_uri = $fc->getLink('fx:coupon_item_categories');
                    if ($coupon_item_categories_uri == '') {
                        $errors[] = 'Unable to obtain fx:coupon_item_categories href';
                    } else {
                        foreach($category_uris_to_add as $category_uri) {
                            $data = array(
                                'coupon_uri' => $_REQUEST['resource_uri'],
                                'item_category_uri' => urldecode($category_uri)
                                );
                            $result = $fc->post($coupon_item_categories_uri, $data);
                            $errors = array_merge($errors,$fc->getErrors($result));
                        }
                    }
                }
            }
        }
    }
    if (count($errors)) {
        $action = 'edit_coupon_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'edit_coupon_form') {
    ?>
    <h2>Edit Coupon</h2>
    <?php
    // let's get the list of item categories
    $errors = array();
    $item_categories = array();
    if ($_SESSION['item_categories_uri']) {
        $result = $fc->get($_SESSION['item_categories_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            foreach($result['_embedded']['fx:item_categories'] as $item_category) {
                $item_categories[] = $item_category;
            }
        }
    }
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->get($_REQUEST['resource_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            $coupon_item_categories_uri = $result['_links']['fx:coupon_item_categories']['href'];
            ?>
            <form role="form" action="/?action=save_coupon" method="post" class="form-horizontal">
            <?php
            $boolean_fields = array('combinable','multiple_codes_allowed','exclude_category_discounts','exclude_line_item_discounts','is_taxable');
            foreach($result as $field => $value) {
                if ($field != '_links' && $field != 'date_created' && $field != 'date_modified') {
                    if ($field == 'coupon_discount_type') {
                        ?>
                        <div class="form-group">
                            <label for="coupon_discount_type" class="col-sm-2 control-label">Coupon Discount Type</label>
                            <div class="col-sm-3">
                                <select name="coupon_discount_type">
                                <?php $selected = ($value == 'quantity_amount') ? ' selected="selected"' : ''; ?>
                                <option<?php print $selected; ?> value="quantity_amount">Amount Based on Quantity (quantity_amount)</option>
                                <?php $selected = ($value == 'quantity_percentage') ? ' selected="selected"' : ''; ?>
                                <option<?php print $selected; ?> value="quantity_percentage">Percentage Based on Quantity (quantity_percentage)</option>
                                <?php $selected = ($value == 'price_amount') ? ' selected="selected"' : ''; ?>
                                <option<?php print $selected; ?> value="price_amount">Amount Based on Price (price_amount)</option>
                                <?php $selected = ($value == 'price_percentage') ? ' selected="selected"' : ''; ?>
                                <option<?php print $selected; ?> value="price_percentage">Percentage Based on Price (price_percentage)</option>
                                </select>
                            </div>
                        </div>
                        <?php
                    } elseif (in_array($field, $boolean_fields)) {
                        ?>
                        <div class="form-group">
                            <div class="radio">
                                <label for="<?php print $field; ?>_true" class="col-sm-2 control-label"><?php print ucwords(str_replace('_',' ',$field)); ?>?</label>
                                <div class="col-sm-3">
                                    <label class="radio-inline">
                                        <?php $checked = ($value) ? ' checked="checked"' : ''; ?>
                                        <input<?php print $checked; ?> type="radio" id="<?php print $field; ?>_true" name="<?php print $field; ?>" value="true" /> Yes
                                    </label>
                                    <label class="radio-inline">
                                        <?php $checked = (!$value) ? ' checked="checked"' : ''; ?>
                                        <input<?php print $checked; ?> type="radio" id="<?php print $field; ?>_false" name="<?php print $field; ?>" value="false" /> No
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="form-group">
                            <label for="<?php print $field; ?>" class="col-sm-2 control-label"><?php print ucwords(str_replace('_',' ',$field)); ?></label>
                            <div class="col-sm-3">
                                <input type="<?php print $field; ?>"
                                    class="form-control"
                                    id="<?php print $field; ?>"
                                    name="<?php print $field; ?>"
                                    maxlength="200"
                                    value="<?php print htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                                />
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            // get category associations
            $result = $fc->get($coupon_item_categories_uri, array('limit' => 300));
            $errors = array_merge($errors,$fc->getErrors($result));
            $applied_categories = array();
            $coupon_item_categories = array();
            if (!count($errors)) {
                foreach($result['_embedded']['fx:coupon_item_categories'] as $coupon_item_category) {
                    foreach($item_categories as $category) {
                        $category_uri = $category['_links']['self']['href'];
                        if ($coupon_item_category['item_category_uri'] == $category_uri) {
                            $applied_categories[] = $category_uri;
                            $coupon_item_categories[urlencode($category_uri)] = urlencode($coupon_item_category['_links']['self']['href']);
                        }
                    }
                }
            }
            ?>
            <h3>Restrict by Item Category</h3>
            <p>Leave everything unchecked to set no restrictions and make this coupon available to all categories.</p>
            <?php
            foreach($item_categories as $category) {
                $category_uri = $category['_links']['self']['href'];
                $is_checked = in_array($category_uri, $applied_categories);
                $category_uri = urlencode($category_uri);
                ?>
                <input type="hidden" id="original_c_uri_value_<?php print $category_uri; ?>" name="original_c_uri_value_<?php print $category_uri; ?>" value="<?php print (($is_checked) ? $coupon_item_categories[$category_uri] : ''); ?>" />
                <?php
                if (isset($_POST['category_uris_' . $category_uri])) {
                    $is_checked = ($_POST['category_uris_' . $category_uri] == 'true');
                }
                ?>
                <div class="form-group">
                    <div class="checkbox">
                        <div class="col-sm-3">
                            <label>
                                <?php $checked = ($is_checked) ? ' checked="checked"' : ''; ?>
                                <input<?php print $checked; ?> type="checkbox" id="category_uris_<?php print $category_uri; ?>" name="category_uris_<?php print $category_uri; ?>" value="<?php print $category_uri; ?>" />
                                <?php print $category['code'] . ': ' . $category['name']; ?>
                            </label>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
                <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <button type="submit" class="btn btn-primary">Save Coupon</button>
            </form>
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

if ($action == 'view_coupon') {
    ?>
    <h2>View Coupon</h2>
    <?php
    $errors = array();
    $resouce_uri = (isset($_REQUEST['resource_uri']) ? $_REQUEST['resource_uri'] : '');
    $coupon_codes_uri = '';
    $generate_codes_uri = '';
    $coupon_name = '';
    if ($resouce_uri == '') {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $item_categories = array();
        $result = $fc->get($_SESSION['item_categories_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            foreach($result['_embedded']['fx:item_categories'] as $item_category) {
                $item_categories[] = $item_category;
            }
        }
        $result = $fc->get($resouce_uri,array('zoom' => 'coupon_codes'));
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            $coupon_name = $result['name'];
            ?>
            <h3><?php print $coupon_name; ?></h3>
            <div class="col-md-6">
            <table class="table">
            <?php
            $generate_codes_uri = $result['_links']['fx:generate_codes']['href'];
            $coupon_codes_uri = $result['_links']['fx:coupon_codes']['href'];
            $coupon_item_categories_uri = $result['_links']['fx:coupon_item_categories']['href'];

            $embedded_data = array();
            $boolean_fields = array('combinable','multiple_codes_allowed','exclude_category_discounts','exclude_line_item_discounts','is_taxable');
            foreach($result as $field => $value) {
                if ($field != '_links' && $field != '_embedded' && $field != 'name') {
                    if (in_array($field, $boolean_fields)) {
                        $value = ($value) ? 'yes' : 'no';
                    }
                    ?>
                    <tr>
                        <td><?php print ucwords(str_replace('_',' ',$field)); ?>: </td>
                        <td><?php print htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                    </tr>
                    <?php
                }
                if ($field == '_embedded') {
                    $embedded_data = $value['fx:coupon_codes'];
                }
            }
            // get category associations
            $result = $fc->get($coupon_item_categories_uri, array('limit' => 300));
            $errors = array_merge($errors,$fc->getErrors($result));
            $applied_categories = array();
            if (!count($errors)) {
                foreach($result['_embedded']['fx:coupon_item_categories'] as $coupon_item_category) {
                    foreach($item_categories as $category) {
                        $category_uri = $category['_links']['self']['href'];
                        if ($coupon_item_category['item_category_uri'] == $category_uri) {
                            $applied_categories[] = $category['code'] . ': ' . $category['name'];
                        }
                    }
                }
                ?>
                <tr><td colspan="2">
                <h3>Item Category Restrictions</h3>
                <?php
                if (!count($applied_categories)) {
                    print '<p>Applies to all categories</p>';
                } else {
                    foreach ($applied_categories as $applied_category) {
                        print '<p>' . $applied_category . '</p>';
                    }
                }
                ?>
                </td>
                </tr>
                <?php
            }
            if (count($embedded_data)) {
                ?>
                <tr><td colspan="2">
                <h2>Coupon Codes</h2>
                <form role="form" action="/?action=view_coupon_codes" method="post" class="form-horizontal">
                <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($resouce_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="hidden" name="resource_collection_uri" value="<?php print htmlspecialchars($coupon_codes_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="submit" name="submit" class="btn btn-info" value="View All Coupon Codes" />
                </form><br />
                <?php
                foreach($embedded_data as $coupon_code) {
                    foreach($coupon_code as $cc_field => $cc_value) {
                        if ($cc_field == 'code') {
                            print '<code>' . $cc_value . '</code>';
                        }
                        if ($cc_field == 'number_of_uses_to_date') {
                            print ' (' . $cc_value . ' uses so far)<br />';
                        }
                    }
                }
                if (count($embedded_data) == 20) {
                    print '...(this list may be incomplete)...<br />';
                }
                ?>
                </td>
                </tr>
                <?php
            }
            ?>
            </table>
            <form role="form" action="/?action=edit_coupon_form" method="post" class="form-horizontal">
            <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($resouce_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="submit" name="submit" class="btn btn-warning" value="Edit <?php print $coupon_name; ?>" />
            </form><br />
            <hr />

            <form role="form" action="/?action=add_coupon_code_form" method="post" class="form-horizontal">
            <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($resouce_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="resource_creation_uri" value="<?php print htmlspecialchars($coupon_codes_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="generate_codes_uri" value="<?php print htmlspecialchars($generate_codes_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="submit" name="submit" class="btn btn-info" value="Add Coupon Codes" />
            </form><br />

            <a class="btn btn-primary" href="/?action=view_coupons">View All Coupons</a>
            </div>
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


if ($action == 'delete_coupon_code') {
    $errors = array();
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->delete($_REQUEST['resource_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        print '<h3 class="alert alert-success" role="alert">Coupon Code Deleted</h3>';
        $action = 'view_coupon_codes';
    }
    if (count($errors)) {
        $action = 'view_coupon_codes';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'delete_coupon_code_form') {
    $errors = array();
    if (!isset($_REQUEST['resource_uri'])) {
        $errors[] = 'The required resource_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
            ?>
            <p>Are you sure you want to delete the <code><?php print $_REQUEST['resource_name']; ?></code> coupon code?
            <form role="form" action="/?action=delete_coupon_code" method="post" class="form-horizontal">
            <input type="hidden" name="resource_uri" value="<?php print htmlspecialchars($_REQUEST['resource_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="resource_collection_uri" value="<?php print htmlspecialchars($_REQUEST['resource_collection_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="submit" name="submit" class="btn btn-danger" value="Yes, Delete It" />
            </form>
            <?php
    }
    if (count($errors)) {
        $action = 'view_coupon_codes';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'view_coupon_codes') {
    ?>
    <h2>View Coupon Codes</h2>
    <?php
    $errors = array();
    $resource_collection_uri = isset($_REQUEST['resource_collection_uri']) ? $_REQUEST['resource_collection_uri'] : '';
    $result = $fc->get($_REQUEST['resource_collection_uri']);
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        ?>
        <h3>Coupon Codes</h3>
        <?php
        print '<p>Displaying ' . $result['returned_items'] . ' (' . ($result['offset']+1) . ' through ' . min($result['total_items'],($result['limit']+$result['offset'])) . ') of ' . $result['total_items'] . ' total coupons.</p>'
        ?>
        <nav>
          <ul class="pagination">
            <li>
              <a href="/?action=view_coupon_codes&amp;resource_collection_uri=<?php print urlencode($result['_links']['prev']['href']); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li>
              <a href="/?action=view_coupon_codes&amp;resource_collection_uri=<?php print urlencode($result['_links']['next']['href']); ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
        <table class="table">
        <tr>
            <th>Coupon Code</th>
            <th>Number of Uses to Date</th>
            <th>Date Created</th>
            <th>Date Modified</th>
            <th>&nbsp;</th>
        </tr>
        <?php
        $coupon_uri = '';
        foreach($result['_embedded']['fx:coupon_codes'] as $coupon_codes) {
            if ($coupon_uri == '') {
                $coupon_uri = $coupon_codes['_links']['fx:coupon']['href'];
            }
            ?>
            <tr>
                <td><?php print $coupon_codes['code']; ?></td>
                <td><?php print $coupon_codes['number_of_uses_to_date']; ?></td>
                <td><?php print $coupon_codes['date_created']; ?></td>
                <td><?php print $coupon_codes['date_modified']; ?></td>
                <td><a class="btn btn-danger" href="/?action=delete_coupon_code_form&amp;resource_collection_uri=<?php print urlencode($resource_collection_uri); ?>&amp;resource_uri=<?php print urlencode($coupon_codes['_links']['self']['href']); ?>&amp;resource_name=<?php print urlencode($coupon_codes['code']); ?>">Delete</a></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <?php
        if ($coupon_uri != '') {
            ?>
            <a class="btn btn-primary" href="/?action=view_coupon&amp;resource_uri=<?php print urlencode($coupon_uri); ?>">View Coupon</a></td>
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


if ($action == 'view_coupons') {
    ?>
    <h2>View Coupons</h2>
    <?php
    $errors = array();
    $coupons_uri = $_SESSION['coupons_uri'];
    if (isset($_REQUEST['coupons_uri'])) {
        $coupons_uri = $_REQUEST['coupons_uri'];
    }
    $result = $fc->get($coupons_uri, array("limit" => 5));
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        ?>
        <h3>Coupons for <?php print $_SESSION['store_name']; ?></h3>
        <?php
        print '<p>Displaying ' . $result['returned_items'] . ' (' . ($result['offset']+1) . ' through ' . min($result['total_items'],($result['limit']+$result['offset'])) . ') of ' . $result['total_items'] . ' total coupons.</p>'
        ?>
        <nav>
          <ul class="pagination">
            <li>
              <a href="/?action=view_coupons&amp;coupons_uri=<?php print urlencode($result['_links']['prev']['href']); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li>
              <a href="/?action=view_coupons&amp;coupons_uri=<?php print urlencode($result['_links']['next']['href']); ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
        <table class="table">
        <tr>
            <th>Coupon Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Uses So Far</th>
            <th>Uses Allowed</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        <?php
        foreach($result['_embedded']['fx:coupons'] as $coupon) {
            ?>
            <tr>
                <td><?php print $coupon['name']; ?></td>
                <td><?php print $coupon['start_date']; ?></td>
                <td><?php print $coupon['end_date']; ?></td>
                <td><?php print $coupon['number_of_uses_to_date']; ?></td>
                <td><?php print $coupon['number_of_uses_allowed']; ?></td>
                <td><a class="btn btn-primary" href="/?action=view_coupon&amp;resource_uri=<?php print urlencode($coupon['_links']['self']['href']); ?>">View</a></td>
                <td><a class="btn btn-warning" href="/?action=edit_coupon_form&amp;resource_uri=<?php print urlencode($coupon['_links']['self']['href']); ?>">Edit</a></td>
                <td><a class="btn btn-danger" href="/?action=delete_coupon_form&amp;resource_uri=<?php print urlencode($coupon['_links']['self']['href']); ?>&amp;resource_name=<?php print urlencode($coupon['name']); ?>">Delete</a></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <a class="btn btn-primary" href="/?action=add_coupon_form">Add Coupon</a>
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