<?php declare(strict_types=1);

namespace WeatherApp\modules\store\frontend\Store;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;
use WeatherApp\modules\store\entities\StoreWeather;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface;

class StoreItemShowController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ResponseInterface $response,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly StoreWeatherRepositoryInterface $storeWeatherRepository
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $edit = (bool) $request->getAttribute('edit');

        $storeViewData = $this->storeRepository->fetchByIdIfExists($id);

        $storeWeatherCurrentViewData = $this->storeWeatherRepository->fetchByStoreIdIfExists($id, StoreWeather::DATA_TYPE_CURRENT);

        $storeWeatherHistoricalAvgViewData = $this->storeWeatherRepository->fetchByStoreIdAndWeekIfExists(
            $id,
            StoreWeather::DATA_TYPE_HISTORICAL,
            (int) (new \DateTimeImmutable())->modify('+3 day')->format('W')
        );

        $storeWeatherFutureViewData = $this->storeWeatherRepository->fetchByStoreIdIfExists($id, StoreWeather::DATA_TYPE_FUTURE);

        $view = $this->twig->render(
            'store.twig',
            [
                'store'          => $storeViewData,
                'weather'        => $storeWeatherCurrentViewData,
                'avg_weather'    => $storeWeatherHistoricalAvgViewData,
                'future_weather' => $storeWeatherFutureViewData,
                'edit'           => $edit,
            ],
        );

        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write($view);

        return $response;
    }
}
