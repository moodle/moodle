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
class RoomMetadataParameters
{
    /**
     * @var string
     */
    protected $roomTitle;
    /**
     * @var string
     */
    protected $welcomeMessage;
    /**
     * @var string
     */
    protected $webhookUrl;
    /**
     * @var RoomFeaturesParameters
     */
    protected $features;

    /**
     * @var LockSettingsParameters
     */
    protected $defaultLockSettings;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getRoomTitle()
    {
        return $this->roomTitle;
    }

    /**
     * @param string $roomTitle
     */
    public function setRoomTitle($roomTitle)
    {
        $this->roomTitle = $roomTitle;
    }

    /**
     * @return string
     */
    public function getWelcomeMessage(): string
    {
        return $this->welcomeMessage;
    }

    /**
     * @param string $welcomeMessage
     */
    public function setWelcomeMessage(string $welcomeMessage): void
    {
        $this->welcomeMessage = $welcomeMessage;
    }

    /**
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->webhookUrl;
    }

    /**
     * @param string $webhookUrl
     */
    public function setWebhookUrl($webhookUrl)
    {
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * @return RoomFeaturesParameters
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param RoomFeaturesParameters $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * @return LockSettingsParameters
     */
    public function getDefaultLockSettings(): LockSettingsParameters
    {
        return $this->defaultLockSettings;
    }

    /**
     * @param LockSettingsParameters $defaultLockSettings
     */
    public function setDefaultLockSettings(LockSettingsParameters $defaultLockSettings): void
    {
        $this->defaultLockSettings = $defaultLockSettings;
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        $body = array(
            "room_title" => $this->roomTitle,
        );

        if (!empty($this->welcomeMessage)) {
            $body["welcome_message"] = $this->welcomeMessage;
        }

        if (!empty($this->webhookUrl)) {
            $body["webhook_url"] = $this->webhookUrl;
        }

        if ($this->features !== null) {
            $body["room_features"] = $this->features->buildBody();
        }

        if ($this->defaultLockSettings !== null) {
            $body["default_lock_settings"] = $this->defaultLockSettings->buildBody();
        }

        return $body;
    }

}
