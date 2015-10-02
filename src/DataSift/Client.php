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
use DataSift\Exception\InvalidDataError;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

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
     * @var HttpClient
     */
    protected $client = null;

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
     * @var integer
     */
    protected $rateLimit = null;

    /**
     * @var integer
     */
    protected $rateLimitRemaining = null;

    /**
     *
     *
     * @param array $config
     * @param HttpClient $client
     * @param Logger $logger
     * @throws APIError
     */
    public function __construct(
        array $config,
        HttpClient $client = null,
        Logger $logger = null
    ) {
        $config = $this->validateConfig($config);
        $this->setConfig($config);

        if ($client === null) {
            $client = new HttpClient(array(
                'base_uri' => 'http://' . $config['base_uri'] . '/v' . $config['api_version'] . '/',
                'headers' => array(
                    'User-Agent' => $config['user_agent']
                ),
                'timeout' => $config['timeout']
            ));
        }

        if ($logger === null) {
            $logger = new Logger('log');
        }

        $logger->pushHandler(new StreamHandler($config['log_path'], $config['log_level']));

        $this->setClient($client);
        $this->setLogger($logger);
    }

    /**
     *
     *
     * @param array $config
     * @return array
     * @throws InvalidDataError
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
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     *
     *
     * @param string $key
     * @return array
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
     * @return array
     */
    protected function getRequiredConfig()
    {
        return $this->requiredConfig;
    }

    /**
     *
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     *
     *
     * @param HttpClient $client
     */
    public function setClient(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     *
     *
     * @return HttpClient
     */
    public function getClient()
    {
        return $this->client;
    }

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
     * @param integer $rateLimit
     */
    public function setRateLimit($rateLimit)
    {
        $this->rateLimit = $rateLimit;
    }

    /**
     *
     *
     * @return integer
     */
    public function getRateLimit()
    {
        return $this->rateLimit;
    }

    /**
     *
     *
     * @param integer $rateLimitRemaining
     */
    public function setRateLimitRemaining($rateLimitRemaining)
    {
        $this->rateLimitRemaining = $rateLimitRemaining;
    }

    /**
     *
     *
     * @return integer
     */
    public function getRateLimitRemaining()
    {
        return $this->rateLimitRemaining;
    }

    /**
     *
     *
     * @param string $method
     * @param array $qs
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     */
    public function get(
        $method,
        array $qs = null,
        array $successCode = array(Client::HTTP_OK)
    ) {
        try {
            $headers = $this->buildHeaders(array('query' => $qs));
            $response = $this->getClient()->get($method, $headers);

            $this->getLogger()->addDebug('DataSift\Client->get', array($method, $headers));

            return $this->processResponse($response, $successCode);
        } catch (ClientException $response) {
            $this->getLogger()->addWarning('DataSift\Client->get', array($method, $headers, $response));

            return $this->decodeException($response);
        } catch (\Exception $e) {
            $this->getLogger()->addError('DataSift\Client->get', array($method, $headers, $e));

            throw new APIError($e->getMessage());
        }
    }

    /**
     *
     *
     * @param string $method
     * @param array $body
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     */
    public function post(
        $method,
        array $body,
        array $successCode = array(Client::HTTP_CREATED)
    ) {
        try {
            $headers = $this->buildHeaders(array('json' => $body), 'application/json');
            $response = $this->getClient()->post($method, $headers);

            $this->getLogger()->addDebug('DataSift\Client->post', array($method, $headers));

            return $this->processResponse($response, $successCode);
        } catch (\Exception $e) {
            throw new APIError($e->getMessage());
        }
    }

    /**
     *
     *
     * @param string $method
     * @param array $body
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     * @todo work this out, http codes and whatnot
     */
    public function patch(
        $method,
        array $body,
        array $successCode = array(Client::HTTP_OK)
    ) {
        try {
            $headers = $this->buildHeaders(array('json' => $body), 'application/json');
            $response = $this->getClient()->patch($method, $headers);

            $this->getLogger()->addDebug('DataSift\Client->patch', array($method, $headers));

            return $this->processResponse($response, $successCode);
        } catch (\Exception $e) {
            throw new APIError($e->getMessage());
        }
    }

    /**
     *
     *
     * @param string $method
     * @param array $body
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     */
    public function put(
        $method,
        array $body,
        array $successCode = array(Client::HTTP_OK)
    ) {
        try {
            $headers = $this->buildHeaders(array('json' => $body), 'application/json');
            $response = $this->getClient()->put($method, $headers);

            $this->getLogger()->addDebug('DataSift\Client->put', array($method, $headers));

            return $this->processResponse($response, $successCode);
        } catch (\Exception $e) {
            throw new APIError($e->getMessage());
        }
    }

    /**
     *
     *
     * @param $method
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     */
    public function delete(
        $method,
        array $successCode = array(Client::HTTP_NO_CONTENT)
    ) {
        try {
            $headers = $this->buildHeaders();
            $response = $this->getClient()->get($method, $headers);

            $this->getLogger()->addDebug('DataSift\Client->delete', array($method, $headers));

            return $this->processResponse($response, $successCode);
        } catch (\Exception $e) {
            throw new APIError($e->getMessage());
        }
    }

    /**
     *
     *
     * @param array $additionalHeaders
     * @param string $contentType
     * @return array
     */
    protected function buildHeaders(
        array $additionalHeaders = null,
        $contentType = null
    ) {
        $headers = array(
            'headers' => array(
                'Auth' => $this->getConfig('username') . ':' . $this->getConfig('api_key'),
                'Expects' => ''
            ),
        );

        if ($contentType !== null) {
            $headers['headers']['Content-Type'] = $contentType;
        }

        if ($additionalHeaders !== null) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        if ($this->getConfig('debug_request')) {
            $headers['debug'] = $this->getConfig('debug_request');
        }

        if ($this->getConfig('verify')) {
            $headers['verify'] = $this->getConfig('verify');
        }

        return $headers;
    }

    protected function processLimits($response)
    {
        $rateLimit = ($this->getRateLimit() ? $this->getRateLimit() : -1);
        $rateLimitRemaining = ($this->getRateLimitRemaining() ? $this->getRateLimitRemaining() : -1);

        if ($response->getHeader('x-ratelimit-limit')) {
            $rateLimit = $response->getHeader('x-ratelimit-limit');
            $rateLimit = $rateLimit[0];
        }

        if ($response->getHeader('x-ratelimit-remaining')) {
            $rateLimitRemaining = $response->getHeader('x-ratelimit-remaining');
            $rateLimitRemaining = $rateLimitRemaining[0];
        }

        $this->setRateLimit($rateLimit);
        $this->setRateLimitRemaining($rateLimitRemaining);

        $this->getLogger()->addDebug('DataSift\Client->processLimits', array($rateLimit, $rateLimitRemaining));
    }

    /**
     *
     *
     * @param $response
     * @param $successCode
     * @return array|boolean
     */
    protected function processResponse($response, $successCode)
    {
        $this->processLimits($response);

        if (in_array($response->getStatusCode(), $successCode)) {
            return $this->decodeBody($response);
        }

        return $this->decodeError($response);
    }

    /**
     *
     *
     * @param $response
     * @return array|bool
     */
    protected function decodeBody($response)
    {
        if (strlen($response->getBody())) {
            $format = $response->getHeader('content-type');

            if ($response->getHeader('x-datasift-format')) {
                $format = $response->getHeader('x-datasift-format');
            };

            if (in_array('json_new_line', $format)) {
                $body = array();
                $parts = explode(Client::NEW_LINE, $response->getBody());

                foreach ($parts as $json) {
                    $body[] = json_decode($json, true);
                }

                $this->getLogger()->addDebug('DataSift\Client->decodeBody', array($format, $body));
                return $body;
            }

            $body = json_decode($response->getBody(), true);
            $this->getLogger()->addDebug('DataSift\Client->decodeBody', array($format, $body));
            return $body;
        }

        $this->getLogger()->addDebug('DataSift\Client->decodeBody', array($response->getBody()));
        return true;
    }

    /**
     *
     *
     * @param $response
     * @return array
     */
    protected function decodeError($response)
    {
        $this->processLimits($response);
        $error = json_decode($response->getBody(), true);

        //do this better
        return array(
            'response_code'     => $error['response_code'],
            'error'             => $error['data']['error']
        );
    }

    /**
     * @param $exception
     * @return array
     */
    protected function decodeException($exception)
    {
        $response = $exception->getResponse();
        $request = $exception->getRequest();
        $this->processLimits($response);

        $this->getLogger()->addError('DataSift\Client->decodeException', array(
            'response_code'     => $response->getStatusCode(),
            'reason_phrase'     => $response->getReasonPhrase(),
            'uri'               => $request->getUri()->__toString(),
            'headers'           => $request->getHeaders(),
            'body'              => $request->getBody()->__toString()
        ));

        return array(
            'response_code'     => $response->getStatusCode(),
            'error'             => $response->getReasonPhrase()
        );
    }
}