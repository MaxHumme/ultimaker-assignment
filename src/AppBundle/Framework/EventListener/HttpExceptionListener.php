<?php
namespace AppBundle\Framework\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class HttpExceptionListener
 *
 * Listens for a http exception and formats it so we get a nice json response.
 *
 * @author Max Humme <max@humme.nl>
 */
final class HttpExceptionListener
{
    /**
     * Listens for a http exception.
     *
     * If found, formats it so we get a nice json response.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // We get the data from the received event
        $exception = $event->getException();
        $data = ['error' => $exception->getMessage()];

        // Create a new JsonResponse to return
        $response = new JsonResponse();

        // Dress the JsonResponse for a http exception
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $response->setStatusCode($statusCode);
        // Dress the JsonResponse for an internal server error
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $response->setStatusCode($statusCode);
        }

        $data['status'] = ['code' => $statusCode, 'message' => Response::$statusTexts[$statusCode]];
        $response->setData($data);

        // Send the modified response object to the event
        $event->setResponse($response);
    }
}
