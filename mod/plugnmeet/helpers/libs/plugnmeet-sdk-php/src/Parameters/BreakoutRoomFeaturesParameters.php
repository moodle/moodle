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
class BreakoutRoomFeaturesParameters
{
    /**
     * @var bool
     */
    protected $isAllow = true;
    /**
     * @var int
     */
    protected $allowedNumberRooms = 6;

    /**
     * @return bool
     */
    public function isAllow(): bool
    {
        return $this->isAllow;
    }

    /**
     * @param bool $isAllow
     */
    public function setIsAllow(bool $isAllow): void
    {
        $this->isAllow = filter_var($isAllow, FILTER_VALIDATE_BOOLEAN);;
    }

    /**
     * @return int
     */
    public function getAllowedNumberRooms(): int
    {
        return $this->allowedNumberRooms;
    }

    /**
     * @param int $allowedNumberRooms
     */
    public function setAllowedNumberRooms(int $allowedNumberRooms): void
    {
        if ($allowedNumberRooms > 0) {
            $this->allowedNumberRooms = $allowedNumberRooms;
        }
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        $body = array(
            "is_allow" => $this->isAllow(),
            "allowed_number_rooms" => $this->allowedNumberRooms
        );

        return $body;
    }
}
