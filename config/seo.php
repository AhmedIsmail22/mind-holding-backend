<?php

return [

    /*
    | Fixed route-level pages that have no database row of their own. Their
    | SEO lives in seo_meta under these keys.
    */
    'route_keys' => ['home', 'services', 'solutions', 'work', 'contact'],

    /*
    | Public site origin used to turn share-image paths into absolute URLs
    | (WhatsApp and LinkedIn need absolute og:image URLs).
    */
    'site_url' => env('PUBLIC_SITE_URL', 'https://mindholding.net'),

];
