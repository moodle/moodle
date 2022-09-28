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

use Mynaparrot\Plugnmeet\Utils\ActiveRoomInfo;
use Mynaparrot\Plugnmeet\Utils\ParticipantInfo;

/**
 *
 */
class GetActiveRoomInfoResponse extends BaseResponse
{
    /**
     * @return ActiveRoomInfo
     */
    public function getActiveRoomInfo(): ActiveRoomInfo
    {
        return new ActiveRoomInfo($this->rawResponse->room->room_info);
    }

    /**
     * @return ParticipantInfo []
     */
    public function getParticipantsInfo(): array
    {
        $participants = [];

        if (count($this->rawResponse->room->participants_info) > 0) {
            foreach ($this->rawResponse->room->participants_info as $participant) {
                $participants[] = new ParticipantInfo($participant);
            }
        }

        return $participants;
    }
}
