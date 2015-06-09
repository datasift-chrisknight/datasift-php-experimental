<?php
/**
 * Created by PhpStorm.
 * User: chrisknight
 * Date: 04/06/2015
 * Time: 12:06
 */
//horrid
require_once __DIR__.'/vendor/autoload.php';

try {
    $config = array(
        'api_key'               => 'c8444e0e9c9cc30e0f08f11a67493d24',
        'username'              => 'pylon_product',
        'use_ssl'               => false,
        'verify'                => true,
        //'debug'                 => true,
        'streaming_end_point'   => 'test',
        'test'                  => 'test'
    );
    $client = new \DataSift\Client($config);
    $identity = new \DataSift\Account\Identity($client);

    #print_r($client);
    print_r($identity->getAll(null, 1, 1));
    print_r($identity->get('389ac5b87c29eb2e135d6573f4d4bc1b'));
    print_r($client);
} catch (Exception $e) {
    print_r($e->getMessage());
}
