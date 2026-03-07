<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PmuProxyController extends AbstractController
{
    private const PMU_API_URL = 'https://open-pmu-api.vercel.app/api/arrivees';

    #[Route('/api/pmu-results', methods: ['GET'])]
    public function results(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $query = array_filter([
            'date' => $request->query->get('date'),
            'prix' => $request->query->get('prix'),
            'hippo' => $request->query->get('hippo'),
        ], static fn (?string $value): bool => $value !== null && $value !== '');

        try {
            $response = $httpClient->request('GET', self::PMU_API_URL, [
                'query' => $query,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            if (!is_array($data)) {
                $data = [
                    'error' => true,
                    'message' => 'Unexpected response from PMU API.',
                    'raw' => $content,
                ];
            }

            return $this->json($data, $statusCode);
        } catch (TransportExceptionInterface $exception) {
            return $this->json([
                'error' => true,
                'message' => 'Unable to reach PMU API.',
                'details' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_GATEWAY);
        } catch (\Throwable $exception) {
            return $this->json([
                'error' => true,
                'message' => 'Unexpected proxy error.',
                'details' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_GATEWAY);
        }
    }
}
