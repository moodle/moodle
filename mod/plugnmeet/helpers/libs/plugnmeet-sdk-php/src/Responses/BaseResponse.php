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
abstract class BaseResponse
{
    /**
     * @var object
     */
    protected $rawResponse;

    /**
     * @param object $rawResponse
     */
    public function __construct($rawResponse)
    {
        $this->rawResponse = $rawResponse;
        if ($rawResponse->status) {
            $this->rawResponse = $rawResponse->response;
        } else {
            $this->rawResponse->msg = $rawResponse->response;
        }
    }

    /**
     * @return object
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->rawResponse->status;
    }


    /**
     * @return string
     */
    public function getResponseMsg(): string
    {
        if ($this->rawResponse->msg === null) {
            return "something went wrong";
        }

        $msg = $this->rawResponse->msg;

        if (is_array($msg)) {
            return json_encode($msg);
        }

        return $msg;
    }
}
