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
class ParticipantInfo
{
    /**
     * @var object
     */
    protected object $participantInfo;

    /**
     * @param object $participantInfo
     */
    public function __construct(object $participantInfo)
    {
        $this->participantInfo = $participantInfo;
    }

    /**
     * @return string
     */
    public function getSid(): string
    {
        return $this->participantInfo->sid;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->participantInfo->identity;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->participantInfo->state;
    }

    /**
     * @return string
     */
    public function getMetadata(): string
    {
        return $this->participantInfo->metadata;
    }

    /**
     * @return int
     */
    public function getJoinedAt(): int
    {
        return $this->participantInfo->joined_at;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->participantInfo->name;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->participantInfo->version;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->participantInfo->permission;
    }
}
