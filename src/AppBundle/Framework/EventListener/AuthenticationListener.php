<?php
namespace AppBundle\Framework\EventListener;

use AppBundle\Domain\Interfaces\Service\UserServiceInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AuthenticationListener
 *
 * Listens for a new request and tries to authorize the user given his/her credentials.
 *
 * @author Max Humme <max@humme.nl>
 */
final class AuthenticationListener
{
    /**
     * @var \AppBundle\Domain\Interfaces\Service\UserServiceInterface
     */
    private $userService;

    /**
     * AuthenticationListener constructor.
     *
     * @param \AppBundle\Domain\Interfaces\Service\UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Listens for a new request.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException when the user is not authenticated.
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $apiToken = $request->headers->get('Authorization');
        $user = $this->userService->authenticate($apiToken);
        if (is_null($user)) {
            throw new AccessDeniedHttpException('Sorry, you are not authenticated to use this service.');
        }
    }
}
