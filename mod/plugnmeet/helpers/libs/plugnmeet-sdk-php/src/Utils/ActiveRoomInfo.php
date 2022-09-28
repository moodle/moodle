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

namespace Mynaparrot\Plugnmeet\Utils;

/**
 *
 */
class ActiveRoomInfo
{
    /**
     * @var object
     */
    protected $roomInfo;

    /**
     * @param object $roomInfo
     */
    public function __construct(object $roomInfo)
    {
        $this->roomInfo = $roomInfo;
    }

    /**
     * @return string
     */
    public function getRoomTitle(): string
    {
        return $this->roomInfo->room_title;
    }

    /**
     * @return string
     */
    public function getRoomId(): string
    {
        return $this->roomInfo->room_id;
    }

    /**
     * @return string
     */
    public function getRoomSid(): string
    {
        return $this->roomInfo->sid;
    }

    /**
     * @return int
     */
    public function getJoinedParticipants(): int
    {
        return $this->roomInfo->joined_participants;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->roomInfo->is_running;
    }

    /**
     * @return bool
     */
    public function isActiveRecording(): bool
    {
        return $this->roomInfo->is_recording;
    }

    /**
     * @return bool
     */
    public function isActiveRTMP(): bool
    {
        return $this->roomInfo->is_active_rtmp;
    }

    /**
     * @return string
     */
    public function getWebhookUrl(): string
    {
        return $this->roomInfo->webhook_url;
    }

    /**
     * @return bool
     */
    public function isBreakoutRoom(): bool
    {
        return $this->roomInfo->is_breakout_room;
    }

    /**
     * @return string
     */
    public function getParentRoomId(): string
    {
        return $this->roomInfo->parent_room_id;
    }

    /**
     * @return int
     */
    public function getCreationTime(): int
    {
        return $this->roomInfo->creation_time;
    }

    /**
     * @return string
     */
    public function getMetadata(): string
    {
        return $this->roomInfo->metadata;
    }
}
