<?php
/*
 * Copyright (c) 2022 MynaParrot
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Mynaparrot\Plugnmeet;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mynaparrot\Plugnmeet\Parameters\CreateRoomParameters;
use Mynaparrot\Plugnmeet\Parameters\DeleteRecordingParameters;
use Mynaparrot\Plugnmeet\Parameters\EndRoomParameters;
use Mynaparrot\Plugnmeet\Parameters\FetchRecordingsParameters;
use Mynaparrot\Plugnmeet\Parameters\GenerateJoinTokenParameters;
use Mynaparrot\Plugnmeet\Parameters\RecordingDownloadTokenParameters;
use Mynaparrot\Plugnmeet\Parameters\GetActiveRoomInfoParameters;
use Mynaparrot\Plugnmeet\Parameters\IsRoomActiveParameters;
use Mynaparrot\Plugnmeet\Responses\ClientFilesResponses;
use Mynaparrot\Plugnmeet\Responses\CreateRoomResponse;
use Mynaparrot\Plugnmeet\Responses\DeleteRecordingResponse;
use Mynaparrot\Plugnmeet\Responses\EndRoomResponse;
use Mynaparrot\Plugnmeet\Responses\FetchRecordingsResponse;
use Mynaparrot\Plugnmeet\Responses\GenerateJoinTokenResponse;
use Mynaparrot\Plugnmeet\Responses\GetActiveRoomInfoResponse;
use Mynaparrot\Plugnmeet\Responses\GetActiveRoomsInfoResponse;
use Mynaparrot\Plugnmeet\Responses\IsRoomActiveResponse;
use Mynaparrot\Plugnmeet\Responses\RecordingDownloadTokenResponse;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class PlugNmeet {
    /**
     * @var string
     */
    protected $serverUrl;
    /**
     * @var string
     */
    protected $apiKey;
    /**
     * @var string
     */
    protected $apiSecret;
    /**
     * @var string
     */
    protected $defaultPath = "/auth";

    /**
     * @param $serverUrl plugNmeet server URL
     * @param $apiKey plugNmeet API_Key
     * @param $apiSecret plugNmeet API_Secret
     */
    public function __construct($serverUrl, $apiKey, $apiSecret) {
        $this->serverUrl = $serverUrl;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    /**
     * Create new room
     * @param CreateRoomParameters $createRoomParameters
     * @return CreateRoomResponse
     */
    public function createRoom(CreateRoomParameters $createRoomParameters): CreateRoomResponse {
        $body = $createRoomParameters->buildBody();
        $output = $this->sendRequest("/room/create", $body);
        return new CreateRoomResponse($output);
    }

    /**
     * Generate join token
     * @param GenerateJoinTokenParameters $generateJoinTokenParameters
     * @return GenerateJoinTokenResponse
     */
    public function getJoinToken(GenerateJoinTokenParameters $generateJoinTokenParameters): GenerateJoinTokenResponse {
        $body = $generateJoinTokenParameters->buildBody();
        $output = $this->sendRequest("/room/getJoinToken", $body);
        return new GenerateJoinTokenResponse($output);
    }

    /**
     * To check if room is active or not
     * @param IsRoomActiveParameters $isRoomActiveParameters
     * @return IsRoomActiveResponse
     */
    public function isRoomActive(IsRoomActiveParameters $isRoomActiveParameters): IsRoomActiveResponse {
        $body = $isRoomActiveParameters->buildBody();
        $output = $this->sendRequest("/room/isRoomActive", $body);
        return new IsRoomActiveResponse($output);
    }

    /**
     * Get active room information
     * @param GetActiveRoomInfoParameters $getActiveRoomInfoParameters
     * @return GetActiveRoomInfoResponse
     */
    public function getActiveRoomInfo(GetActiveRoomInfoParameters $getActiveRoomInfoParameters): GetActiveRoomInfoResponse {
        $body = $getActiveRoomInfoParameters->buildBody();
        $output = $this->sendRequest("/room/getActiveRoomInfo", $body);
        return new GetActiveRoomInfoResponse($output);
    }

    /**
     * Get all active rooms
     * @return GetActiveRoomsInfoResponse
     */
    public function getActiveRoomsInfo(): GetActiveRoomsInfoResponse {
        $output = $this->sendRequest("/room/getActiveRoomsInfo", []);
        return new GetActiveRoomsInfoResponse($output);
    }

    /**
     * End active room
     * @param EndRoomParameters $endRoomParameters
     * @return EndRoomResponse
     */
    public function endRoom(EndRoomParameters $endRoomParameters) {
        $body = $endRoomParameters->buildBody();
        $output = $this->sendRequest("/room/endRoom", $body);
        return new EndRoomResponse($output);
    }

    /**
     * To fetch recordings
     * @param FetchRecordingsParameters $fetchRecordingsParameters
     * @return FetchRecordingsResponse
     */
    public function fetchRecordings(FetchRecordingsParameters $fetchRecordingsParameters): FetchRecordingsResponse {
        $body = $fetchRecordingsParameters->buildBody();
        $output = $this->sendRequest("/recording/fetch", $body);
        return new FetchRecordingsResponse($output);
    }

    /**
     * To delete recording
     * @param DeleteRecordingParameters $deleteRecordingParameters
     * @return DeleteRecordingResponse
     */
    public function deleteRecordings(DeleteRecordingParameters $deleteRecordingParameters): DeleteRecordingResponse {
        $body = $deleteRecordingParameters->buildBody();
        $output = $this->sendRequest("/recording/delete", $body);
        return new DeleteRecordingResponse($output);
    }

    /**
     * Generate token to download recording
     * @param RecordingDownloadTokenParameters $recordingDownloadTokenParameters
     * @return RecordingDownloadTokenResponse
     */
    public function getRecordingDownloadToken(RecordingDownloadTokenParameters $recordingDownloadTokenParameters): RecordingDownloadTokenResponse {
        $body = $recordingDownloadTokenParameters->buildBody();
        $output = $this->sendRequest("/recording/getDownloadToken", $body);
        return new RecordingDownloadTokenResponse($output);
    }

    /**
     * @return ClientFilesResponses
     */
    public function getClientFiles() {
        $output = $this->sendRequest("/getClientFiles", []);
        return new ClientFilesResponses($output);
    }

    /**
     * @param array $payload
     * @param int $validity in seconds
     * @param string $algo
     * @param array $head
     * @return string
     */
    public function getJWTencodedData(array $payload, int $validity, $algo = "HS256", array $head = []) {
        $payload['iss'] = $this->apiKey;
        $payload['nbf'] = time();
        $payload['exp'] = time() + $validity;

        return JWT::encode($payload, $this->apiSecret, $algo, null, $head);
    }

    /**
     * @param string $raw
     * @param string $algo
     * @return object
     */
    public function decodeJWTData(string $raw, $algo = "HS256") {
        return JWT::decode($raw, new Key($this->apiSecret, $algo));
    }

    /**
     * Generate UUID random string
     * @return string
     */
    public function getUUID() {
        $uuid = Uuid::uuid4();
        return $uuid->toString();
    }

    /**
     * @param $path
     * @param array $body
     * @return object
     */
    protected function sendRequest($path, array $body) {
        $output = new \stdClass();
        $output->status = false;

        $header = array(
            "Content-type: application/json",
            "API-KEY: " . $this->apiKey,
            "API-SECRET: " . $this->apiSecret
        );
        $url = $this->serverUrl . $this->defaultPath . $path;

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cert/cacert.pem');

            $result = curl_exec($ch);
            $error = curl_error($ch);
            $errno = curl_errno($ch);
            curl_close($ch);

            if (0 !== $errno) {
                $output->response = "Error: " . $error;
                return $output;
            }

            $output->status = true;
            $output->response = json_decode($result);
        } catch (\Exception $e) {
            $output->response = "Exception: " . $e->getMessage();
        }

        return $output;
    }
}
