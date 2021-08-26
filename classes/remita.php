<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Paystack enrolment plugin.
 *
 * This plugin allows you to set up paid courses.
 *
 * @category Enrol_Plugin
 *
 * @package Enrol_Remita
 *
 * @author Adetunji Oyebanji <tunji.oyebanji2015@gmail.com>
 *
 * @copyright 2021 Adetunji Oyebanji
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @link none
 */

namespace enrol_remita;

// defined('MOODLE_INTERNAL') || die();

class remita
{
    public $merchantId;
    public $apiKey;
    public $serviceTypeId;
    public $url;
    public $amount;
    public $order_id;
    /**
     * Undocumented function
     *
     * @param string $merchantId    The merchant ID
     * @param string $apiKey        The api key
     * @param [type] $serviceTypeId The service type
     * @param [type] $url           Remita's endpoint
     */
    public function __construct($merchantId, $apiKey, $serviceTypeId, $url)
    {
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
        $this->serviceTypeId = $serviceTypeId;
        $this->url = $url;
    }

    /**
     * Set additional data
     *
     * @param $data array
     *
     * @return void
     */
    public function setData($data)
    {
        foreach ($data as $key => $datum) {
            $this->{$key} = $datum;
        }
    }

    /**
     * Verify transaction
     *
     * @return object
     */
    public function verify($rrr)
    {
        $apiHash = hash("sha512", $rrr . $this->apiKey . $this->merchantId);
        // $rrr = intval($rrr);
        $uri = "remita/exapp/api/v1/send/api/echannelsvc";
        $uri .= "/{$this->merchantId}/{$rrr}/{$apiHash}/status.reg";
        $url = $this->url . $uri;

        $curl = curl_init();
        curl_setopt_array(
            $curl, [
                CURLOPT_URL => $this->url . "/$uri",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "authorization: remitaConsumerKey=" . $this->merchantId . ",remitaConsumerToken=" . $apiHash,
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ]
        );

        $request = curl_exec($curl);
        $res = json_decode($request, true);

        if (curl_errno($curl)) {
            throw new moodle_exception(
                'errorremitaconnect',
                'enrol_remita',
                '',
                array('url' => $url, 'response' => $res),
            );
        }

        curl_close($curl);

        return $res;

    }

}