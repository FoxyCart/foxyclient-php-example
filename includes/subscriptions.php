<?php

function getSubscriptionsAsCSV($fc, $subs_uri, $offset = 0,$csv = '') {
    $header = '"customer_id","email","start_date","next_transaction_date","end_date","frequency","past_due_amount","';
    $header .= '"billing_first_name","billing_last_name","billing_company","billing_address1","billing_city","billing_region","billing_postal_code","billing_country"';
    $header .= '"item_name","price","quantity","item_options"';
    $header .= '"shipping_first_name","shipping_last_name","shipping_company","shipping_address1","shipping_city","shipping_region","shipping_postal_code","shipping_country"';
    $header .= "\n";
    $csv_header = '';
    $csv_detail = '';
    if ($csv == '') {
        $csv .= $header;
    }
    $limit = 300;
    $params = array(
        'zoom' => 'customer:default_billing_address,last_transaction:shipments,transaction_template,transaction_template:items,transaction_template:item_options',
        'is_active' => 1,
        'offset' => $offeset,
        'limit' => $limit
        );
    $result = $fc->get($subs_uri,$params);
    foreach($result['_embedded']['fx:subscriptions'] as $subscription) {
        $transaction_template = $subscription['_embedded']['fx:transaction_template'];
        $customer = $subscription['_embedded']['fx:customer'];
        $billing_address = $customer['_embedded']['fx:default_billing_address'];
        $shipments = $subscription['_embedded']['fx:last_transaction']['_embedded']['fx:shipments'];
        $items = array();
        foreach($transaction_template['_embedded']['fx:items'] as $item) {
            $items[] = $item;
        }
        $csv_header = '"' . $customer['id'] . '",';
        $csv_header .= '"' . $customer['email'] . '",';
        $csv_header .= '"' . $subscription['start_date'] . '",';
        $csv_header .= '"' . $subscription['next_transaction_date'] . '",';
        $csv_header .= '"' . $subscription['end_date'] . '",';
        $csv_header .= '"' . $subscription['frequency'] . '",';
        $csv_header .= '"' . $subscription['past_due_amount'] . '",';
        $csv_header .= '"' . $billing_address['first_name'] . '",';
        $csv_header .= '"' . $billing_address['last_name'] . '",';
        $csv_header .= '"' . $billing_address['company'] . '",';
        $csv_header .= '"' . $billing_address['address1'] . '",';
        $csv_header .= '"' . $billing_address['city'] . '",';
        $csv_header .= '"' . $billing_address['region'] . '",';
        $csv_header .= '"' . $billing_address['postal_code'] . '",';
        $csv_header .= '"' . $billing_address['country'] . '",';
        foreach ($items as $item) {
            $csv .= $csv_header;
            $csv_detail = '';
            $csv_detail .= '"' . $item['name'] . '",';
            $csv_detail .= '"' . $item['price'] . '",';
            $csv_detail .= '"' . $item['quantity'] . '",';
            $item_options = '';
            if (isset($item['_embedded']['fx:item_options'])) {
                foreach ($item['_embedded']['fx:item_options'] as $item_option) {
                    $item_options .= $item_option['name'] . ':' . $item_option['value'] . '|';
                }
            }
            $item_options = trim($item_options,'|');
            $csv_detail .= '"' . $item_options . '",';
            foreach ($shipments as $shipment) {
                if ($shipment['address_name'] == $item['shipto']) {
                    $csv_detail .= '"' . $shipment['first_name'] . '",';
                    $csv_detail .= '"' . $shipment['last_name'] . '",';
                    $csv_detail .= '"' . $shipment['company'] . '",';
                    $csv_detail .= '"' . $shipment['address1'] . '",';
                    $csv_detail .= '"' . $shipment['city'] . '",';
                    $csv_detail .= '"' . $shipment['region'] . '",';
                    $csv_detail .= '"' . $shipment['postal_code'] . '",';
                    $csv_detail .= '"' . $shipment['country'] . '",';
                }
            }
            $csv_detail = trim($csv_detail,',');
            $csv .= $csv_detail;
            $csv .= "\n";
        }
    }
    if ($result['offset'] < $result['total_items'] && $result['returned_items'] == $result['limit']) {
        $offset += $result['limit'];
        $csv .= getSubscriptionsAsCSV($fc, $subs_uri, $offset, $csv);
    }
    return $csv;
}

if ($action == 'view_subscriptions_as_csv') {
    ?>
    <h2>View Subscriptions as CSV</h2>
    <?php
    $errors = array();
    $subscriptions_uri = $_SESSION['subscriptions_uri'];
    if (isset($_REQUEST['subscriptions_uri'])) {
        $subscriptions_uri = $_REQUEST['subscriptions_uri'];
    }
    $csv = getSubscriptionsAsCSV($fc,$subscriptions_uri);
    ?>
    <h3>Subscriptions for <?php print $_SESSION['store_name']; ?></h3>
<pre>
<?php print $csv; ?>
</pre>
    <?php
}