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

namespace DataSift\Base;
use Monolog\Logger as Logger;

class Client
{
    /**
     * The default user agent for the client
     */
    const DEFAULT_USER_AGENT                = 'DataSiftPHP/3.0.0-alpha';

    /**
     * The default API location
     */
    const DEFAULT_BASE_URI                  = 'api.datasift.com';

    /**
     * The default streaming location
     */
    const DEFAULT_STREAM_URI                = 'stream.datasift.com';

    /**
     * The default API version
     */
    const DEFAULT_API_VERSION               = 1.2;

    /**
     * The default SSL verification check
     */
    const DEFAULT_SSL_VERIFY                = true;

    /**
     * Allow debugging to be added to the logs
     */
    const DEFAULT_DEBUG                     = false;

    /**
     * Allow debugging requests to be shown in the client
     */
    const DEFAULT_DEBUG_REQUEST             = false;

    /**
     *  The connection timeout for the request
     */
    const DEFAULT_CONNECTION_TIMEOUT        = 5;

    /**
     * The default log level for the client
     */
    const DEFAULT_LOG_LEVEL                 = Logger::WARNING;

    /**
     * The default log location
     */
    const DEFAULT_LOG_PATH                  = '/Users/chrisknight/api.log';

    /**
     * HTTP Success
     */
    const HTTP_OK                           = 200;
    const HTTP_CREATED                      = 201;
    const HTTP_ACCEPTED                     = 202;
    const HTTP_NO_CONTENT                   = 204;
    const HTTP_RESET_CONTENT                = 205;

    /**
     * HTTP Client Error
     */
    const HTTP_BAD_REQUEST                  = 400;
    const HTTP_UNAUTHORIZED                 = 401;
    const HTTP_PAYMENT_REQUIRED             = 402;
    const HTTP_FORBIDDEN                    = 403;
    const HTTP_NOT_FOUND                    = 404;
    const HTTP_METHOD_NOT_ALLOWED           = 405;
    const HTTP_NOT_ACCEPTABLE               = 406;
    const HTTP_REQUEST_TIMEOUT              = 408;
    const HTTP_CONFLICT                     = 409;
    const HTTP_GONE                         = 410;
    const HTTP_PAYLOAD_TOO_LARGE            = 413;
    const HTTP_IM_A_TEAPOT                  = 418;

    /**
     * HTTP Server Error
     */
    const HTTP_INTERNAL_SERVER_ERROR        = 500;
    const HTTP_NOT_IMPLEMENTED              = 501;
    const HTTP_BAD_GATEWAY                  = 502;
    const HTTP_SERVICE_UNAVAILABLE          = 503;
    const HTTP_GATEWAY_TIMEOUT              = 504;
    const HTTP_VERSION_NOT_SUPPORTED        = 505;

    /**
     * Other constants
     */
    const NEW_LINE = "\n";

    /**
     * @var Logger
     */
    protected $logger = null;


    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $defaultConfig = array(
        'username'              => null,
        'api_key'               => null,
        'base_uri'              => Client::DEFAULT_BASE_URI,
        'stream_uri'            => Client::DEFAULT_STREAM_URI,
        'api_version'           => Client::DEFAULT_API_VERSION,
        'user_agent'            => Client::DEFAULT_USER_AGENT,
        'ssl_verify'            => Client::DEFAULT_SSL_VERIFY,
        'debug'                 => Client::DEFAULT_DEBUG,
        'debug_request'         => Client::DEFAULT_DEBUG_REQUEST,
        'connection_timeout'    => Client::DEFAULT_CONNECTION_TIMEOUT,
        'log_level'             => Client::DEFAULT_LOG_LEVEL,
        'log_path'              => Client::DEFAULT_LOG_PATH
    );

    /**
     * @var array
     */
    protected $requiredConfig = array(
        'username', 'api_key'
    );

    /**
     *
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     *
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     *
     *
     * @param   array     $config
     * @return  array
     * @throws  InvalidDataError
     */
    protected function validateConfig(array $config)
    {
        $required = $this->getRequiredConfig();

        foreach($config as $k => $option) {
            $pos = array_search($k, $required);

            if ($pos !== false) {
                unset($required[$pos]);
            }
        }

        if (count($required) !== 0) {
            throw new InvalidDataError('Requires ' . implode(', ', $required));
        }

        $config = array_merge($this->getDefaultConfig(), $config);
        $config = array_intersect_key($config, $this->getDefaultConfig());

        if ($config['debug'] == true) {
            $config['log_level'] = Logger::DEBUG;
        }

        return $config;
    }

    /**
     *
     *
     * @param   array   $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     *
     *
     * @param   string  $key
     * @return  array
     */
    public function getConfig($key = null)
    {
        if($key !== null) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }

            return null;
        }

        return $this->config;
    }

    /**
     *
     *
     * @return  array
     */
    protected function getRequiredConfig()
    {
        return $this->requiredConfig;
    }

    /**
     *
     *
     * @return  array
     */
    protected function getDefaultConfig()
    {
        return $this->defaultConfig;
    }
}