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
 * The DataSift\Account\Identity class defines identity entities endpoint.
 *
 * @package   DataSift Client for PHP
 * @author    Chris Knight <chris.knight@datasift.com>
 * @copyright 2015 MediaSift Ltd.
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://www.mediasift.com
 */
namespace DataSift\Account;
use DataSift\Base;

class Identity extends Base
{
    /**
     * Returns and identity
     *
     * @param string        $identity
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function get($identity)
    {
        return $this->getClient()->get('account/identity/' . $identity);
    }

    /**
     * Gets all the identities
     *
     * @param string        $label
     * @param integer       $page
     * @param integer       $perPage
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function getAll($label = null, $page = 1, $perPage = 25)
    {
        $qs = array(
            'page' => $page,
            'per_page' => $perPage
        );

        return $this->getClient()->get('account/identity', $qs);
    }

    /**
     * Creates an identity
     *
     * @param string        $label
     * @param bool          $master
     * @param string        $status
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function create($label, $master = false, $status = 'active')
    {
        $body = array(
            'label'     => $label,
            'master'    => $master,
            'status'    => $status
        );

        return $this->getClient()->post('account/identity', $body);
    }

    /**
     * Updates an identity
     *
     * @param string        $identity
     * @param string        $label
     * @param boolean       $master
     * @param string        $status
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function update($identity, $label = null, $master = null, $status = null)
    {
        $body = array(
            'label'     => $label,
            'master'    => $master,
            'status'    => $status
        );

        foreach ($body as $k => $v) {
            if ($v == null) {
                unset($body[$k]);
            }
        }

        return $this->getClient()->put('account/identity/' . $identity, $body);
    }

    /**
     * Deletes an identity
     *
     * @param string        $identity
     * @return array|bool
     * @throws \DataSift\Exception\APIError
     */
    public function delete($identity)
    {
        return $this->getClient()->delete('account/identity/' . $identity);
    }
}
