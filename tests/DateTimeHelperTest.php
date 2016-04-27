<?php
use fproject\common\utils\DateTimeHelper;

class DateTimeHelperTest extends PHPUnit_Framework_TestCase
{
    public function testToISO8601UTC()
    {
        $newTZ = new DateTimeZone("America/New_York");
        $d = new DateTime('2012-01-01T12:12:12', $newTZ);
        $s = DateTimeHelper::toISO8601UTC($d);
        $this->assertEquals("2012-01-01T12:12:12",$s);
    }
}
?>