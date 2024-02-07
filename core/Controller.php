<?php

namespace App\core;

use App\models\User;
use App\controllers\UserActivitiesController;

class Controller
{   
    public function handleNull(string|int $key, array $array): string
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return false;
    }

    /**
     * Formats time and according to format passed in
     *
     * @param string $time
     * @param string $format
     * @return string
     */
    public function time_format(string $time, string $format): string
    {
        $org_date = $time;
        $new_date = date($format, strtotime($org_date));

        return $new_date;
    }

    /**
     * generates unique ids for the user
     *
     * @return int
     */
    public function generateUniqid(): int
    {
        $randomId = random_int(100000, 999999); 
        return $randomId; 
    }

    /**
     * formats date entered
     *
     * @param string $date
     * @return string
     */
    public function format_date(string $date)
    {
        # Convert date to timestamp
        $timestamp = strtotime($date);

        # Define lookup tables for month and day strings
        $months = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];

        # Get month, day, and year strings

        # $month = $months[date('m', $timestamp)];

        $month = $months[date('m', $timestamp)];
        $day = date('d', $timestamp);
        $year = date('Y', $timestamp);

        # Construct English date string
        $english_date = $month . ' ' . $day . ', ' . $year;

        return $english_date;
    }

    public function wordAbbreviator(string $word): string
    {
        # Exploded string
        $abbr = '';
        $exploded_string = explode(' ', $word);
        # Abbreviation
        foreach ($exploded_string as $word) {
            $abbr .= strtoupper($word[0]);
        }
        return $abbr;
    }

    public function textTruncate(string $text, int $chars): string
    {
        if (strlen($text) <= $chars) {
            return $text;
        }

        $returnText = substr($text, 0, $chars) . ' ...';

        return $returnText;
    }

    public function redirect(string $url): never
    {
        header("location: $url");
        exit();
    }
}
