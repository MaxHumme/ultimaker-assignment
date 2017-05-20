<?php
namespace AppBundle\Api\Controller;

use AppBundle\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract class AbstractApiController
 *
 * @author Max Humme <max@humme.nl>
 */
abstract class AbstractApiController extends Controller
{
    /**
     * Gets the input from the request or query input.
     *
     * So we don't need to check both.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $key
     * @return null|string
     */
    protected function getInput(Request $request, $key): ?string
    {
        $input = $request->query->get($key);
        if (is_null($input)) {
            $input = $request->request->get($key);
        }

        return $input;
    }

    /**
     * Checks if the request or query input have the input for $key.
     *
     * So we don't need to check both.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $key
     * @return bool
     */
    protected function hasInput(Request $request, $key): bool
    {
        $hasInput = $request->query->has($key);
        if (!$hasInput) {
            $hasInput = $request->request->has($key);
        }

        return $hasInput;
    }

    /**
     * Creates a JsonResponse in a standard format.
     *
     * @param mixed[] $responseData
     * @param int $statusCode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function jsonResponse(array $responseData = [], $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = [];

        if (!empty($responseData)) {
            $response['data'] = $responseData;
        }

        $response['status'] = [
            'code' => $statusCode,
            'message' => Response::$statusTexts[$statusCode]
        ];

        return new JsonResponse($response, $statusCode);
    }

    /**
     * Returns the authenticated User.
     *
     * @return \AppBundle\Domain\Entity\User
     */
    protected function authenticatedUser(): User
    {
        return $this->container->get('app.user_service')->authenticatedUser();
    }
}
