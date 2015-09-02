<?php
/**
 * DataSift Client for PHP
 *
 * This software is the intellectual property of MediaSift Ltd., and is covered
 * by retained intellectual property rights, including copyright.
 * Distribution of this software is strictly forbidden under the terms of this license.
 *
 * @category  DataSift
 * @package   DataSift Client for PHP
 * @author    Chris Knight <chris.knight@datasift.com>
 * @copyright 2015 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */
class ClientTest extends PHPUnit_Framework_TestCase {

    public function validateConfigProvider()
    {
        return array(
            array(
                'api_key'               => '123445678901234567890123456789012',
                'username'              => 'demo_name',
                'use_ssl'               => false,
                'verify'                => true,
                'debug'                 => true,
                'streaming_end_point'   => 'test'
            ),
            array(
                'api_key'               => '123445678901234567890123456789012',
                'username'              => 'demo_name',
                'use_ssl'               => false,
                'verify'                => true,
                'debug'                 => true,
                'base_uri'              => 'api.datasift.com'
            ),
        );
    }

    /**
     * @param $config
     * @param $expectedResult
     *
     * @dataProvider validateConfigProvider
     */
    public function testValidateConfig($config, $expectedResult)
    {
        $curlClient = $this->getMock('GuzzleHttp\Client');
        $client = new \DataSift\Client($config, $curlClient);

        $this->assertEquals($expectedResult, $client->getConfig());
    }
}