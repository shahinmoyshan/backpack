<?php

namespace Backpack\Lib;

use DateTime;
use Exception;

/**
 * A class for formatting dates and times into a "pretty" format.
 */
class PrettyTime
{
    /**
     * The translation for the various time units and phrases.
     *
     * @var array
     */
    private array $translation = [];

    /**
     * The time constants for the various units.
     */
    private const MINUTE = 60;
    private const HOUR = 3600;
    private const DAY = 86400;
    private const WEEK = 604800;
    private const MONTH = 2628000;
    private const YEAR = 31536000;

    /**
     * Constructor.
     *
     * @param array $translation The translation for the various time units and phrases.
     */
    public function __construct(array $translation = [])
    {
        $this->translation = array_merge(
            [
                'moments_ago' => 'Moments ago',
                'seconds_from_now' => 'Seconds from now',
                'minute' => 'minute',
                'hour' => 'hour',
                'day' => 'day',
                'week' => 'week',
                'month' => 'month',
                'year' => 'year',
                'yesterday' => 'Yesterday',
                'tomorrow' => 'Tomorrow',
                'ago' => 'ago',
                'in' => 'In',
            ],
            $translation
        );
    }

    /**
     * Parse the given DateTime object and return a "pretty" string representation of the time difference.
     *
     * @param DateTime $dateTime The DateTime object to parse.
     * @param DateTime $reference The reference DateTime object to compare with.
     *
     * @return string The "pretty" string representation of the time difference.
     */
    public function parse(DateTime $dateTime, DateTime $reference = null)
    {
        if (!$reference) {
            $reference = new DateTime('now', $dateTime->getTimezone());
        }

        $difference = $reference->format('U') - $dateTime->format('U');
        $absDiff = abs($difference);
        $date = $dateTime->format('Y/m/d');

        if (is_nan($difference)) {
            throw new Exception('The difference between the DateTimes is NaN.');
        }

        if ($reference->format('Y/m/d') === $date) {
            if ($absDiff < self::MINUTE) {
                return $difference >= 0 ? $this->translate('moments_ago') : $this->translate('seconds_from_now');
            } elseif ($absDiff < self::HOUR) {
                return $this->prettyFormat($difference / self::MINUTE, 'minute');
            } else {
                return $this->prettyFormat($difference / self::HOUR, 'hour');
            }
        }

        $yesterday = (clone $reference)->modify('-1 day')->format('Y/m/d');
        $tomorrow = (clone $reference)->modify('+1 day')->format('Y/m/d');

        if ($date === $yesterday) {
            return $this->translate('yesterday');
        } elseif ($date === $tomorrow) {
            return $this->translate('tomorrow');
        } elseif ($absDiff < self::WEEK) {
            return $this->prettyFormat($difference / self::DAY, 'day');
        } elseif ($absDiff < self::MONTH) {
            return $this->prettyFormat($difference / self::WEEK, 'week');
        } elseif ($absDiff < self::YEAR) {
            return $this->prettyFormat($difference / self::MONTH, 'month');
        }

        return $this->prettyFormat($difference / self::YEAR, 'year');
    }

    /**
     * Translate a given key into the given language.
     *
     * @param string $key The key to translate.
     *
     * @return string The translated string.
     */
    private function translate(string $key)
    {
        return $this->translation[$key] ?? $key;
    }

    /**
     * Format a given time difference into a "pretty" string.
     *
     * @param float $difference The time difference to format.
     * @param string $unit The unit of time to use (e.g. 'minute', 'hour', etc.).
     * @param string $language The language to use for the translation.
     *
     * @return string The "pretty" string representation of the time difference.
     */
    private function prettyFormat(float $difference, string $unit)
    {
        $prepend = ($difference < 0) ? $this->translate('in') . ' ' : '';
        $append = ($difference > 0) ? ' ' . $this->translate('ago') : '';
        $difference = floor(abs($difference));

        $unitTranslation = $this->translate($unit);
        if ($difference > 1) {
            $unitTranslation .= 's'; // Add logic for pluralization if needed in other languages
        }

        return sprintf('%s%d %s%s', $prepend, $difference, $unitTranslation, $append);
    }
}