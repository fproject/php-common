<?php
use fproject\common\utils\DateTimeHelper;

class DateTimeHelperTest extends PHPUnit_Framework_TestCase
{
    private $params = [];

    public function testJsonEncode()
    {
        $d = new DateTime('2012-01-01T12:12:12');
        $s = DateTimeHelper::toISO8601UTC($d);
        $this->assertEquals("2012-01-01T12:12:12",$s);
    }
}
?>