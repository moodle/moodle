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

namespace Mynaparrot\Plugnmeet\Responses;

/**
 *
 */
class CreateRoomResponse extends BaseResponse
{
    /**
     * @return string|null
     */
    public function getSid(): ?string
    {
        return $this->rawResponse->sid;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->rawResponse->name;
    }

    /**
     * @return int|null
     */
    public function getMaxParticipants(): ?int
    {
        return $this->rawResponse->max_participants;
    }

    /**
     * @return int|null
     */
    public function getEmptyTimeout(): ?int
    {
        return $this->rawResponse->empty_timeout;
    }

    /**
     * @return int|null
     */
    public function getCreationTime(): ?int
    {
        return $this->rawResponse->creation_time;
    }

    /**
     * @return string|null
     */
    public function getTurnPassword(): ?string
    {
        return $this->rawResponse->turn_password;
    }

    /**
     * @return array|null
     */
    public function getEnabledCodecs(): ?array
    {
        return $this->rawResponse->enabled_codecs;
    }

    /**
     * @return string|null
     */
    public function getMetadata(): ?string
    {
        return $this->rawResponse->metadata;
    }
}
