<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_videowatch;

/**
 * Reads the duration of an uploaded video from the file itself.
 *
 * The browser is not trusted for this number. A short claimed length would
 * let someone finish a long video immediately.
 *
 * @package    local_videowatch
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class duration {
    /**
     * Duration in seconds, or null when the file has no readable length.
     *
     * @param \stored_file $file
     */
    public static function from_file(\stored_file $file): ?float {
        $handle = $file->get_content_file_handle();
        if (!$handle) {
            return null;
        }
        $stat = fstat($handle);
        $size = (int) ($stat['size'] ?? $file->get_filesize());
        $seconds = self::from_handle($handle, $size);
        fclose($handle);
        return $seconds;
    }

    /**
     * Duration in seconds from a byte string, or null.
     */
    public static function from_string(string $bytes): ?float {
        $handle = fopen('php://temp', 'rb+');
        fwrite($handle, $bytes);
        rewind($handle);
        $seconds = self::from_handle($handle, strlen($bytes));
        fclose($handle);
        return $seconds;
    }

    /**
     * @param resource $handle
     */
    public static function from_handle($handle, int $filesize): ?float {
        if ($filesize < 16) {
            return null;
        }
        $seconds = self::find_mvhd($handle, 0, $filesize, true);
        if ($seconds !== null && $seconds > 0) {
            return $seconds;
        }
        return self::webm_duration($handle, $filesize);
    }

    /**
     * @param resource $handle
     */
    private static function find_mvhd($handle, int $start, int $end, bool $top): ?float {
        $pos = $start;
        $guard = 0;
        while ($pos + 8 <= $end && $guard < 100000) {
            $guard++;
            fseek($handle, $pos);
            $size = self::read_u32($handle);
            $type = fread($handle, 4);
            if ($size === null || strlen($type) < 4) {
                return null;
            }
            $header = 8;
            if ($size === 1) {
                $high = self::read_u32($handle);
                $low = self::read_u32($handle);
                if ($high === null || $low === null) {
                    return null;
                }
                $size = ($high * 4294967296) + $low;
                $header = 16;
            } else if ($size === 0 && $top) {
                $size = $end - $pos;
            }
            if ($size < $header || $pos + $size > $end) {
                return null;
            }
            if ($type === 'mvhd') {
                return self::parse_mvhd($handle, $pos + $header);
            }
            if ($type === 'moov') {
                $found = self::find_mvhd($handle, $pos + $header, $pos + $size, false);
                if ($found !== null) {
                    return $found;
                }
            }
            $pos += $size;
        }
        return null;
    }

    /**
     * @param resource $handle
     */
    private static function parse_mvhd($handle, int $body): ?float {
        fseek($handle, $body);
        $version = fread($handle, 1);
        if ($version === false || $version === '') {
            return null;
        }
        fread($handle, 3);
        if (ord($version) === 1) {
            fread($handle, 16);
            $timescale = self::read_u32($handle);
            $high = self::read_u32($handle);
            $low = self::read_u32($handle);
            if ($timescale === null || $high === null || $low === null || $timescale === 0) {
                return null;
            }
            $mediaduration = ($high * 4294967296) + $low;
        } else {
            fread($handle, 8);
            $timescale = self::read_u32($handle);
            $mediaduration = self::read_u32($handle);
            if ($timescale === null || $mediaduration === null || $timescale === 0) {
                return null;
            }
        }
        $seconds = $mediaduration / $timescale;
        if ($seconds <= 0 || $seconds > 86400) {
            return null;
        }
        return $seconds;
    }

    /**
     * WebM Duration element: 0x44 0x89, size 0x88, IEEE float seconds.
     *
     * @param resource $handle
     */
    private static function webm_duration($handle, int $filesize): ?float {
        $window = min($filesize, 1048576);
        fseek($handle, 0);
        $chunk = fread($handle, $window);
        if (!is_string($chunk) || $chunk === '') {
            return null;
        }
        $offset = 0;
        while (($at = strpos($chunk, "\x44\x89\x88", $offset)) !== false) {
            $raw = substr($chunk, $at + 3, 8);
            if (strlen($raw) === 8) {
                $value = unpack('E', $raw);
                $seconds = $value[1] ?? 0;
                if ($seconds > 0 && $seconds <= 86400) {
                    return (float) $seconds;
                }
            }
            $offset = $at + 3;
        }
        return null;
    }

    /**
     * @param resource $handle
     */
    private static function read_u32($handle): ?int {
        $bytes = fread($handle, 4);
        if (!is_string($bytes) || strlen($bytes) < 4) {
            return null;
        }
        $value = unpack('N', $bytes);
        return $value[1] ?? null;
    }
}
