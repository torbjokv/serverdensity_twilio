<?php

// Settings

define("TWILIO_SID", "CHANGE_ME");
define("TWILIO_TOKEN", "CHANGE_ME");
define("TWILIO_SENDER_PHONE", "CHANGE_ME");//Number including prefix, but not +. Example: 442033221234
define("BASE_URL", "CHANGE_ME");//Example: http://www.example.com/


$constants = get_defined_constants(true);
foreach ($constants['user'] as $key => $value) {
    if ($value == "CHANGE_ME")
        exit("Please change value of $key in settings.php");
}