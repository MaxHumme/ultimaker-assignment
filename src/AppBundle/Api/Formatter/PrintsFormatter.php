<?php
namespace AppBundle\Api\Formatter;

use AppBundle\Domain\Entity\ThreeDPrint;
use RuntimeException;

/**
 * Class PrintsFormatter
 *
 * Responsible form formatting ThreeDPrints to an associative array.
 * This array can be used as input for a Json response.
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintsFormatter extends AbstractFormatter
{
    /**
     * @var string
     */
    private $imagesBaseUrl;

    /**
     * @var \AppBundle\Domain\Entity\ThreeDPrint[]
     */
    private $prints;

    /**
     * PrintsFormatter constructor.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint[] $prints
     * @param string $imagesBaseUrl
     * @throws \RuntimeException when not all $prints are ThreeDPrints.
     */
    public function __construct(array $prints, $imagesBaseUrl)
    {
        foreach ($prints as $print) {
            if (!is_a($print, ThreeDPrint::class)) {
                throw new RuntimeException("Object of class '".ThreeDPrint::class."' expected. Got ".get_class($print).".");
            }
        }

        $this->prints = $prints;
        $this->imagesBaseUrl = $imagesBaseUrl;
    }

    /** @inheritdoc */
    public function render(): array
    {
        $formattedPrints = [];
        foreach ($this->prints as $print) {
            $formattedPrints[] = [
                'id' => $print->getPublicId(),
                'user' => [
                    'username' => $print->getUser()->getUsername()
                ],
                'title' => $print->getTitle(),
                'imageUrl' => $this->imagesBaseUrl.'/'.$print->getImage()->getFileName(),
                'dateTime' => $print->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return $formattedPrints;
    }
}
