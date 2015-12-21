<?php

class ReportPayload {
    const REPORT_URL = 'https://report.jpush.cn/v3/received';
    const MESSAGES_URL = 'https://report.jpush.cn/v3/messages';
    const USERS_URL = 'https://report.jpush.cn/v3/users';

    private $client;

    /**
     * ReportPayload constructor.
     * @param $client JPush
     */
    public function __construct($client)
    {
        $this->client = $client;
    }


    public function getReceived($msgIds) {
        $queryParams = '?msg_ids=';
        if (is_array($msgIds) && count($msgIds) > 0) {
            $isFirst = true;
            foreach ($msgIds as $msgId) {
                if ($isFirst) {
                    $queryParams .= $msgId;
                    $isFirst = false;
                } else {
                    $queryParams .= ',';
                    $queryParams .= $msgId;
                }
            }
        } else if (is_string($msgIds)) {
            $queryParams .= $msgIds;
        } else {
            throw new InvalidArgumentException("Invalid msg_ids");
        }

        $url = ReportPayload::REPORT_URL . $queryParams;

        $response = $this->client->_request($url, JPush::HTTP_GET);
        if($response['http_code'] === 200) {
            $body = array();
            $body['data'] = (array)json_decode($response['body']);
            $headers = $response['headers'];
            if (is_array($headers)) {
                $limit = array();
                $limit['rateLimitLimit'] = $headers['X-Rate-Limit-Limit'];
                $limit['rateLimitRemaining'] = $headers['X-Rate-Limit-Remaining'];
                $limit['rateLimitReset'] = $headers['X-Rate-Limit-Reset'];
                $body['limit'] = (object)$limit;
                return (object)$body;
            }
            return $body;
        } else {
            throw new APIRequestException($response);
        }

    }

    public function getMessages() {

    }

    public function getUsers() {

    }
}