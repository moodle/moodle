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

namespace Mynaparrot\Plugnmeet\Parameters;

/**
 *
 */
class RoomFeaturesParameters
{
    /**
     * @var bool
     */
    protected $allowWebcams = true;
    /**
     * @var bool
     */
    protected $muteOnStart = false;
    /**
     * @var bool
     */
    protected $allowScreenShare = true;
    /**
     * @var bool
     */
    protected $allowRecording = true;
    /**
     * @var bool
     */
    protected $allowRTMP = true;
    /**
     * @var bool
     */
    protected $adminOnlyWebcams = false;
    /**
     * @var bool
     */
    protected $allowViewOtherWebcams = true;
    /**
     * @var bool
     */
    protected $allowViewOtherParticipants = true;
    /**
     * @var bool
     */
    protected $allowPolls = true;
    /**
     * @var int
     */
    protected $roomDuration = 0;
    /**
     * @var ChatFeaturesParameters
     */
    protected $chatFeatures;

    /**
     * @var SharedNotePadFeaturesParameters
     */
    protected $sharedNotePadFeatures;
    /**
     * @var WhiteboardFeaturesParameters
     */
    protected $whiteboardFeatures;
    /**
     * @var ExternalMediaPlayerFeaturesParameters
     */
    protected $externalMediaPlayerFeatures;

    /**
     * @var WaitingRoomFeaturesParameters
     */
    protected $waitingRoomFeatures;

    /**
     * @var BreakoutRoomFeaturesParameters
     */
    protected $breakoutRoomFeatures;

    /**
     * @var DisplayExternalLinkFeaturesParameters
     */
    protected $displayExternalLinkFeatures;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isAllowWebcams()
    {
        return $this->allowWebcams;
    }

    /**
     * @param bool $allowWebcams
     */
    public function setAllowWebcams($allowWebcams)
    {
        $this->allowWebcams = filter_var($allowWebcams, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isMuteOnStart(): bool
    {
        return $this->muteOnStart;
    }

    /**
     * @param bool $muteOnStart
     */
    public function setMuteOnStart(bool $muteOnStart): void
    {
        $this->muteOnStart = filter_var($muteOnStart, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowScreenShare()
    {
        return $this->allowScreenShare;
    }

    /**
     * @param bool $allowScreenShare
     */
    public function setAllowScreenShare($allowScreenShare)
    {
        $this->allowScreenShare = filter_var($allowScreenShare, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowRecording()
    {
        return $this->allowRecording;
    }

    /**
     * @param bool $allowRecording
     */
    public function setAllowRecording($allowRecording)
    {
        $this->allowRecording = filter_var($allowRecording, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowRTMP()
    {
        return $this->allowRTMP;
    }

    /**
     * @param bool $allowRTMP
     */
    public function setAllowRTMP($allowRTMP)
    {
        $this->allowRTMP = filter_var($allowRTMP, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAdminOnlyWebcams()
    {
        return $this->adminOnlyWebcams;
    }

    /**
     * @param bool $adminOnlyWebcams
     */
    public function setAdminOnlyWebcams($adminOnlyWebcams)
    {
        $this->adminOnlyWebcams = filter_var($adminOnlyWebcams, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowViewOtherWebcams(): bool
    {
        return $this->allowViewOtherWebcams;
    }

    /**
     * @param bool $allowViewOtherWebcams
     */
    public function setAllowViewOtherWebcams(bool $allowViewOtherWebcams): void
    {
        $this->allowViewOtherWebcams = filter_var($allowViewOtherWebcams, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowViewOtherParticipants(): bool
    {
        return $this->allowViewOtherParticipants;
    }

    /**
     * @param bool $allowViewOtherParticipants
     */
    public function setAllowViewOtherParticipants(bool $allowViewOtherParticipants): void
    {
        $this->allowViewOtherParticipants = filter_var($allowViewOtherParticipants, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowPolls(): bool
    {
        return $this->allowPolls;
    }

    /**
     * @param bool $allowPolls
     */
    public function setAllowPolls(bool $allowPolls): void
    {
        $this->allowPolls = filter_var($allowPolls, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return int
     */
    public function getRoomDuration(): int
    {
        return $this->roomDuration;
    }

    /**
     * @param int $roomDuration
     */
    public function setRoomDuration(int $roomDuration): void
    {
        $this->roomDuration = $roomDuration;
    }

    /**
     * @return ChatFeaturesParameters
     */
    public function getChatFeatures()
    {
        return $this->chatFeatures;
    }

    /**
     * @param ChatFeaturesParameters $chatFeatures
     */
    public function setChatFeatures($chatFeatures)
    {
        $this->chatFeatures = $chatFeatures;
    }

    /**
     * @return SharedNotePadFeaturesParameters
     */
    public function getSharedNotePadFeatures(): SharedNotePadFeaturesParameters
    {
        return $this->sharedNotePadFeatures;
    }

    /**
     * @param SharedNotePadFeaturesParameters $sharedNotePadFeatures
     */
    public function setSharedNotePadFeatures(SharedNotePadFeaturesParameters $sharedNotePadFeatures): void
    {
        $this->sharedNotePadFeatures = $sharedNotePadFeatures;
    }

    /**
     * @return WhiteboardFeaturesParameters
     */
    public function getWhiteboardFeatures(): WhiteboardFeaturesParameters
    {
        return $this->whiteboardFeatures;
    }

    /**
     * @param WhiteboardFeaturesParameters $whiteboardFeatures
     */
    public function setWhiteboardFeatures(WhiteboardFeaturesParameters $whiteboardFeatures): void
    {
        $this->whiteboardFeatures = $whiteboardFeatures;
    }

    /**
     * @return ExternalMediaPlayerFeaturesParameters
     */
    public function getExternalMediaPlayerFeatures(): ExternalMediaPlayerFeaturesParameters
    {
        return $this->externalMediaPlayerFeatures;
    }

    /**
     * @param ExternalMediaPlayerFeaturesParameters $externalMediaPlayerFeatures
     */
    public function setExternalMediaPlayerFeatures(
        ExternalMediaPlayerFeaturesParameters $externalMediaPlayerFeatures
    ): void {
        $this->externalMediaPlayerFeatures = $externalMediaPlayerFeatures;
    }

    /**
     * @return WaitingRoomFeaturesParameters
     */
    public function getWaitingRoomFeatures(): WaitingRoomFeaturesParameters
    {
        return $this->waitingRoomFeatures;
    }

    /**
     * @param WaitingRoomFeaturesParameters $waitingRoomFeatures
     */
    public function setWaitingRoomFeatures(WaitingRoomFeaturesParameters $waitingRoomFeatures): void
    {
        $this->waitingRoomFeatures = $waitingRoomFeatures;
    }

    /**
     * @return BreakoutRoomFeaturesParameters
     */
    public function getBreakoutRoomFeatures(): BreakoutRoomFeaturesParameters
    {
        return $this->breakoutRoomFeatures;
    }

    /**
     * @param BreakoutRoomFeaturesParameters $breakoutRoomFeatures
     */
    public function setBreakoutRoomFeatures(BreakoutRoomFeaturesParameters $breakoutRoomFeatures): void
    {
        $this->breakoutRoomFeatures = $breakoutRoomFeatures;
    }

    /**
     * @return DisplayExternalLinkFeaturesParameters
     */
    public function getDisplayExternalLinkFeatures(): DisplayExternalLinkFeaturesParameters
    {
        return $this->displayExternalLinkFeatures;
    }

    /**
     * @param DisplayExternalLinkFeaturesParameters $displayExternalLinkFeatures
     */
    public function setDisplayExternalLinkFeatures(
        DisplayExternalLinkFeaturesParameters $displayExternalLinkFeatures
    ): void {
        $this->displayExternalLinkFeatures = $displayExternalLinkFeatures;
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        $body = array(
            "allow_webcams" => $this->allowWebcams,
            "mute_on_start" => $this->muteOnStart,
            "allow_screen_share" => $this->allowScreenShare,
            "allow_recording" => $this->allowRecording,
            "allow_rtmp" => $this->allowRTMP,
            "admin_only_webcams" => $this->adminOnlyWebcams,
            "allow_view_other_webcams" => $this->allowViewOtherWebcams,
            "allow_view_other_users_list" => $this->allowViewOtherParticipants,
            "allow_polls" => $this->allowPolls,
            "room_duration" => $this->roomDuration
        );

        if ($this->chatFeatures !== null) {
            $body['chat_features'] = $this->chatFeatures->buildBody();
        }

        if ($this->sharedNotePadFeatures !== null) {
            $body['shared_note_pad_features'] = $this->sharedNotePadFeatures->buildBody();
        }

        if ($this->whiteboardFeatures !== null) {
            $body['whiteboard_features'] = $this->whiteboardFeatures->buildBody();
        }

        if ($this->externalMediaPlayerFeatures !== null) {
            $body['external_media_player_features'] = $this->externalMediaPlayerFeatures->buildBody();
        }

        if ($this->waitingRoomFeatures !== null) {
            $body['waiting_room_features'] = $this->waitingRoomFeatures->buildBody();
        }

        if ($this->breakoutRoomFeatures !== null) {
            $body['breakout_room_features'] = $this->breakoutRoomFeatures->buildBody();
        }

        if ($this->displayExternalLinkFeatures !== null) {
            $body['display_external_link_features'] = $this->displayExternalLinkFeatures->buildBody();
        }

        return $body;
    }
}
