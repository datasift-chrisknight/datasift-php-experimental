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
 * The DataSift\Account\Identity\Token class defines identity tokens endpoint.
 *
 * @package   DataSift Client for PHP
 * @author    Chris Knight <chris.knight@datasift.com>
 * @copyright 2015 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */

namespace DataSift\Account\Identity;
use DataSift\Base;
use DataSift\Client;

class Token extends Base
{
    /**
     * Gets the token for a service
     *
     * @param string        $identity
     * @param string        $service
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function get($identity, $service)
    {
        return $this->getClient()->get('account/identity/' . $identity . '/token/' . $service);
    }

    /**
     * Get all the tokens for an identity
     *
     * @param string        $identity
     * @param integer       $page
     * @param integer       $perPage
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function getAll($identity, $page = 1, $perPage = 25)
    {
        $qs = array(
            'page'          => $page,
            'per_page'      => $perPage
        );

        return $this->getClient()->get('account/identity/' . $identity . '/token', $qs);
    }

    /**
     * Creates a token for a service
     *
     * @param string        $identity
     * @param string        $service
     * @param string        $token
     *
     * @return mixed
     */
    public function create($identity, $service, $token)
    {
        $body = array(
            'service'       => $service,
            'token'         => $token,
        );

        return $this->getClient()->post('account/identity/' . $identity . '/token', $body);
    }

    /**
     * Updates the token for a service
     *
     * @param string        $identity
     * @param string        $service
     * @param string        $token
     * @return mixed
     */
    public function update($identity, $service, $token)
    {
        $successCode = array(
            Client::HTTP_CREATED,
            Client::HTTP_OK
        );

        $body = array(
            'token' => $token,
        );

        return $this->getClient()->put('account/identity/' . $identity . '/token/' . $service, $body, $successCode);
    }

    /**
     * Deletes a token for a service
     *
     * @param $identity
     * @param $service
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function delete($identity, $service)
    {
        return $this->getClient()->delete('account/identity/' . $identity . '/token/' . $service);
    }
}
