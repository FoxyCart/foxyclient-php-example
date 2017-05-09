<?php

function printSubscriptionsAsCSV($fc, $subs_uri, $offset = 0) {
    $header = '"sub_token_url","customer_id","email","start_date","next_transaction_date","end_date","frequency","past_due_amount",';
    $header .= '"billing_first_name","billing_last_name","billing_company","billing_address1","billing_city","billing_region","billing_postal_code","billing_country",';
    $header .= '"item_name","price","quantity","item_options",';
    $header .= '"shipping_first_name","shipping_last_name","shipping_company","shipping_address1","shipping_city","shipping_region","shipping_postal_code","shipping_country"';
    $header .= "\n";
    $csv_header = '';
    $csv_detail = '';
    if ($offset == 0) {
        print $header;
    }
    $limit = 300;
    $params = array(
        'zoom' => 'customer:default_billing_address,last_transaction:shipments,transaction_template,transaction_template:items,transaction_template:items:item_options',
        'is_active' => 1,
        'offset' => $offset,
        'limit' => $limit,
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
        $csv_header = '"' . $subscription['_links']["fx:sub_token_url"]["href"] . '",';
        $csv_header .= '"' . $customer['id'] . '",';
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
            $item_price = $item['price'];
            $item_options = '';
            if (isset($item['_embedded']['fx:item_options'])) {
                foreach ($item['_embedded']['fx:item_options'] as $item_option) {
                    $item_options .= str_replace('"', "'",$item_option['name'] . ':::' . $item_option['value'] . '|||');
                    $item_price += $item_option['price_mod'];
                }
            }
            $csv .= $csv_header;
            $csv_detail = '';
            $csv_detail .= '"' . $item['name'] . '",';
            $csv_detail .= '"' . $item_price . '",';
            $csv_detail .= '"' . $item['quantity'] . '",';
            $item_options = trim($item_options,'|');
            $csv_detail .= '"' . $item_options . '",';
            $shipping_address = '';
            foreach ($shipments as $shipment) {
                if ($shipment['address_name'] == $item['shipto'] && $shipping_address == '') {
                    $shipping_address .= '"' . $shipment['first_name'] . '",';
                    $shipping_address .= '"' . $shipment['last_name'] . '",';
                    $shipping_address .= '"' . $shipment['company'] . '",';
                    $shipping_address .= '"' . $shipment['address1'] . '",';
                    $shipping_address .= '"' . $shipment['city'] . '",';
                    $shipping_address .= '"' . $shipment['region'] . '",';
                    $shipping_address .= '"' . $shipment['postal_code'] . '",';
                    $shipping_address .= '"' . $shipment['country'] . '",';
                    $csv_detail .= $shipping_address;
                }
            }
            $csv_detail = trim($csv_detail,',');
            $csv .= $csv_detail;
            $csv .= "\n";
        }
    }
    print $csv;
    if ($result['offset'] < $result['total_items'] && $result['returned_items'] == $result['limit']) {
        $offset += $result['limit'];
        if ($offset > $result['total_items']) {
            $offset = 0;
        }
        return $offset;
    }
    return 0;
}

if ($action == 'view_subscriptions_as_csv') {
    // in case you have a _lot_ of subscriptions
    //ini_set('memory_limit', '2G');
    ?>
    <h2>View Subscriptions as CSV</h2>
    <h3>Subscriptions for <?php print $_SESSION['store_name']; ?></h3>
<pre>
<?php
$subscriptions_uri = $_SESSION['subscriptions_uri'];
if (isset($_REQUEST['subscriptions_uri'])) {
    $subscriptions_uri = $_REQUEST['subscriptions_uri'];
}
$new_offset = printSubscriptionsAsCSV($fc,$subscriptions_uri);
while($new_offset != 0) {
sleep(1); // don't hammer the API
$new_offset = printSubscriptionsAsCSV($fc,$subscriptions_uri,$new_offset);
}
?>
</pre>
    <?php
}