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

class Historics extends Base
{
    const DEFAULT_SAMPLE_SIZE = 100;

    public function get($id)
    {
        $qs = array(
            'id' => $id
        );

        return $this->getClient()->get('historics/get', $qs);
    }

    public function getAll()
    {
        return $this->getClient()->get('historics/get');
    }

    public function prepare(
        $hash,
        $start,
        $end,
        $name,
        $sources,
        $sample = Historics::DEFAULT_SAMPLE_SIZE
    ) {
        $body = array(
            'hash' => $hash,
            'start' => $start,
            'end' => $end,
            'name' => $name,
            'sources' => implode(',', $sources),
            'sample' => $sample
        );

        return $this->getClient()->post('historics/prepare', $body, array(Client::HTTP_OK));
    }
}