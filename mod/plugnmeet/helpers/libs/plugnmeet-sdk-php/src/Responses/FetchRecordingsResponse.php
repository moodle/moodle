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

use Mynaparrot\Plugnmeet\Utils\RecordingInfo;

/**
 *
 */
class FetchRecordingsResponse extends BaseResponse
{
    /**
     * @return int
     */
    public function getTotalRecordings(): ?int
    {
        return $this->rawResponse->result->total_recordings;
    }

    /**
     * @return int|null
     */
    public function getFrom(): ?int
    {
        return $this->rawResponse->result->from;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->rawResponse->result->limit;
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->rawResponse->result->order_by;
    }

    /**
     * @return RecordingInfo []
     */
    public function getRecordings(): array
    {
        $recordings = [];

        if (count($this->rawResponse->result->recordings_list) > 0) {
            foreach ($this->rawResponse->result->recordings_list as $recording) {
                $recordings[] = new RecordingInfo($recording);
            }
        }

        return $recordings;
    }
}

