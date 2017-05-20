<?php
namespace AppBundle\Domain\Factory;

use AppBundle\Domain\Entity\ThreeDPrint;
use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface;
use AppBundle\Domain\Interfaces\Factory\PrintFactoryInterface;
use DateTime;

/**
 * Class PrintFactory
 *
 * {@inheritdoc}
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintFactory implements PrintFactoryInterface
{
    /**
     * @var \AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface
     */
    private $imageFactory;

    /**
     * PrintFactory constructor.
     *
     * @param \AppBundle\Domain\Interfaces\Factory\ImageFactoryInterface $imageFactory
     */
    public function __construct(ImageFactoryInterface $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    /** @inheritdoc */
    public function create(
        User $user,
        $title,
        $description,
        $fileOriginalName,
        $fileByteSize,
        $fileMimeType
    ): ThreeDPrint
    {

        $print = new ThreeDPrint();
        $print->setUser($user);

        $image = $this->imageFactory->create($fileOriginalName, $fileByteSize, $fileMimeType);
        $print->setImage($image);
        $print->setTitle($title);
        $print->setDescription($description);

        $now = new DateTime();
        $print->setCreatedAt($now);
        $print->setUpdatedAt($now);

        return $print;
    }
}
