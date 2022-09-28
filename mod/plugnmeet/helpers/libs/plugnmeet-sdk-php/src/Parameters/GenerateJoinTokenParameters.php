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
class GenerateJoinTokenParameters
{
    /**
     * @var string
     */
    protected $roomId;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $userId;
    /**
     * @var bool
     */
    protected $isAdmin = false;
    /**
     * @var bool
     */
    protected $isHidden = false;

    /**
     * @var UserMetadataParameters
     */
    protected $userMetadata;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getRoomId()
    {
        return $this->roomId;
    }

    /**
     * @param string $roomId
     */
    public function setRoomId($roomId)
    {
        $this->roomId = $roomId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->isHidden;
    }

    /**
     * @param bool $isHidden
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;
    }

    /**
     * @return UserMetadataParameters
     */
    public function getUserMetadata()
    {
        return $this->userMetadata;
    }

    /**
     * @param UserMetadataParameters $userMetadata
     */
    public function setUserMetadata($userMetadata)
    {
        $this->userMetadata = $userMetadata;
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        $body = array(
            "room_id" => $this->roomId,
            "user_info" => array(
                "name" => $this->name,
                "user_id" => $this->userId,
                "is_admin" => $this->isAdmin,
                "is_hidden" => $this->isHidden
            )
        );

        if ($this->userMetadata !== null) {
            $body["user_info"]["user_metadata"] = $this->userMetadata->buildBody();
        }

        return $body;
    }
}
