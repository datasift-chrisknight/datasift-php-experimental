<?php
/**
 * DataSift client
 *
 * This software is the intellectual property of MediaSift Ltd., and is covered
 * by retained intellectual property rights, including copyright.
 * Distribution of this software is strictly forbidden under the terms of this license.
 *
 * @category  DataSift
 * @package   PHP-client
 * @author    Stuart Dallas <stuart@3ft9.com>
 * @copyright 2011 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

/**
 * The DataSift_Account_Identity class defines identity entities endpoint.
 *
 * @category DataSift
 * @package  PHP-client
 * @author   Chris Knight <chris.knight@datasift.com>
 * @license  http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link     http://www.mediasift.com
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
