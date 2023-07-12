<?php

/*
 * Copyright (c) 2012 The University of Queensland
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

/*
 * Written by David Gwynne <dlg@uq.edu.au> as part of the IT
 * Infrastructure Group in the Faculty of Engineering, Architecture
 * and Information Technology.
 */

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function riak_hook_cron(&$croninfo)
{
    assert(is_array($croninfo));
    assert(array_key_exists('summary', $croninfo));
    assert(array_key_exists('tag', $croninfo));

    if ($croninfo['tag'] !== 'hourly') {
        return;
    }

    try {
        $store = new \SimpleSAML\Module\riak\Store\Riak();
        $result = $store->getExpired();

        if ($result === null) {
            $result = [];
        } else {
            foreach ($result as $key) {
                $store->delete('session', $key);
            }
        }

        \SimpleSAML\Logger::info(
            sprintf("deleted %s riak key%s", sizeof($result), sizeof($result) == 1 ? '' : 's')
        );
    } catch (\Exception $e) {
        $message = 'riak threw exception: ' . $e->getMessage();
        \SimpleSAML\Logger::warning($message);
        $croninfo['summary'][] = $message;
    }
}
