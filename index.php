<?php
/**
 * Server Density callback endpoint for activating twilio phone call
 *
 * @author Torbjørn Kvåle <torbjoernkvaale@gmail.com>
 *
 */

$settingsname = "settings.php";
if (!file_exists($settingsname)) {
    file_put_contents($settingsname, file_get_contents('settings-example.php'));
} 
require $settingsname;
require 'includes/twilio-php/Services/Twilio.php';


/**
 *  Data Fields
 *  alert_section - The top level alert section e.g. System
 *  alert_type - The sub type e.g. Load average
 *  configured_trigger_value - The value that will trigger this alert from the configuration
 *  configured_trigger_location_threshold (services only) - How many monitoring locations the status must exist at before the alert is triggered
 *  current_value - The current value of this metric as per the latest value posted back by the agent or through the service monitor request.
 *  item_cloud (devices only) - Whether the alert is triggered on a cloud device or not
 *  item_id - The ID of the item (device or service) the alert is triggered on
 *  item_name - The name of the item (device or service) the alert is triggered on
 *  item_type - The type of item (Device or Service) the alert is triggered on
 *  trigger_datetime - The time in UTC the alert was triggered
 *  triggered_value - The value that triggered the alert (only for devices)
 *  fixed - Will be present as true for the final fixed notification
 *
 *  More on the format: http://support.serverdensity.com/knowledgebase/articles/221772-webhook-format
 *  How to set up webhook: http://support.serverdensity.com/knowledgebase/articles/206749-setting-up-webhooks
 */

// Message to read

$item_name                = $_GET['item_name'];
$item_type                = $_GET['item_type'];
$configured_trigger_value = $_GET['configured_trigger_value'];

$alert = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<Response>
<Pause length="2"/>
<Say voice="alice">
This is a alert call.
</Say>
<Pause length="1"/>
<Say voice="alice">
$item_name $item_type is $configured_trigger_value. I repeat. $item_name $item_type is $configured_trigger_value.
</Say>
<Say voice="alice">
Please respond to this issue.
</Say>
</Response>
XML;


$receiver_phone = preg_replace("/[^0-9]/", "", $_GET['phone']);


if (isset($receiver_phone) && strlen($receiver_phone)>=9) {
    $client = new Services_Twilio(TWILIO_SID, TWILIO_TOKEN, "2010-04-01");

    try {
        $call = $client->account->calls->create(
            TWILIO_SENDER_PHONE, 
            '+'.$receiver_phone, 
            BASE_URL."?getresponse=true&".http_build_query(json_decode($HTTP_RAW_POST_DATA, true))
        );
        echo 'STARTED CALL: ' . $call->sid;
    } catch (Exception $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
} elseif (isset($_GET['getresponse'])) {
    echo $alert;
} else {
    echo "ERROR: No phone number was provided";
}

