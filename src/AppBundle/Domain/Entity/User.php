<?php
namespace AppBundle\Domain\Entity;

use AppBundle\Domain\Interfaces\Entity\HasIdInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * The User entity class.
 *
 * @author Max Humme <max@humme.nl>
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="user",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="username_index", columns={"username"}),
 *         @ORM\UniqueConstraint(name="api_token_index", columns={"api_token"}),
 *     },
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"}
 * )
 */
class User extends AbstractEntity implements HasIdInterface
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
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191)
     */
    private $apiToken;

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
     * Set username
     *
     * @param string $username
     * @return \AppBundle\Domain\Entity\User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     * @return \AppBundle\Domain\Entity\User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return \AppBundle\Domain\Entity\User
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
     * @return \AppBundle\Domain\Entity\User
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
