<?php

return [

    /*
    | Shown on the thank-you message after a request. The SRS requires showing
    | an expected response time but does not give one, so this is a placeholder
    | to confirm with the client.
    */
    'expected_response_time' => [
        'ar' => 'سنرد عليك خلال 24 ساعة عمل',
        'en' => 'We will respond within 24 working hours',
    ],

    /*
    | Mobile country code prefix -> ISO 3166-1 alpha-2 code. Egypt is the
    | default for anything not listed. Gulf codes are checked against the
    | gulf_countries setting to pick the WhatsApp number.
    */
    'mobile_prefixes' => [
        '966' => 'SA',
        '971' => 'AE',
        '965' => 'KW',
        '974' => 'QA',
        '973' => 'BH',
        '968' => 'OM',
        '20' => 'EG',
    ],

    'submissions_per_hour' => 5,

];
