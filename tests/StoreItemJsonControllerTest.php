<?php

use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use WeatherApp\framework\TerminalKernel;
use WeatherApp\modules\store\frontend\Store\StoreItemJsonController;

/**
 * @internal
 */
final class StoreItemJsonControllerTest extends TestCase
{
    public static TerminalKernel $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TerminalKernel();
        self::$kernel->boot();
    }

    public function testController(): void
    {
        $storeItemJsonController = self::$kernel->getContainer()->get('WeatherApp\modules\store\frontend\Store\StoreItemJsonController');
        \assert($storeItemJsonController instanceof StoreItemJsonController);

        $request = ServerRequestFactory::fromGlobals()->withAttribute('id', 1);
        $response = $storeItemJsonController->__invoke($request);
        $body = $response->getBody();
        $body->rewind();
        $content = $body->getContents();
        static::assertStringContainsString('"name":"Brauhaus M\u00fcnchen"', $content);
    }
}
