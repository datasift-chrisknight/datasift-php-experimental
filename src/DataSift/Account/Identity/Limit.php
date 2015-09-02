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
 * The DataSift\Account\Identity\Limit class defines identity limits endpoint.
 *
 * @package   DataSift Client for PHP
 * @author    Chris Knight <chris.knight@datasift.com>
 * @copyright 2015 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

namespace DataSift\Account\Identity;
use DataSift\Base;

class Limit extends Base
{
    /**
     * Returns the limit for a service
     *
     * @param string        $identity
     * @param string        $service
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function get($identity, $service)
    {
        return $this->getClient()->get('account/identity/' . $identity . '/limit/' . $service);
    }

    /**
     * Get all the limits for a service
     *
     * @param string        $service
     * @param integer       $page
     * @param integer       $perPage
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function getAll($service, $page = 1, $perPage = 25)
    {
        $qs = array(
            'page' => $page,
            'per_page' => $perPage
        );

        return $this->getClient()->get('account/identity/limit/' . $service, $qs);
    }

    /**
     * Create a limit for a service
     *
     * @param string        $identity
     * @param string        $service
     * @param integer       $totalAllowance
     *
     * @return mixed
     * @throws \DataSift\Exception\APIError
     */
    public function create($identity, $service, $totalAllowance)
    {
        $body = array(
            'service'           => $service,
            'total_allowance'   => $totalAllowance
        );

        return $this->getClient()->post('account/identity/' . $identity . '/limit', $body);
    }

    /**
     * Update the limit for an service
     *
     * @param string        $identity
     * @param string        $service
     * @param integer       $totalAllowance
     * @return mixed
     * @throws \DataSift\Exception\APIError
     */
    public function update($identity, $service, $totalAllowance)
    {
        $body = array(
            'total_allowance'   => $totalAllowance
        );

        return $this->getClient()->put('account/identity/' . $identity . '/limit/' . $service, $body);
    }

    /**
     * Delete the limit for an service
     *
     * @param string        $identity
     * @param string        $service
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function delete($identity, $service)
    {
        return $this->getClient()->delete('account/identity/' . $identity . '/limit/' . $service);
    }
}
