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
use DataSift\Base\Client as BaseClient;
use DataSift\Exception\APIError;
use GuzzleHttp\Stream\Stream as StreamingFactory;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;


class StreamingClient extends BaseClient
{
    /**
     *
     *
     * @param   array               $config
     * @param   Logger              $logger
     * @throws  APIError
     */
    public function __construct(
        array $config,
        Logger $logger = null
    ) {
        $config = $this->validateConfig($config);
        $this->setConfig($config);

        if ($logger === null) {
            $logger = new Logger('log');
        }

        $logger->pushHandler(new StreamHandler($config['log_path'], $config['log_level']));

        $this->setLogger($logger);
    }

    public function getStream($test)
    {
        return StreamingFactory::factory($test);
    }
}