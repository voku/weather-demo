<?php declare(strict_types=1);

namespace WeatherApp\modules\store\frontend\StoreList;

use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use WeatherApp\modules\store\repositories\StoreRepositoryInterface;

class StoreListShowController
{
    public function __construct(
        private Environment $twig,
        private ResponseInterface $response,
        private StoreRepositoryInterface $storeRepository
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        $storeListViewData = $this->storeRepository->all();

        $view = $this->twig->render(
            'storeList.twig',
            [
                'stores' => $storeListViewData,
            ]
        );

        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write($view);

        return $response;
    }
}
