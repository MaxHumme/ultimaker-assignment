<?php
namespace AppBundle\Domain\Entity;

use AppBundle\Domain\Interfaces\Entity\HasIdInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * ThreeDPrint
 *
 * "Print" is not allowed as a class name in PHP, as is "3DPrint",
 * so we go for the not so nice name "ThreeDPrint"...
 *
 * @author Max Humme <max@humme.nl>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="print",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="public_id_index", columns={"public_id"})},
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"}
 * )
 */
final class ThreeDPrint extends AbstractEntity implements HasIdInterface
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
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $publicId;

    /**
     * Many ThreeDPrints belong to one User.
     *
     * @var \AppBundle\Domain\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * One ThreeDPrint has one Image.
     *
     * @var \AppBundle\Domain\Entity\Image
     *
     * @ORM\OneToOne(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

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
     * @return \AppBundle\Domain\Entity\ThreeDPrint
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
     * Set title
     *
     * @param string $title
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set user
     *
     * @param \AppBundle\Domain\Entity\User $user
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Domain\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Domain\Entity\Image $image
     * @return \AppBundle\Domain\Entity\ThreeDPrint
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Domain\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return \AppBundle\Domain\Entity\ThreeDPrint
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
     * @return \AppBundle\Domain\Entity\ThreeDPrint
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
