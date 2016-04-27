<?php
///////////////////////////////////////////////////////////////////////////////
//
// Licensed Source Code - Property of ProjectKit.net
//
// © Copyright ProjectKit.net 2013. All Rights Reserved.
//
///////////////////////////////////////////////////////////////////////////////
/**
 * The Date-Time Helper class
 *
 * @author Bui Sy Nguyen <nguyenbs@gmail.com>
 */
class DateTimeHelper
{
    const DATE_ISO8601_UTC = "Y-m-d\TH:i:s";
    
    /**
     * Format a date-time using ISO 8601 UTC
     * @param mixed $d
     * @return string
     */
    public static function toISO8601UTC($d)
    {
        if($d instanceof DateTime)
            return $d->format(self::DATE_ISO8601_UTC);
        return $d;
    }

    /**
     * Return current date-time as a ISO 8601 UTC serial
     * @return bool|string
     */
    public static function currentDateTime()
    {
        return date(self::DATE_ISO8601_UTC, time());
    }
}