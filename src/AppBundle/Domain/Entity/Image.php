<?php
namespace AppBundle\Domain\Entity;

use AppBundle\Domain\Interfaces\Entity\HasIdInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * The Image entity class.
 *
 * This class is not marked as 'final' because that keyword does not play nice with Doctrine relations and flushing
 * a newly set Image to a ThreeDPrint.
 *
 * @author Max Humme <max@humme.nl>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="image",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="public_id_index", columns={"public_id"})},
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"}
 * )
 */
class Image extends AbstractEntity implements HasIdInterface
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The public identifier.
     *
     * To be used in urls, so we don't need to expose $id.
     *
     * @var int
     *
     * @ORM\Column(type="string", length=191)
     */
    private $publicId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $mimeType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $fileByteSize;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $originalFileName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function equals(HasIdInterface $entity): bool
    {
        return $this->getId() == $entity->getId();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set publicId
     *
     * @param string $publicId
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;

        return $this;
    }

    /**
     * Get publicId
     *
     * @return string
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set fileByteSize
     *
     * @param integer $fileByteSize
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setFileByteSize($fileByteSize)
    {
        $this->fileByteSize = $fileByteSize;

        return $this;
    }

    /**
     * Get fileByteSize
     *
     * @return int
     */
    public function getFileByteSize()
    {
        return $this->fileByteSize;
    }

    /**
     * Set originalFileName
     *
     * @param string $originalFileName
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    /**
     * Get originalFileName
     *
     * @return string
     */
    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return \AppBundle\Domain\Entity\Image
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
