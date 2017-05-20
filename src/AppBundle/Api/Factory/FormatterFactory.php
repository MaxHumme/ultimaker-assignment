<?php
namespace AppBundle\Api\Factory;

use AppBundle\Api\Formatter\PrintFormatter;
use AppBundle\Api\Formatter\PrintsFormatter;
use AppBundle\Api\Interfaces\Factory\FormatterFactoryInterface;
use AppBundle\Domain\Entity\ThreeDPrint;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FormatterFactory
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class FormatterFactory implements FormatterFactoryInterface
{
    /**
     * @var string
     */
    private $imagesBaseUrl;

    /**
     * FormatterFactory constructor.
     *
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param string $imagesBaseUrl
     */
    public function __construct(RequestStack $requestStack, $imagesBaseUrl)
    {
        $request = $requestStack->getCurrentRequest();
        $this->imagesBaseUrl = $request->getScheme().'://'.$request->getHttpHost().$imagesBaseUrl;
    }

    /** @inheritdoc */
    public function createPrintFormatter(ThreeDPrint $print): PrintFormatter
    {
        return new PrintFormatter($print, $this->imagesBaseUrl);
    }

    /** @inheritdoc */
    public function createPrintsFormatter(array $prints): PrintsFormatter
    {
        return new PrintsFormatter($prints, $this->imagesBaseUrl);
    }
}
