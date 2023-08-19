
<?php
// We will move all env variables and other application level constants
// into this configuration file so we can cache them.
return [
    'axesso_api_query_endpoint'    => 'http://api-prd.axesso.de/amz/amazon-lookup-product',
    'axesso_api_account_endpoint'  => 'http://api-prd.axesso.de/usr/user-account-info',
    'axesso_api_search_product'    => 'http://api-prd.axesso.de/amz/amazon-search-by-keyword-asin',
    //  'axesso_api_key'               => 'axesso-api-key: 48da9d19-4158-4079-99b8-a862a99e7601',
    'axesso_api_key'               => '48da9d19-4158-4079-99b8-a862a99e7601',
    'sms_username'                 => '500012188614156',
    'sms_password'                 => '80818337',
    'sms_sender_number'            => '500012188614156',
    'sms_gateway_endpoint'         => 'http://www.linepayamak.ir/Post/Send.asmx?wsdl',
    'vandar_api_send'              => 'https://ipg.vandar.io/api/v3/send',
    'vandar_api_verify'            => 'https://ipg.vandar.io/api/v3/verify',
    'vandar_api_transaction'       => 'https://ipg.vandar.io/api/v3/transaction',
    'vandar_api_key'               => 'd7e5729e536585ab19cb871db72b5e022311b7ac',
    'vandar_base_url'              => 'https://ipg.vandar.io/v3/'
];
