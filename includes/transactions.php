<?php

if ($action == 'add_tranaction_attribute') {
    $errors = array();
    if (!isset($_REQUEST['transaction_attributes_uri'])) {
        $errors[] = 'The required transaction_attributes_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $data = array(
            'name' => $_POST['name'],
            'value' => $_POST['value'],
            'visibility' => $_POST['visibility'],
        );
        $result = $fc->post($_REQUEST['transaction_attributes_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            print '<h3 class="alert alert-success" role="alert">Transaction Attribute Created</h3>';
            $action = 'view_transaction';
        }
    }
    if (count($errors)) {
        $action = 'add_transaction_attribute_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'add_transaction_attribute_form') {
    $errors = array();
    ?>
    <h2>Add Transaction Attribute</h2>
    <form role="form" action="/?action=add_tranaction_attribute" method="post" class="form-horizontal">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Attribute Name<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="name" name="name" maxlength="200" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="value" class="col-sm-2 control-label">Attribute Value<span class="text-danger">*</span></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="name" name="value" maxlength="200" value="<?php echo isset($_POST['value']) ? htmlspecialchars($_POST['value']) : ""; ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="visibility" class="col-sm-2 control-label">Visibility</label>
            <div class="col-sm-3">
                <select name="visibility">
                <?php $selected = (isset($_POST['visibility']) && $_POST['visibility'] == 'public') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="public">Public</option>
                <?php $selected = (isset($_POST['visibility']) && $_POST['visibility'] == 'private') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="private">Private</option>
                <?php $selected = (isset($_POST['visibility']) && $_POST['visibility'] == 'restricted') ? ' selected="selected"' : ''; ?>
                <option<?php print $selected; ?> value="restricted">Restricted to this OAuth Client</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="transaction_uri" value="<?php print htmlspecialchars($_REQUEST['transaction_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="transaction_attributes_uri" value="<?php print htmlspecialchars($_REQUEST['transaction_attributes_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
        <button type="submit" class="btn btn-primary">Add Transaction Attribute</button>
    </form>
    <?php
}

if ($action == 'edit_tranaction_attribute') {
    $errors = array();
    if (!isset($_REQUEST['attribute_uri'])) {
        $errors[] = 'The required attribute_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $data = array(
            'name' => $_POST['name'],
            'value' => $_POST['value'],
        );
        $result = $fc->patch($_REQUEST['attribute_uri'],$data);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            print '<h3 class="alert alert-success" role="alert">Transaction Attribute Edited</h3>';
            $action = 'view_transaction';
        }
    }
    if (count($errors)) {
        $action = 'edit_transaction_attribute_form';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'edit_transaction_attribute_form') {
    $errors = array();
    ?>
    <h2>Edit Transaction Attribute</h2>
    <?php
    if (!isset($_REQUEST['attribute_uri'])) {
        $errors[] = 'The required attribute_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->get($_REQUEST['attribute_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            ?>
            <form role="form" action="/?action=edit_tranaction_attribute" method="post" class="form-horizontal">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">Attribute Name<span class="text-danger">*</span></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="name" name="name" maxlength="200" value="<?php echo isset($result['name']) ? htmlspecialchars($result['name']) : ""; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="value" class="col-sm-2 control-label">Attribute Value<span class="text-danger">*</span></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="name" name="value" maxlength="200" value="<?php echo isset($result['value']) ? htmlspecialchars($result['value']) : ""; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="visibility" class="col-sm-2 control-label">Visibility</label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="visibility" readonly="readonly" value="<?php echo isset($result['visibility']) ? htmlspecialchars($result['visibility']) : ""; ?>">
                    </div>
                </div>
                <input type="hidden" name="transaction_uri" value="<?php print htmlspecialchars($_REQUEST['transaction_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="hidden" name="attribute_uri" value="<?php print htmlspecialchars($_REQUEST['attribute_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                <button type="submit" class="btn btn-primary">Edit Transaction Attribute</button>
            </form>
        <?php
        }
    }
}

if ($action == 'delete_transaction_attribute') {
    $errors = array();
    if (!isset($_REQUEST['attribute_uri'])) {
        $errors[] = 'The required attribute_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->delete($_REQUEST['attribute_uri']);
        $errors = array_merge($errors,$fc->getErrors($result));
        print '<h3 class="alert alert-success" role="alert">Transaction Attribute Deleted</h3>';
        $action = 'view_transaction';
    }
    if (count($errors)) {
        $action = 'view_transaction';
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}

if ($action == 'delete_transaction_attribute_form') {
    $errors = array();
    if (!isset($_REQUEST['attribute_uri'])) {
        $errors[] = 'The required attribute_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
            ?>
            <p>Are you sure you want to delete the <code><?php print $_REQUEST['attribute_name']; ?></code> transaction attribute?</p>
            <form role="form" action="/?action=delete_transaction_attribute" method="post" class="form-horizontal">
            <input type="hidden" name="attribute_uri" value="<?php print htmlspecialchars($_REQUEST['attribute_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="transaction_uri" value="<?php print htmlspecialchars($_REQUEST['transaction_uri'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
            <input type="submit" name="submit" class="btn btn-danger" value="Yes, Delete It" />
            </form>
            <?php
    }
    if (count($errors)) {
        $action = 'view_transaction';
        
        print '<div class="alert alert-danger" role="alert">';
        print '<h2>Error:</h2>';
        foreach($errors as $error) {
            print $error . '<br />';
        }
        print '</div>';
    }
}


if ($action == 'view_transaction') {
    ?>
    <h2>View Transaction</h2>
    <?php
    $errors = array();
    $transaction_uri = (isset($_REQUEST['transaction_uri']) ? $_REQUEST['transaction_uri'] : '');
    if ($transaction_uri == '') {
        $errors[] = 'The required transaction_uri is missing. Please click back and try again.';
    }
    if (!count($errors)) {
        $result = $fc->get($transaction_uri . "?zoom=payments,items,items:item_options,items:item_category,applied_taxes,custom_fields,discounts,shipments,billing_addresses");
        $errors = array_merge($errors,$fc->getErrors($result));
        if (!count($errors)) {
            ?>
            <h3>Transaction ID #<?php print $result['id']; ?></h3>
            <h5><?php print date("D d M Y h:i:s A T", strtotime($result['transaction_date'])); ?> | <?php print $result['customer_first_name'] . " " . $result['customer_last_name']; ?> | <?php print $result['customer_email']; ?> | <a href="<?php print $result['_links']['fx:receipt']['href']; ?>" target="_blank">Web Receipt</a></h5>

            <table class="table">
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>   
                <?php
                foreach ($result['_embedded']['fx:items'] as $item) {
                    print "<tr>";
                        print "<td>";
                        print $item['name'];
                        if (array_key_exists('fx:item_options', $item['_embedded'])) {
                            foreach ($item['_embedded']['fx:item_options'] as $option) {
                                print "<br>" . $option['name'] . ": " . $option['value'];
                            }
                        }
                        if ($item['code'] != "") {
                            print "<br>Code: " . $item['code'];
                        }
                        if ($item['_embedded']['fx:item_category']['code'] != "DEFAULT") {
                            print "<br>Category: " . $item['_embedded']['fx:item_category']['code'];
                        }
                        if ($item['weight'] > 0) {
                            print "<br>Weight: " . $item['weight'];
                        }
                        if ($item['subscription_frequency'] != "") {
                            print "<br><u>Subscription Details</u>";
                            print "<br>Frequency: " . $item['subscription_frequency'];
                            print "<br>Start Date: " . date("d M Y", strtotime($item['subscription_start_date']));
                            print "<br>Next Date: " . date("d M Y", strtotime($item['subscription_next_transaction_date']));
                            if ($item['subscription_end_date'] != "") {
                                print "<br>End Date: " . date("d M Y", strtotime($item['subscription_end_date']));
                            }
                        }
                        print "</td>";
                        print "<td>" . $item['quantity'] . "</td>";
                        print "<td>" . $item['price'] . "</td>";
                    print "</tr>";
                }
                ?>
                <tr><td colspan='2' class='text-right'>Subtotal</td><td><?php print $result['total_item_price']; ?></td></tr>
                <tr><td colspan='2' class='text-right'>Shipping</td><td><?php print $result['total_shipping']; ?></td></tr>
                <tr><td colspan='2' class='text-right'>Tax</td><td><?php print $result['total_tax']; ?></td></tr>
                <tr><td colspan='2' class='text-right'>Total</td><td><?php print $result['total_order']; ?></td></tr>
            </table>
            <div class="row">
                <div class="col-md-6">
                <?php
                
                print "<h4>Billing</h4>";
                $billing = $result['_embedded']['fx:billing_addresses'][0];
                $details = "";
                $details .= $billing['first_name'] . " " . $billing['last_name'];
                if ($billing['company'] !== "") $details .= "<br>" . $billing['company'];
                if ($billing['address1'] !== "") $details .= "<br>" . $billing['address1'];
                if ($billing['address2'] !== "") $details .= "<br>" . $billing['address2'];
                $details .= "<br>" . $billing['city'] . " " . $billing['region'] . " " . $billing['customer_postal_code'];
                $details .= "<br>" . $billing['customer_country'];
                print "<p>" . $details . "</p>";
                ?>

                <h4>Payment</h4>
                <?php
                $payment = $result['_embedded']['fx:payments'][0];
                if ($payment['type'] == "plastic") {
                ?>
                    <dl>
                        <dt>Processor Response</dt>
                        <dd><?php print $payment['processor_response']; ?></dd>
                        <dt><?php print $payment['cc_type']; ?></dt>
                        <dd><?php print $payment['cc_number_masked']; ?></dd>
                        <dt>Expiry</dt>
                        <dd><?php print $payment['cc_exp_month'] . "/" . $payment['cc_exp_year']; ?></dd>
                    </dl>
                <?php
                } else if ($payment['type'] == "paypal") {
                ?>
                    <dl>
                        <dt>Processor Response</dt>
                        <dd><?php print $payment['processor_response']; ?></dd>
                        <dt>PayPal ID</dt>
                        <dd><?php print $payment['paypal_payer_id']; ?></dd>
                    </dl>
                <?php
                }
                ?>
                </div>
                <div class="col-md-6">
                <?php
                foreach ($result['_embedded']['fx:shipments'] as $shipment) {
                    if (count($result['_embedded']['fx:shipments']) > 1 || $shipment['address_name'] != "Me") {
                        print "<h4>Shipping to " . $shipment['address_name'] . "</h4>";
                    } else {
                        print "<h4>Shipping</h4>";
                    }
                    $details = "";
                    $details .= $shipment['first_name'] . " " . $shipment['last_name'];
                    if ($shipment['company'] !== "") $details .= "<br>" . $shipment['company'];
                    if ($shipment['address1'] !== "") $details .= "<br>" . $shipment['address1'];
                    if ($shipment['address2'] !== "") $details .= "<br>" . $shipment['address2'];
                    $details .= "<br>" . $shipment['city'] . " " . $shipment['region'] . " " . $shipment['postal_code'];
                    $details .= "<br>" . $shipment['country'];
                    print "<p>" . $details . "</p>";
                    if ($shipment['shipping_service_description'] != "") {
                        print "<p>Method: " . $shipment['shipping_service_description'] . "</p>";
                    }
                }
                ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php
            $attributes_uri = (isset($_REQUEST['transaction_attributes_uri']) ? $_REQUEST['transaction_attributes_uri'] : $fc->getLink("fx:attributes"));
            if ($attributes_uri != "") {
                $result = $fc->get($attributes_uri, array("limit" => 5));
                $errors = array_merge($errors,$fc->getErrors($result));
                if (!count($errors)) {
                    print "<hr />";
                    print "<h3>Transaction Attributes</h3>";
                    
                    if ($result['total_items'] == 0) {
                        print "<p>No attributes set.</p>";
                    } else {
                        print '<p>Displaying ' . $result['returned_items'] . ' (' . ($result['offset']+1) . ' through ' . min($result['total_items'],($result['limit']+$result['offset'])) . ') of ' . $result['total_items'] . ' total coupons.</p>'
                        ?>
                        
                        <nav>
                          <ul class="pagination">
                            <li>
                              <a href="/?action=view_transaction&amp;transaction_uri=<?php print urlencode($transaction_uri) ?>&amp;transaction_attributes_uri=<?php print urlencode($result['_links']['prev']['href']); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                              </a>
                            </li>
                            <li>
                              <a href="/?action=view_transaction&amp;transaction_uri=<?php print urlencode($transaction_uri) ?>&amp;transaction_attributes_uri=<?php print urlencode($result['_links']['next']['href']); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                              </a>
                            </li>
                          </ul>
                        </nav>
                        <table class="table">
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Visibility</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                        <?php
                        foreach($result['_embedded']['fx:attributes'] as $attribute) {
                            ?>
                            <tr>
                                <td><?php print $attribute['name']; ?></td>
                                <td><?php print $attribute['value']; ?></td>
                                <td><?php print $attribute['visibility']; ?></td>
                                <td><a class="btn btn-warning" href="/?action=edit_transaction_attribute_form&amp;transaction_uri=<?php print urlencode($transaction_uri); ?>&amp;attribute_uri=<?php print urlencode($attribute['_links']['self']['href']); ?>">Edit</a></td>
                                <td><a class="btn btn-danger" href="/?action=delete_transaction_attribute_form&amp;transaction_uri=<?php print urlencode($transaction_uri); ?>&amp;attribute_uri=<?php print urlencode($attribute['_links']['self']['href']); ?>&amp;attribute_name=<?php print urlencode($attribute['name']); ?>">Delete</a></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </table>
                    <?php
                    }
                    ?>
                    <form role="form" action="/?action=add_transaction_attribute_form" method="post" class="form-horizontal">
                        <input type="hidden" name="transaction_uri" value="<?php print htmlspecialchars($transaction_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                        <input type="hidden" name="transaction_attributes_uri" value="<?php print htmlspecialchars($attributes_uri, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                        <input type="hidden" name="csrf_token" value="<?php print htmlspecialchars($token, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" />
                        <input type="submit" name="submit" class="btn btn-info" value="Add Transaction Attribute" />
                    </form>
                <?php
                }
            }

            ?>
            <hr />

            <a class="btn btn-primary" href="/?action=view_transactions">View All Transactions</a>
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


if ($action == 'view_transactions') {
    ?>
    <h2>View Transactions</h2>
    <?php
    $errors = array();
    $transactions_uri = $_SESSION['transactions_uri'];
    if (isset($_REQUEST['transactions_uri'])) {
        $transactions_uri = $_REQUEST['transactions_uri'];
    }
    $result = $fc->get($transactions_uri, array("limit" => 5));
    $errors = array_merge($errors,$fc->getErrors($result));
    if (!count($errors)) {
        ?>
        <h3>Transactions for <?php print $_SESSION['store_name']; ?></h3>
        <?php
        print '<p>Displaying ' . $result['returned_items'] . ' (' . ($result['offset']+1) . ' through ' . min($result['total_items'],($result['limit']+$result['offset'])) . ') of ' . $result['total_items'] . ' total transactions.</p>'
        ?>
        <nav>
          <ul class="pagination">
            <li>
              <a href="/?action=view_transactions&amp;transactions_uri=<?php print urlencode($result['_links']['prev']['href']); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li>
            <li>
              <a href="/?action=view_transactions&amp;transactions_uri=<?php print urlencode($result['_links']['next']['href']); ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
        <table class="table">
        <tr>
            <th>Date</th>
            <th>ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>&nbsp;</th>
        </tr>
        <?php
        foreach($result['_embedded']['fx:transactions'] as $transaction) {
            ?>
            <tr>
                <td><?php print date("D d M Y h:i:s A T", strtotime($transaction['transaction_date'])); ?></td>
                <td><?php print $transaction['id']; ?></td>
                <td><?php print $transaction['customer_first_name'] . ' ' . $transaction['customer_last_name']; ?></td>
                <td><?php print $transaction['total_order']; ?></td>
                <td><a class="btn btn-primary" href="/?action=view_transaction&amp;transaction_uri=<?php print urlencode($transaction['_links']['self']['href']); ?>">View</a></td>
            </tr>
            <?php
        }
        ?>
        </table>
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