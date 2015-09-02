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
    public function get($id)
    {
        $qs = array(
            'id' => $id
        );

        return $this->getClient()->get('source/get', $qs);
    }

    public function getAll($page = 1, $perPage = 25, $sourceType = null)
    {
        $qs = array(
            'page'      => $page,
            'per_page'  => $perPage
        );

        return $this->getClient()->get('source/get', $qs);
    }

}