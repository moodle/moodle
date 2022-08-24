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
class ChatFeaturesParameters
{
    /**
     * @var bool
     */
    protected $allowChat = true;
    /**
     * @var bool
     */
    protected $allowFileUpload = true;
    /**
     * @var string[]
     */
    protected $allowedFileTypes = array();
    /**
     * @var int
     */
    protected $maxFileSize = 0;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isAllowChat()
    {
        return $this->allowChat;
    }

    /**
     * @param bool $allowChat
     */
    public function setAllowChat($allowChat)
    {
        $this->allowChat = filter_var($allowChat, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return bool
     */
    public function isAllowFileUpload()
    {
        return $this->allowFileUpload;
    }

    /**
     * @param bool $allowFileUpload
     */
    public function setAllowFileUpload($allowFileUpload)
    {
        $this->allowFileUpload = filter_var($allowFileUpload, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return string[]
     */
    public function getAllowedFileTypes()
    {
        return $this->allowedFileTypes;
    }

    /**
     * @param string[] $allowedFileTypes
     */
    public function setAllowedFileTypes(array $allowedFileTypes)
    {
        $this->allowedFileTypes = $allowedFileTypes;
    }

    /**
     * @return int
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param int $maxFileSize
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        $body = array(
            "allow_chat" => $this->allowChat,
            "allow_file_upload" => $this->allowFileUpload
        );

        if (!empty($this->allowedFileTypes)) {
            $body['allowed_file_types'] = $this->allowedFileTypes;
        }

        if ($this->maxFileSize > 0) {
            $body['max_file_size'] = $this->maxFileSize;
        }

        return $body;
    }
}
