<?php
namespace App\Service;

use GuzzleHttp\Client;

class SteamCmdService
{
    /**
     * dequeue
     * @param  string   $query
     * @return string
     */
    public function dequeue(string $query): string
    {
        return $this->sendRequest('dequeue', $query);
    }

    /**
     * queueApp
     * @param  string   $query
     * @return string
     */
    public function queueApp(string $query): string
    {
        return $this->sendRequest('queue-app', $query);
    }

    /**
     * requeue
     * @param  string   $query
     * @return string
     */
    public function requeue(string $query): string
    {
        return $this->sendRequest('requeue', $query);
    }

    /**
     * searchApps
     * @param  string  $query
     * @return array
     */
    public function searchApps(string $query): array
    {
        $response = $this->sendRequest('search-apps', $query);

        $result = [];
        $split = preg_split("[\n]", $response);

        foreach ($split as $value) {
            $xpl = preg_split("/[\s]/", $value, 2);
            if (!empty($xpl) && isset($xpl[1])) {
                $result[$xpl[0]] = $xpl[1];
            }
        }

        return $result;
    }

    /**
     * showQueue
     * @param  string   $query
     * @return string
     */
    public function showQueue(string $query): string
    {
        return $this->sendRequest('show-queue', $query);
    }

    /**
     * startDownloading
     * @param  string   $query
     * @return string
     */
    public function startDownloading(string $query): string
    {
        return $this->sendRequest('start-downloading', $query);
    }

    /**
     * updateAppList
     * @param  string   $query
     * @return string
     */
    public function updateAppList(string $query): string
    {
        return $this->sendRequest('update-app-list', $query);
    }

    /**
     * @param  string   $target
     * @param  string   $query
     * @return string
     */
    protected function sendRequest(string $target, string $query): string
    {
        $guzzle = new Client();
        $response = $guzzle->request(
            'GET',
            'http://localhost:8000/' . $target . "?q=" . urlencode($query)
        );

        return $response->getBody();
    }

}
