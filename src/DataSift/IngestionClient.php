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

namespace DataSift;
use DataSift\Exception\APIError;
use GuzzleHttp\Client as HttpClient;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

class IngestionClient extends Client
{
    /**
     *
     *
     * @param   array               $config
     * @param   HttpClient          $httpClient
     * @param   Logger              $logger
     * @throws  APIError
     */
    public function __construct(
        array $config,
        HttpClient $httpClient = null,
        Logger $logger = null
    ) {
        $config = $this->validateConfig($config);
        $this->setConfig($config);

        if ($httpClient === null) {
            $httpClient = new HttpClient(array(
                //@todo SSL
                'base_uri' => 'http://' . $config['ingestion_uri'] . '/',
                'headers' => array(
                    'User-Agent' => $config['user_agent']
                ),
                'timeout' => $config['connection_timeout']
            ));
        }

        if ($logger === null) {
            $logger = new Logger('log');
        }

        $logger->pushHandler(new StreamHandler($config['log_path'], $config['log_level']));

        $this->setHttpClient($httpClient);
        $this->setLogger($logger);
    }
}