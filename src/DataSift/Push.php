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

class Push extends Base
{
    public function get()
    {

    }

    public function getAll()
    {

    }

    public function validate($outputType, $outputParams)
    {
        $body = array(
            'output_type' => $outputType,
            'output_params' => $outputParams
        );

        return $this->getClient()->post('push/validate', $body);
    }

    public function create($hash, $historicsId, $name, $outputType, $outputParams, $initialStatus)
    {
        $body = array(
            'hash' => $hash
        );

        return $this->getClient()->post('push/create', $body);
    }

    public function pause()
    {

    }

    public function resume()
    {

    }

    public function update()
    {

    }

    public function stop()
    {

    }

    public function delete()
    {

    }

    public function getLogs()
    {

    }

    public function pull()
    {

    }
}