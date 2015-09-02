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

/**
 * The DataSift\Core class defines core functionality.
 *
 * @package   DataSift Client for PHP
 * @author    Chris Knight <chris.knight@datasift.com>
 * @copyright 2015 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

namespace DataSift;

class Core extends Base
{
    /**
     *
     *
     * @param string        $csdl
     * @return array|bool
     * @throws Exception\APIError
     */
    public function validate($csdl)
    {
        $body = array(
            'csdl' => $csdl
        );

        return $this->getClient()->post('validate', $body, array(Client::HTTP_OK));
    }

    /**
     *
     *
     * @param  string     $csdl
     * @return array|bool
     * @throws Exception\APIError
     */
    public function compile($csdl)
    {
        $body = array(
            'csdl' => $csdl
        );

        return $this->getClient()->post('compile', $body, array(Client::HTTP_OK));
    }

    /**
     *
     *
     * @param string $period
     * @return array|bool
     * @throws Exception\APIError
     */
    public function usage($period = 'hour')
    {
        return $this->getClient()->get('usage', array('period' => $period));
    }

    /**
     *
     *
     * @param null $hash
     * @param null $historicsId
     * @return array|bool
     * @throws Exception\APIError
     */
    public function dpu($hash = null , $historicsId = null)
    {
        $qs = array('hash' => $hash);

        if($hash === null) {
            $qs = array('historics_id' => $historicsId);
        }

        return $this->getClient()->get('dpu', $qs);
    }

    /**
     *
     *
     * @return array|bool
     * @throws Exception\APIError
     */
    public function balance()
    {
        return $this->getClient()->get('balance');
    }
}