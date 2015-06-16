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

use DataSift\Exception\InvalidDataError;

class Pylon extends Base
{
    protected $name;
    protected $csdl;
    protected $hash;
    protected $status;
    protected $start;
    protected $end;
    protected $volume;
    protected $defaultAttributes = array(
        'name',
        'csdl',
        'hash',
        'status',
        'start',
        'end',
        'volume',
    );

    /**
     *
     *
     * @param Client $client
     * @param array $attributes
     */
    public function __construct(Client $client, array $attributes = array())
    {
        parent::__construct($client);

        if (count($attributes) > 0) {
            $this->load($attributes);
        }
    }

    /**
     *
     *
     * @param array $attributes
     */
    protected function load(array $attributes)
    {
        foreach ($attributes as $key => $attribute) {
            $key = ucwords(str_replace('_', ' ', $key));
            $key = str_replace(' ', '', lcfirst($key));

            $this->$key = $attribute;
        }
    }

    /**
     *
     *
     * @param $attributes
     * @return Pylon
     */
    protected function getObject($attributes)
    {
        return new self($this->getClient(), $attributes);
    }

    /**
     *
     *
     * @param string $hash
     * @return array|bool
     * @throws Exception\APIError
     */
    public function get($hash = false)
    {
        if ($hash) {
            $this->setHash($hash);
        }

        $qs = array(
            'hash' => $this->getHash()
        );

        return $this->getClient()->get('pylon/get', $qs);
    }

    /**
     *
     *
     * @param integer $page
     * @param integer $perPage
     * @param string $orderBy
     * @param string $orderDir
     *
     * @return array
     *
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function getAll($page = 1, $perPage = 25, $orderBy = 'id', $orderDir = 'asc')
    {
        if ($page < 1) {
            throw new InvalidDataError('The page number is invalid');
        }

        if($perPage < 1) {
            throw new InvalidDataError('The perPage number is invalid');
        }

        $qs = array(
            'page'      => $page,
            'per_page'  => $perPage,
            'order_by'  => $orderBy,
            'order_dir' => $orderDir
        );

        $results = $this->getClient()->get('pylon/get', $qs);

        $return = array(
            'analyzes' => array()
        );

        foreach ($results as $pylon) {
            $return['analyzes'][] = $this->getObject($pylon);
        }

        return $return;
    }

    /**
     *
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     *
     * @param $csdl
     */
    public function setCsdl($csdl)
    {
        $this->csdl = $csdl;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getCsdl()
    {
        return $this->csdl;
    }

    /**
     *
     *
     * @param $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     *
     *
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     *
     *
     * @param string $csdl
     * @return array|bool
     * @throws Exception\APIError
     */
    public function validate($csdl = false)
    {
        $body = array('csdl' => $csdl);

        return $this->getClient()->post('pylon/validate', $body, array(Client::HTTP_OK));
    }

    /**
     *
     *
     * @param string $csdl
     * @return array|bool
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function compile($csdl = false)
    {
        if($csdl !== false) {
            $this->setCSDL($csdl);
        }

        if(strlen($this->getCSDL()) === 0) {
            throw new InvalidDataError('');
        }

        $body = array('csdl' => $this->getCSDL());

        $result = $this->getClient()->post('pylon/compile', $body, array(Client::HTTP_OK));
        $this->setHash($result['hash']);
        return $result;
    }

    /**
     *
     *
     * @param string $hash
     * @param string $name
     * @return array|bool
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function start($hash = false, $name = false)
    {
        if ($hash !== false) {
            $this->setHash($hash);
        }

        if ($name !== false) {
            $this->setName($name);
        }

        if (strlen($this->getHash()) == 0) {
            throw new InvalidDataError('Cannot start a recording without a hash');
        }

        $body = array('hash' => $this->getHash());

        if (!empty($this->getName())) {
            $body['name'] = $this->getName();
        }

        return $this->getClient()->post('pylon/start', $body);
    }

    /**
     *
     *
     * @param string $hash
     * @return array|bool
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function stop($hash = false)
    {
        if ($hash !== false) {
            $this->setHash($hash);
        }

        if(strlen($this->getHash()) == 0) {
            throw new InvalidDataError('Cannot stop a recording without a hash');
        }

        $body = array(
            'hash' => $this->getHash()
        );

        return $this->getClient()->post('pylon/stop', $body);
    }

    /**
     *
     *
     * @param $params
     * @param bool $filter
     * @param bool $start
     * @param bool $end
     * @param bool $hash
     * @return array|bool
     *
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function analyze($params, $filter = false, $start = false, $end = false, $hash = false)
    {
        if($hash) {
            $this->setHash($hash);
        }

        //If parameters is not an array try and decode it
        if (!is_array($params)) {
            $params = json_decode($params);
        }

        if (empty($params)) {
            throw new InvalidDataError('Parameters must be supplied as an array or valid JSON');
        }

        $body = array(
            'hash'       => $this->getHash(),
            'parameters' => $params
        );

        if ($filter) {
            $body['filter'] = $filter;
        }

        if ($start) {
            $body['start'] = $start;
        }

        if ($end) {
            $body['end'] = $end;
        }

        return $this->getClient()->post('pylon/analyze', $body);
    }

    /**
     *
     *
     * @param string $hash
     * @return array|bool
     * @throws Exception\APIError
     * @throws InvalidDataError
     */
    public function tags($hash = false)
    {
        if($hash) {
            $this->setHash($hash);
        }

        if (strlen($this->getHash()) == 0) {
            throw new InvalidDataError('Cannot analyze tags without a hash');
        }

        $qs = array(
            'hash' => $this->getHash()
        );

        return $this->getClient()->get('pylon/tags', $qs);

    }
}