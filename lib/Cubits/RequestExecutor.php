<?php

namespace Cubits;

class RequestExecutor
{

    /**
     * @param $curl
     * @return array
     * @throws ApiException
     * @throws ConnectionException
     */
    public function executeRequest($curl)
    {
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_errno($curl);
            $message = curl_error($curl);
            curl_close($curl);
            throw new ConnectionException("Network error " . $message . " (" . $error . ")");
        }

        // Check status code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (($statusCode != 200) && ($statusCode != 201)) {
            throw new ApiException("Status code " . $statusCode, $statusCode, $response);
        }

        return array("statusCode" => $statusCode, "body" => $response);
    }

}
