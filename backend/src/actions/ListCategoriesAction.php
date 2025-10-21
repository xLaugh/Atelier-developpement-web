<?php
declare(strict_types=1);

namespace App\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\application\ports\api\ServiceCategoryInterface;

class ListCategoriesAction
{
    public function __construct(
        private ServiceCategoryInterface $serviceCategory
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $categories = $this->serviceCategory->listerCategories();

            $items = array_map(function ($category) {
                return $category->toArray();
            }, $categories);

            $payload = [
                'count' => count($items),
                'items' => $items,
                '_links' => ['self' => ['href' => '/api/categories']],
            ];

            $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'server_error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
