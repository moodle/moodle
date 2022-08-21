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
class FetchRecordingsParameters
{
    /**
     * @var array
     */
    protected $roomIds = array();
    /**
     * @var int
     */
    protected $from = 0;
    /**
     * @var int
     */
    protected $limit = 20;
    /**
     * @var string
     */
    protected $orderBy = "ASC";

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getRoomIds(): array
    {
        return $this->roomIds;
    }

    /**
     * @param array $roomIds
     */
    public function setRoomIds(array $roomIds): void
    {
        $this->roomIds = $roomIds;
    }

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @param int $from
     */
    public function setFrom(int $from): void
    {
        $this->from = $from;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * value can be ASC or DESC
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return array
     */
    public function buildBody()
    {
        return array(
            "room_ids" => $this->roomIds,
            "from" => $this->from,
            "limit" => $this->limit,
            "order_by" => $this->orderBy
        );
    }
}
