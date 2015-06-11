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

class Pylon extends Base
{
    protected $name;
    protected $csdl;
    protected $hash;
    protected $status;
    protected $start;
    protected $end;
    protected $volume;
    protected $createdAt;
    protected $dpu;

    protected $defaultAttributes = array(
        'name', 'csdl', 'hash', 'status', 'start', 'end',
        'volume', 'created_at', 'dpu'
    );

    public function __construct(Client $client, array $attributes = array())
    {
        parent::__construct($client);
    }

    protected function load(array $attributes)
    {
        if (count($attributes) == 0) {
            // should be invaliddata
            throw new APIError('');
        }

        foreach ($attributes as $key => $attribute) {

        }
    }
}