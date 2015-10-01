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

class Source extends Base
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_DISABLED = 'disabled';
    const STATUS_DELETED = 'deleted';
    const STATUS_FAILED = 'failed';
    const STATUS_PROBLEMATIC = 'problematic';

    const DEFAULT_SOURCE_TYPE = null;

    const DEFAULT_STATUS = Source::STATUS_PAUSED;

    protected $id = null;

    protected $name = null;

    protected $sourceType = null;

    protected $status = Source::DEFAULT_STATUS;

    protected $parameters = array();

    protected $authentication = array();

    protected $resources = array();

    protected $createdAt = null;

    public function __construct($client, $data = array())
    {
        parent::__construct($client);
        $this->load($data);
    }

    private function load($data)
    {
        $map = array(
            'id' => 'setId',
            'name' => 'setName',
            'source_type' => 'setSourceType',
            'status' => 'setStatus',
            'parameters' => 'setParameters',
            'auth' => 'setAuth',
            'resources' => 'setResources',
            'created_at' => 'setCreatedAt'
        );

        foreach ($map as $k => $setter) {
            if (isset($data[$k])) {
                $this->$setter($data[$k]);
            }
        }

        return $this;
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    protected function getId()
    {
        return $this->id;
    }

    protected function setName($name)
    {
        $this->name = $name;
    }

    protected function getName()
    {
        return $this->name;
    }

    protected function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    protected function getSourceType()
    {
        return $this->sourceType;
    }

    protected function setStatus($status)
    {
        $this->status = $status;
    }

    protected function getStatus()
    {
        return $this->status;
    }

    protected function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    protected function getParameters()
    {
        return $this->parameters;
    }

    protected function setAuthentication(array $authentication)
    {
        $this->authentication = $authentication;
    }

    protected function getAuthentication()
    {
        return $this->authentication;
    }

    protected function setResources(array $resources)
    {
        $this->resources = $resources;
    }

    protected function getResources()
    {
        return $this->resources;
    }

    protected function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    protected function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function get($id)
    {
        $qs = array(
            'id' => $id
        );

        return new self($this->getClient(), $this->getClient()->get('source/get', $qs));
    }

    public function getAll(
        $page = Source::DEFAULT_PAGE,
        $perPage = Source::DEFAULT_PER_PAGE,
        $sourceType = Source::DEFAULT_SOURCE_TYPE
    ) {
        $qs = array(
            'page'      => $page,
            'per_page'  => $perPage
        );

        if ($sourceType) {
            $qs['source_type'] = $sourceType;
        }

        return $this->getClient()->get('source/get', $qs);
    }

    public function start()
    {
        $body = array(
            'id' => $this->getId()
        );

        return $this->getClient()->put('source/start', $body);
    }

    public function stop()
    {
        $body = array(
            'id' => $this->getId()
        );

        return $this->getClient()->put('source/stop', $body);
    }

    public function delete()
    {
        $body = array(
            'id' => $this->getId()
        );

        return $this->getClient()->put('source/stop', $body);
    }

    public function getLogs($page = Source::DEFAULT_PAGE, $perPage = Source::DEFAULT_PER_PAGE)
    {
        $body = array(
            'id' => $this->getId()
        );

        return $this->getClient()->get('source/log', $body);
    }
}