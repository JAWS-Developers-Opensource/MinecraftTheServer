<?php

/**
 * This class is used to manage dates
 *
 * @author Timo Coupek | JAWS Developers
 * @version 31.03.2023
 */
class Dates
{
    /**
     * @param string $date
     * @param string $format can be edited in Y or m or d to check only one of thoese
     * @return bool true if is a date
     */
    public static function ValidateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}