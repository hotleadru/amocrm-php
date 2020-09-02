<?php

namespace AmoCRM\Events;

use AmoCRM\Exception;
use AmoCRM\NetworkException;

class PhoneCall extends AbstractEvent
{
    const TYPE = 'phone_call';

    protected $oldApi = false;

    protected $fields = [
        'type',
        'phone_number',
        'users',
    ];

    /**
     * @param PhoneCall[] $events
     * @return array
     * @throws NetworkException|Exception
     */
    public function send($events = [])
    {
        $parameters = ['add' => []];

        foreach ($events AS $event) {
            $parameters['add'][] = array_merge(['type' => self::TYPE], $event->getValues());
        }

        $response = $this->postRequest('/api/v2/events/', $parameters);

        $result = [];
        if (isset($response['errors'])) {
            return $result;
        } elseif (isset($response['items'])) {
            $result = $response['items'];
        }

        return count($events) == 1 ? array_shift($result) : $result;
    }

    /**
     * @param string $response
     * @param array $info
     * @return array
     * @throws Exception
     */
    protected function parseResponse($response, $info)
    {
        $result = json_decode($response, true);

        if (floor($info['http_code'] / 100) >= 3) {
            if (isset($result['response']['status']) && $result['response']['status'] > 0) {
                $code = $result['response']['status'];
            } elseif ($result !== null) {
                $code = 0;
            } else {
                $code = $info['http_code'];
            }
            if ($this->v1 === false && isset($result['detail'])) {
                throw new Exception($result['detail'], $code);
            } elseif (isset($result)) {
                throw new Exception(json_encode($result));
            } else {
                throw new Exception('Invalid response body.', $code);
            }
        } elseif (!isset($result)) {
            throw new Exception('Invalid response body.');
        }

        return $result['_embedded'];
    }
}
