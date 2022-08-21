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

use Mynaparrot\Plugnmeet\Parameters\BreakoutRoomFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\ChatFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\CreateRoomParameters;
use Mynaparrot\Plugnmeet\Parameters\DeleteRecordingParameters;
use Mynaparrot\Plugnmeet\Parameters\DisplayExternalLinkFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\EndRoomParameters;
use Mynaparrot\Plugnmeet\Parameters\ExternalMediaPlayerFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\FetchRecordingsParameters;
use Mynaparrot\Plugnmeet\Parameters\GenerateJoinTokenParameters;
use Mynaparrot\Plugnmeet\Parameters\GetActiveRoomInfoParameters;
use Mynaparrot\Plugnmeet\Parameters\IsRoomActiveParameters;
use Mynaparrot\Plugnmeet\Parameters\LockSettingsParameters;
use Mynaparrot\Plugnmeet\Parameters\RecordingDownloadTokenParameters;
use Mynaparrot\Plugnmeet\Parameters\RoomFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\RoomMetadataParameters;
use Mynaparrot\Plugnmeet\Parameters\SharedNotePadFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\WaitingRoomFeaturesParameters;
use Mynaparrot\Plugnmeet\Parameters\WhiteboardFeaturesParameters;
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
use Mynaparrot\Plugnmeet\PlugNmeet;

require __DIR__ . "/libs/plugnmeet-sdk-php/vendor/autoload.php";

/**
 *
 */
class plugNmeetConnect {
    /**
     * @var PlugNmeet
     */
    protected $plugnmeet;

    function __construct($config) {
        $this->plugnmeet = new PlugNmeet(
            $config->plugnmeet_server_url,
            $config->plugnmeet_api_key,
            $config->plugnmeet_secret
        );
    }

    /**
     * @return string
     */
    public function getUUID() {
        return $this->plugnmeet->getUUID();
    }

    /**
     * @param string $roomId
     * @return IsRoomActiveResponse
     */
    public function isRoomActive(string $roomId): IsRoomActiveResponse {
        $isRoomActiveParameters = new IsRoomActiveParameters();
        $isRoomActiveParameters->setRoomId($roomId);

        return $this->plugnmeet->isRoomActive($isRoomActiveParameters);
    }

    /**
     * @param string $roomId
     * @param string $roomTitle
     * @param string $welcomeMessage
     * @param string $webHookUrl
     * @param array $roomMetadata
     * @return CreateRoomResponse
     */
    public function createRoom(string $roomId, string $roomTitle, string $welcomeMessage, int $max_participants, string $webHookUrl, array $roomMetadata): CreateRoomResponse {
        $roomFeatures = $roomMetadata['room_features'];
        $features = new RoomFeaturesParameters();

        if (isset($roomFeatures['allow_webcams'])) {
            $features->setAllowWebcams($roomFeatures['allow_webcams']);
        }
        if (isset($roomFeatures['mute_on_start'])) {
            $features->setMuteOnStart($roomFeatures['mute_on_start']);
        }
        if (isset($roomFeatures['allow_screen_share'])) {
            $features->setAllowScreenShare($roomFeatures['allow_screen_share']);
        }
        if (isset($roomFeatures['allow_recording'])) {
            $features->setAllowRecording($roomFeatures['allow_recording']);
        }
        if (isset($roomFeatures['allow_rtmp'])) {
            $features->setAllowRTMP($roomFeatures['allow_rtmp']);
        }
        if (isset($roomFeatures['allow_view_other_webcams'])) {
            $features->setAllowViewOtherWebcams($roomFeatures['allow_view_other_webcams']);
        }
        if (isset($roomFeatures['allow_view_other_users_list'])) {
            $features->setAllowViewOtherParticipants($roomFeatures['allow_view_other_users_list']);
        }
        if (isset($roomFeatures['admin_only_webcams'])) {
            $features->setAdminOnlyWebcams($roomFeatures['admin_only_webcams']);
        }
        if (isset($roomFeatures['allow_polls'])) {
            $features->setAllowPolls($roomFeatures['allow_polls']);
        }
        if (isset($roomFeatures['room_duration'])) {
            if ($roomFeatures['room_duration'] > 0) {
                $features->setRoomDuration($roomFeatures['room_duration']);
            }
        }

        if (isset($roomMetadata['chat_features'])) {
            $roomChatFeatures = $roomMetadata['chat_features'];
            $chatFeatures = new ChatFeaturesParameters();
            if (isset($roomChatFeatures['allow_chat'])) {
                $chatFeatures->setAllowChat($roomChatFeatures['allow_chat']);
            }
            if (isset($roomChatFeatures['allow_file_upload'])) {
                $chatFeatures->setAllowFileUpload($roomChatFeatures['allow_file_upload']);
            }
            $features->setChatFeatures($chatFeatures);
        }

        if (isset($roomMetadata['shared_note_pad_features'])) {
            $roomSharedNotepadFeatures = $roomMetadata['shared_note_pad_features'];
            $sharedNotePadFeatures = new SharedNotePadFeaturesParameters();
            if (isset($roomSharedNotepadFeatures['allowed_shared_note_pad'])) {
                $sharedNotePadFeatures->setAllowedSharedNotePad($roomSharedNotepadFeatures['allowed_shared_note_pad']);
            }
            $features->setSharedNotePadFeatures($sharedNotePadFeatures);
        }

        if (isset($roomMetadata['whiteboard_features'])) {
            $roomWhiteboardFeatures = $roomMetadata['whiteboard_features'];
            $whiteboardFeatures = new WhiteboardFeaturesParameters();
            if (isset($roomWhiteboardFeatures['allowed_whiteboard'])) {
                $whiteboardFeatures->setAllowedWhiteboard($roomWhiteboardFeatures['allowed_whiteboard']);
            }
            $features->setWhiteboardFeatures($whiteboardFeatures);
        }

        if (isset($roomMetadata['external_media_player_features'])) {
            $roomExternalMediaPlayerFeatures = $roomMetadata['external_media_player_features'];
            $externalMediaPlayerFeatures = new ExternalMediaPlayerFeaturesParameters();
            if (isset($roomExternalMediaPlayerFeatures['allowed_external_media_player'])) {
                $externalMediaPlayerFeatures->setAllowedExternalMediaPlayer($roomExternalMediaPlayerFeatures['allowed_external_media_player']);
            }
            $features->setExternalMediaPlayerFeatures($externalMediaPlayerFeatures);
        }

        if (isset($roomMetadata['waiting_room_features'])) {
            $roomWaitingRoomFeatures = $roomMetadata['waiting_room_features'];
            $waitingRoomFeatures = new WaitingRoomFeaturesParameters();
            if (isset($roomWaitingRoomFeatures['is_active'])) {
                $waitingRoomFeatures->setIsActive($roomWaitingRoomFeatures['is_active']);
            }
            if (isset($roomWaitingRoomFeatures['waiting_room_msg'])) {
                if (!empty($roomWaitingRoomFeatures['waiting_room_msg'])) {
                    $waitingRoomFeatures->setWaitingRoomMsg($roomWaitingRoomFeatures['waiting_room_msg']);
                }
            }
            $features->setWaitingRoomFeatures($waitingRoomFeatures);
        }

        if (isset($roomMetadata['breakout_room_features'])) {
            $roomBreakoutRoomFeatures = $roomMetadata['breakout_room_features'];
            $breakoutRoomFeatures = new BreakoutRoomFeaturesParameters();
            if (isset($roomBreakoutRoomFeatures['is_allow'])) {
                $breakoutRoomFeatures->setIsAllow($roomBreakoutRoomFeatures['is_allow']);
            }
            if (isset($roomBreakoutRoomFeatures['allowed_number_rooms'])) {
                if (!empty($roomBreakoutRoomFeatures['allowed_number_rooms'])) {
                    $breakoutRoomFeatures->setAllowedNumberRooms($roomBreakoutRoomFeatures['allowed_number_rooms']);
                }
            }
            $features->setBreakoutRoomFeatures($breakoutRoomFeatures);
        }

        if (isset($roomMetadata['display_external_link_features'])) {
            $roomDisplayExternalLinkFeatures = $roomMetadata['display_external_link_features'];
            $displayExternalLinkFeatures = new DisplayExternalLinkFeaturesParameters();
            if (isset($roomDisplayExternalLinkFeatures['is_allow'])) {
                $displayExternalLinkFeatures->setIsAllow($roomDisplayExternalLinkFeatures['is_allow']);
            }
            $features->setDisplayExternalLinkFeatures($displayExternalLinkFeatures);
        }

        $metadata = new RoomMetadataParameters();
        $metadata->setRoomTitle($roomTitle);
        $metadata->setWelcomeMessage($welcomeMessage);
        $metadata->setWebhookUrl($webHookUrl);
        $metadata->setFeatures($features);

        if (isset($roomMetadata['default_lock_settings'])) {
            $defaultLocks = $roomMetadata['default_lock_settings'];
            $lockSettings = new LockSettingsParameters();

            if (isset($defaultLocks['lock_microphone'])) {
                $lockSettings->setLockMicrophone($defaultLocks['lock_microphone']);
            }
            if (isset($defaultLocks['lock_webcam'])) {
                $lockSettings->setLockWebcam($defaultLocks['lock_webcam']);
            }
            if (isset($defaultLocks['lock_screen_sharing'])) {
                $lockSettings->setLockScreenSharing($defaultLocks['lock_screen_sharing']);
            }
            if (isset($defaultLocks['lock_whiteboard'])) {
                $lockSettings->setLockWhiteboard($defaultLocks['lock_whiteboard']);
            }
            if (isset($defaultLocks['lock_shared_notepad'])) {
                $lockSettings->setLockSharedNotepad($defaultLocks['lock_shared_notepad']);
            }
            if (isset($defaultLocks['lock_chat'])) {
                $lockSettings->setLockChat($defaultLocks['lock_chat']);
            }
            if (isset($defaultLocks['lock_chat_send_message'])) {
                $lockSettings->setLockChatSendMessage($defaultLocks['lock_chat_send_message']);
            }
            if (isset($defaultLocks['lock_chat_file_share'])) {
                $lockSettings->setLockChatFileShare($defaultLocks['lock_chat_file_share']);
            }
            if (isset($defaultLocks['lock_private_chat'])) {
                $lockSettings->setLockPrivateChat($defaultLocks['lock_private_chat']);
            }

            $metadata->setDefaultLockSettings($lockSettings);
        }

        $roomCreateParams = new CreateRoomParameters();
        $roomCreateParams->setRoomId($roomId);
        if ($max_participants > 0) {
            $roomCreateParams->setMaxParticipants($max_participants);
        }
        $roomCreateParams->setRoomMetadata($metadata);

        return $this->plugnmeet->createRoom($roomCreateParams);
    }

    /**
     * @param string $roomId
     * @param string $name
     * @param string $userId
     * @param bool $isAdmin
     * @return GenerateJoinTokenResponse
     */
    public function getJoinToken(string $roomId, string $name, string $userId, bool $isAdmin, bool $isHidden = false): GenerateJoinTokenResponse {
        $generateJoinTokenParameters = new GenerateJoinTokenParameters();
        $generateJoinTokenParameters->setRoomId($roomId);
        $generateJoinTokenParameters->setName($name);
        $generateJoinTokenParameters->setUserId($userId);
        $generateJoinTokenParameters->setIsAdmin($isAdmin);
        $generateJoinTokenParameters->setIsHidden($isHidden);

        return $this->plugnmeet->getJoinToken($generateJoinTokenParameters);
    }

    /**
     * @param string $roomId
     * @return EndRoomResponse
     */
    public function endRoom(string $roomId) {
        $endRoomParameters = new EndRoomParameters();
        $endRoomParameters->setRoomId($roomId);

        return $this->plugnmeet->endRoom($endRoomParameters);
    }

    /**
     * @param string $roomId
     * @return GetActiveRoomInfoResponse
     */
    public function getActiveRoomInfo(string $roomId) {
        $getActiveRoomInfoParameters = new GetActiveRoomInfoParameters();
        $getActiveRoomInfoParameters->setRoomId($roomId);

        return $this->plugnmeet->getActiveRoomInfo($getActiveRoomInfoParameters);
    }

    /**
     * @return GetActiveRoomsInfoResponse
     */
    public function getActiveRoomsInfo() {
        return $this->plugnmeet->getActiveRoomsInfo();
    }

    /**
     * @param array $roomIds
     * @param int $from
     * @param int $limit
     * @param string $orderBy
     * @return FetchRecordingsResponse
     */
    public function getRecordings(array $roomIds, int $from = 0, int $limit = 20, string $orderBy = "DESC") {
        $fetchRecordingsParameters = new FetchRecordingsParameters();
        $fetchRecordingsParameters->setRoomIds($roomIds);
        $fetchRecordingsParameters->setFrom($from);
        $fetchRecordingsParameters->setLimit($limit);
        $fetchRecordingsParameters->setOrderBy($orderBy);

        return $this->plugnmeet->fetchRecordings($fetchRecordingsParameters);
    }

    /**
     * @param $recordingId
     * @return mixed|RecordingDownloadTokenResponse
     */
    public function getRecordingDownloadLink($recordingId) {
        $recordingDownloadTokenParameters = new RecordingDownloadTokenParameters();
        $recordingDownloadTokenParameters->setRecordId($recordingId);

        return $this->plugnmeet->getRecordingDownloadToken($recordingDownloadTokenParameters);
    }

    /**
     * @param $recordingId
     * @return DeleteRecordingResponse
     */
    public function deleteRecording($recordingId) {
        $deleteRecordingParameters = new DeleteRecordingParameters();
        $deleteRecordingParameters->setRecordId($recordingId);

        return $this->plugnmeet->deleteRecordings($deleteRecordingParameters);
    }

    /**
     * @return ClientFilesResponses
     */
    public function getClientFiles() {
        return $this->plugnmeet->getClientFiles();
    }
}
