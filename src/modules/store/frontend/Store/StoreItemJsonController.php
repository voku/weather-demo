<?php declare(strict_types=1);

namespace WeatherApp\modules\store\frontend\Store;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WeatherApp\modules\store\entities\StoreWeather;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface;

class StoreItemJsonController
{
    public function __construct(
        private readonly ResponseInterface $response,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly StoreWeatherRepositoryInterface $storeWeatherRepository
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $storeViewData = $this->storeRepository->fetchByIdIfExists($id);

        $storeWeatherCurrentViewData = $this->storeWeatherRepository->fetchByStoreIdIfExists($id, StoreWeather::DATA_TYPE_CURRENT);

        $storeWeatherHistoricalAvgViewData = $this->storeWeatherRepository->fetchByStoreIdAndWeekIfExists(
            $id,
            StoreWeather::DATA_TYPE_HISTORICAL,
            (int) (new \DateTimeImmutable())->modify('+3 day')->format('W')
        );

        $storeWeatherFutureViewData = $this->storeWeatherRepository->fetchByStoreIdIfExists($id, StoreWeather::DATA_TYPE_FUTURE);

        $response = $this->response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode([
            'store'          => $storeViewData,
            'weather'        => $storeWeatherCurrentViewData,
            'avg_weather'    => $storeWeatherHistoricalAvgViewData,
            'future_weather' => $storeWeatherFutureViewData,
        ], \JSON_THROW_ON_ERROR));

        return $response;
    }
}
