<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PmuProxyController extends AbstractController
{
    private const PMU_API_URL = 'https://open-pmu-api.vercel.app/api/arrivees';

    #[Route('/api/pmu-results', methods: ['GET'])]
    public function results(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $query = array_filter([
            'date' => trim((string) $request->query->get('date', '')),
            'prix' => trim((string) $request->query->get('prix', '')),
            'hippo' => trim((string) $request->query->get('hippo', '')),
        ], static fn (string $value): bool => $value !== '');

        try {
            $response = $httpClient->request('GET', self::PMU_API_URL, ['query' => $query]);
            $statusCode = $response->getStatusCode();
            $payload = $response->toArray(false);

            if (!is_array($payload)) {
                $payload = ['error' => true, 'message' => 'Unexpected response from PMU API.'];
            }

            return new JsonResponse($payload, $statusCode);
        } catch (TransportExceptionInterface $exception) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Unable to reach PMU API.',
                'details' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_GATEWAY);
        } catch (\Throwable $exception) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Unexpected proxy error.',
                'details' => $exception->getMessage(),
            ], JsonResponse::HTTP_BAD_GATEWAY);
        }
    }
}
