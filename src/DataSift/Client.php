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
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

class Client extends BaseClient
{
    /**
     * @var HttpClient
     */
    protected $httpClient = null;

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
     * @param   array         $config
     * @param   HttpClient    $httpClient
     * @param   Logger        $logger
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
                'base_uri' => 'http://' . $config['base_uri'] . '/v' . $config['api_version'] . '/',
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

    /**
     *
     *
     * @param   HttpClient  $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     *
     *
     * @return  HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
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
            $response = $this->getHttpClient()->get($method, $headers);

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
            $response = $this->getHttpClient()->post($method, $headers);

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
            $response = $this->getHttpClient()->patch($method, $headers);

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
            $response = $this->getHttpClient()->put($method, $headers);

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
            $response = $this->getHttpClient()->get($method, $headers);

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