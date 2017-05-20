<?php
namespace AppBundle\Infrastructure\Repository;

use AppBundle\Domain\Entity\Image;
use AppBundle\Domain\Interfaces\Repository\ImageRepositoryInterface;
use AppBundle\Infrastructure\Interfaces\HasPublicIdInterface;
use AppBundle\Infrastructure\Traits\HasPublicIdTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

/**
 * Class ImageRepository
 *
 * Responsible for retrieving, adding and deleting Images.
 *
 * @author Max Humme <max@humme.nl>
 */
final class ImageRepository extends AbstractEntityRepository implements ImageRepositoryInterface, HasPublicIdInterface
{
    use HasPublicIdTrait;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $doctrineRepository;

    /**
     * @var MimeTypeExtensionGuesser
     */
    private $extensionGuesser;

    /**
     * ImageRepository constructor.
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param \Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser $extensionGuesser
     */
    public function __construct(Registry $doctrine, MimeTypeExtensionGuesser $extensionGuesser)
    {
        parent::__construct($doctrine->getManager());

        $this->doctrineRepository = $doctrine->getRepository('AppBundle:Image');
        $this->extensionGuesser = $extensionGuesser;
    }

    /** @inheritdoc */
    public function add(Image $image): Image
    {
        $imagePublicId = $this->newPublicId();
        $image->setPublicId($imagePublicId);

        $imageFileName = $this->newImageFileName($image->getMimeType());
        $image->setFileName($imageFileName);

        $this->doctrineManager->persist($image);
        $this->doctrineManager->flush();

        // Load $image in memory, so we don't have to query the database if we need it again this request.
        $this->load($image);

        return $image;
    }

    /** @inheritdoc */
    public function delete(Image $image)
    {
        $this->unload($image);
        $this->doctrineManager->remove($image);
        $this->doctrineManager->flush();
    }

    /** @inheritdoc */
    public function newImageFileName($mimeType): string
    {
        do {
            $newFileName = $this->generateFileName();
        } while (!$this->fileNameIsUnique($newFileName));

        $extension = $this->extensionGuesser->guess($mimeType);

        return $newFileName.'.'.$extension;
    }

    /**
     * Checks if $newFileName does not exist in this repository.
     *
     * @param string $newFileName
     * @return bool
     */
    private function fileNameIsUnique($newFileName)
    {
        return is_null($this->doctrineRepository->findOneByFileName($newFileName));
    }

    /**
     * Generates a new file name.
     *
     * @return string
     */
    private function generateFileName()
    {
        $fileName = '';
        $characters = 'bcdfghjklmnpqrstvwxyz123456789';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < 32; $i++) {
            $fileName .= $characters[mt_rand(0, $max)];
        }

        return $fileName;
    }
}
