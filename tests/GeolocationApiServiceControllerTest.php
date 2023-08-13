<?php

use PHPUnit\Framework\TestCase;
use WeatherApp\framework\kernel\TerminalKernel;
use WeatherApp\modules\store\services\GeolocationApiOpenStreetMapService;

/**
 * @internal
 */
final class GeolocationApiServiceControllerTest extends TestCase
{
    public static TerminalKernel $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TerminalKernel();
        self::$kernel->boot();
    }

    public function testGeolocationApiOpenStreetMapService(): void
    {
        $coordinates = (new GeolocationApiOpenStreetMapService())->getCoordinates(
            'Bahnhofstr.', '6', '46562', 'Voerde', 'Germany'
        );
        self::assertSame(
            ['latitude' => '51.6027747', 'longitude' => '6.70254'],
            $coordinates
        );
    }
}
