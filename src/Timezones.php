<?php

/*
 * (c) Ivan Hanak <packagist@ivanhanak.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace hanakivan;

use \DateTime;
use \DateTimeZone;

class Timezones {

    /**
     * @var string
     */
    public $timezoneName;

    /**
     * @var string
     */
    private $offset;

    public function __construct(string $timezoneName) {
        $this->timezoneName = $timezoneName;

        $this->setTimezoneOffset();
    }

    public function __toString(): string {
        return $this->timezoneName;
    }

    public function getOffset($formatted = true, $usePrefix = true) {
        return $formatted ? self::format_GMT_offset($this->offset, $usePrefix) : $this->offset;
    }

    private static function format_GMT_offset($offset, $usePrefix = true): string {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));

        if($usePrefix) {
            return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
        }

        return ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    public static function getTimeZones(): array {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets = [];
            $now = new DateTime();

            foreach (DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();
                $timezones[$timezone] = '(' . self::format_GMT_offset($offset) . ') ' . self::format_timezone_name($timezone);
            }

            array_multisort($offsets, $timezones);
        }

        return $timezones;
    }

    public static function getTimezoneByCity($city): ?string {

        $city = mb_strtolower($city);
        $city = trim($city);

        if(mb_strlen($city) === 0) {
            return null;
        }

        foreach(self::getTimeZones() as $timezone => $timezoneWithOffset){
            $timezoneAltered = mb_strtolower($timezone);
            if(mb_strpos($timezoneAltered, $city) !== false) {
                return $timezone;
            }
        }

        return null;
    }


    private static function format_timezone_name($name): string {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);

        return $name;
    }

    private function setTimezoneOffset(): void {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone($this->timezoneName));

        $this->offset = $now->getOffset();
    }
}
