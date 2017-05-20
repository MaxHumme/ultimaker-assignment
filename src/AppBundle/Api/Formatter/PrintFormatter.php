<?php
namespace AppBundle\Api\Formatter;

use AppBundle\Domain\Entity\ThreeDPrint;

/**
 * Class PrintFormatter
 *
 * Responsible form formatting a ThreeDPrint to an associative array.
 * This array can be used as input for a Json response.
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintFormatter extends AbstractFormatter
{
    /**
     * @var string
     */
    private $imagesBaseUrl;

    /**
     * @var \AppBundle\Domain\Entity\ThreeDPrint
     */
    private $print;

    /**
     * PrintFormatter constructor.
     *
     * @param \AppBundle\Domain\Entity\ThreeDPrint $print
     * @param string $imagesBaseUrl
     */
    public function __construct(ThreeDPrint $print, $imagesBaseUrl)
    {
        $this->print = $print;
        $this->imagesBaseUrl = $imagesBaseUrl;
    }

    /** @inheritdoc */
    public function render(): array
    {
        $formattedPrint = [
            'id' => $this->print->getPublicId(),
            'user' => [
                'username' => $this->print->getUser()->getUsername()
            ],
            'title' => $this->print->getTitle(),
            'description' => $this->print->getDescription(),
            'imageUrl' => $this->imagesBaseUrl.'/'.$this->print->getImage()->getFileName(),
            'dateTime' => $this->print->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        return $formattedPrint;
    }
}
