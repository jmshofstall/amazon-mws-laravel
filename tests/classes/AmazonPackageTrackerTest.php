<?php

use Jmshofstall\AmazonMws\AmazonPackageTracker;
use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-12-12 at 13:17:14.
 */
class AmazonPackageTrackerTest extends TestCase
{
    /**
     * @var AmazonPackageTracker
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        resetLog();
        $this->object = new AmazonPackageTracker('testStore', null, true, null);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testSetUp()
    {
        $obj = new AmazonPackageTracker('testStore', '77');

        $o = $obj->getOptions();
        $this->assertArrayHasKey('PackageNumber', $o);
        $this->assertEquals('77', $o['PackageNumber']);
    }

    public function testSetPackageNumber()
    {
        $this->assertNull($this->object->setPackageNumber(777));
        $o = $this->object->getOptions();
        $this->assertArrayHasKey('PackageNumber', $o);
        $this->assertEquals(777, $o['PackageNumber']);
        $this->assertNull($this->object->setPackageNumber('777')); //works for number strings
        $this->assertFalse($this->object->setPackageNumber('five')); //but not other strings
        $this->assertFalse($this->object->setPackageNumber(null)); //won't work for other things
    }

    public function testFetchTrackingDetails()
    {
        resetLog();
        $this->object->setMock(true, 'fetchTrackingDetails.xml');

        $this->assertFalse($this->object->fetchTrackingDetails()); //no package ID set yet

        $this->object->setPackageNumber('777');
        $ok = $this->object->fetchTrackingDetails(); //now it is good
        $this->assertNull($ok);

        $o = $this->object->getOptions();
        $this->assertEquals('GetPackageTrackingDetails', $o['Action']);

        $check = parseLog();
        $this->assertEquals('Single Mock File set: fetchTrackingDetails.xml', $check[1]);
        $this->assertEquals('Package Number must be set in order to fetch it!', $check[2]);

        return $this->object;
    }

    /**
     * @depends testFetchTrackingDetails
     */
    public function testGetDetails($o)
    {
        $get = $o->getDetails();
        $this->assertIsArray($get);

        $x = [];
        $x['PackageNumber'] = '42343';
        $x['TrackingNumber'] = '3A18351E0390447173';
        $x['CarrierCode'] = 'UPS';
        $x['CarrierPhoneNumber'] = '206-000-0000';
        $x['CarrierURL'] = 'http://www.ups.com/';
        $x['ShipDate'] = '2012-03-09T10:27:10Z';
        $x['ShipToAddress']['City'] = 'Seattle';
        $x['ShipToAddress']['State'] = 'WA';
        $x['ShipToAddress']['Country'] = 'US';
        $x['CurrentStatus'] = 'DELIVERED';
        $x['SignedForBy'] = 'John';
        $x['EstimatedArrivalDate'] = '2012-03-09T10:00:00Z';
        $x['TrackingEvents'][0]['EventDate'] = '2012-03-09T08:48:53Z';
        $x['TrackingEvents'][0]['EventAddress']['City'] = 'Reno';
        $x['TrackingEvents'][0]['EventAddress']['State'] = 'NV';
        $x['TrackingEvents'][0]['EventAddress']['Country'] = 'US';
        $x['TrackingEvents'][0]['EventCode'] = 'EVENT_202';
        $x['TrackingEvents'][1]['EventDate'] = '2012-03-10T10:27:10Z';
        $x['TrackingEvents'][1]['EventAddress']['City'] = 'Seattle';
        $x['TrackingEvents'][1]['EventAddress']['State'] = 'WA';
        $x['TrackingEvents'][1]['EventAddress']['Country'] = 'US';
        $x['TrackingEvents'][1]['EventCode'] = 'EVENT_301';
        $x['AdditionalLocationInfo'] = 'FRONT_DESK';

        $this->assertEquals($x, $get);

        $this->assertFalse($this->object->getDetails()); //not fetched yet for this object
    }
}

require_once __DIR__.'/../helperFunctions.php';
