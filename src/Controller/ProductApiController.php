<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductApiController extends AbstractController
{
    #[Route('/api/products', name: 'api_products_list', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(100, max(1, (int) $request->query->get('limit', 20)));

        $offset = ($page - 1) * $limit;

        $qb = $em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('p.id', 'DESC');

        $paginator = new Paginator($qb);

        $items = [];
        foreach ($paginator as $product) {
            /** @var Product $product */
            $items[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => (float) $product->getPrice(),
                'imageUrl' => $product->getImageUrl(),
                'productUrl' => $product->getProductUrl(),
            ];
        }

        return $this->json([
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total_items' => count($paginator),
                'total_pages' => ceil(count($paginator) / $limit),
            ],
        ]);
    }
}