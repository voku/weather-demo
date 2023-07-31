<?php declare(strict_types=1);

namespace WeatherApp\modules\store\frontend\Store;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WeatherApp\modules\store\entities\StoreWeather;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;
use WeatherApp\modules\store\repositories\StoreWeatherRepositoryInterface;
use WeatherApp\modules\store\services\GeolocationApiServiceInterface;
use WeatherApp\modules\weather_importer\services\WeatherSaveService;

class StoreItemEditController
{
    public function __construct(
        private readonly ResponseInterface $response,
        private readonly StoreRepositoryInterface $storeRepository,
        private readonly StoreWeatherRepositoryInterface $storeWeatherRepository,
        private readonly GeolocationApiServiceInterface $geolocationApiService,
        private readonly WeatherSaveService $weatherSaveService
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $storeForm = $request->getParsedBody()['store'];

        $store = $this->storeRepository->fetchById($id);

        if (
            $store->city !== $storeForm['city']
            ||
            $store->street !== $storeForm['street']
            ||
            $store->zip !== $storeForm['zip']
        ) {
            $geoData = $this->geolocationApiService->getCoordinates(
                $storeForm['street'],
                $storeForm['houseNo'],
                $storeForm['city'],
                $storeForm['zip'],
                'Germany'
            );
            if (!$geoData) {
                return $this->response->withStatus(302)->withHeader('Location', '/store/' . $id . '/1?wrong_address');
            }

            $latitude = (float) $geoData['latitude'];
            $longitude = (float) $geoData['longitude'];
        } else {
            $latitude = $store->latitude;
            $longitude = $store->longitude;
        }

        $this->storeRepository->update(
            $store->id,
            $storeForm['name'],
            $storeForm['street'],
            $storeForm['houseNo'],
            $storeForm['zip'],
            $storeForm['city'],
            $latitude,
            $longitude
        );

        $this->weatherSaveService->saveCurrentWeatherInfoForStore($store->id);

        $this->weatherSaveService->saveFutureWeatherInfoForStore($store->id);

        $this->weatherSaveService->saveTodayWeatherInfoForStore($store->id);

        $this->storeWeatherRepository->deleteAllByStoreId($store->id, StoreWeather::DATA_TYPE_HISTORICAL);

        return $this->response->withStatus(302)->withHeader('Location', '/store/' . $id . '?saved');
    }
}
