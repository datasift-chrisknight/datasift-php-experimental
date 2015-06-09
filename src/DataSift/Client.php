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
use GuzzleHttp\Client as HttpClient;
use DataSift\Exception\APIError;

class Client {
    const USER_AGENT = 'DataSiftPHP/3.0.0-alpha';

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 404;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;

    const NEW_LINE = "\n";

    /**
     * @var CurlClient
     */
    protected $client = null;

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
        'base_uri'              => 'api.datasift.com',
        'verify'                => true,
        'debug'                 => false,
        'timeout'               => 5
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
     * @throws APIError
     */
    public function __construct($config, HttpClient $client = null)
    {
        $config = $this->validateConfig($config);
        $this->setConfig($config);

        if($client === null) {
            $client = new HttpClient(array(
                'base_uri'  => 'https://' . $config['base_uri'] . '/',
                'headers'   => array(
                    'User-Agent'    => Client::USER_AGENT
                ),
                'timeout'   => $config['timeout']
            ));
        }

        $this->setClient($client);
    }

    /**
     *
     *
     * @param array $config
     * @return array
     * @throws APIError
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
            throw new APIError('Requires ' . implode(', ', $required));
        }

        $config = array_merge($this->getDefaultConfig(), $config);
        $config = array_intersect_key($config, $this->getDefaultConfig());

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
        if($key !== null && isset($this->config[$key])) {
            return $this->config[$key];
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
    public function get($method, array $qs = null, array $successCode = array(Client::HTTP_OK))
    {
        try {
            $headers = $this->buildHeaders(array('query' => $qs));
            $response = $this->getClient()->get($method, $headers);

            return $this->processResponse($response, $successCode);
        } catch (\Exception $e) {
            throw new APIError($e->getMessage());
        }
    }

    /**
     * @param string $method
     * @param array $body
     * @param array $successCode
     * @return array|bool
     * @throws APIError
     */
    public function post($method, array $body, array $successCode = array(Client::HTTP_CREATED))
    {
        try {
            $headers = $this->buildHeaders(array('body' => $body), 'application/json');
            $response = $this->getClient()->post($method, $headers);

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
    public function patch($method, array $body, array $successCode = array(Client::HTTP_OK))
    {
        try {
            $headers = $this->buildHeaders(array('body' => $body), 'application/json');
            $response = $this->getClient()->patch($method, $headers);

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
    public function put($method, array $body, array $successCode = array(Client::HTTP_OK))
    {
        try {
            $headers = $this->buildHeaders(array('body' => $body), 'application/json');
            $response = $this->getClient()->put($method, $headers);

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
    public function delete($method, array $successCode = array(Client::HTTP_NO_CONTENT))
    {
        try {
            $headers = $this->buildHeaders();
            $response = $this->getClient()->get($method, $headers);

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
    protected function buildHeaders(array $additionalHeaders = null, $contentType = null)
    {
        $headers = array(
            'headers' => array(
                'Auth' => $this->getConfig('username') . ':' . $this->getConfig('api_key'),
                'Expects' => ''
            ),
        );

        if($contentType !== null) {
            $headers['headers']['Content-Type'] = $contentType;
        }

        if($additionalHeaders !== null) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        if($this->getConfig('debug')) {
            $headers['debug'] = $this->getConfig('debug');
        }

        if($this->getConfig('verify')) {
            $headers['verify'] = $this->getConfig('verify');
        }

        return $headers;
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
        $rateLimit = -1;
        $rateLimitRemaining = -1;

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

        if (in_array($response->getStatusCode(), $successCode)) {
            return $this->decodeBody($response);
        }

        return $this->processError($response);
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
                return $body;
            }

            return json_decode($response->getBody(), true);
        }

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
        $error = json_decode($response->getBody(), true);

        //do this better
        return array(
            'response_code' => $error['response_code'],
            'error' => $error['data']['error']
        );
    }
}
