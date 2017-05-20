<?php
namespace AppBundle\Infrastructure\Repository;

use AppBundle\Domain\Entity\AbstractEntity;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Abstract class AbstractEntityRepository
 *
 * Subclass entity repositories from this abstract class.
 * It loads queried entities so we don't need to query the database again for already fetched entities.
 *
 * @author Max Humme <max@humme.nl>
 */
abstract class AbstractEntityRepository
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $doctrineManager;

    /**
     * @var \AppBundle\Domain\Entity\AbstractEntity[]
     */
    private $loadedEntities = [];

    /**
     * AbstractEntityRepository constructor.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $doctrineManager
     */
    public function __construct(ObjectManager $doctrineManager)
    {
        $this->doctrineManager = $doctrineManager;
    }

    /**
     * Updates the changes in all entities to persistence.
     */
    public function update()
    {
        $this->doctrineManager->flush();
    }

    /**
     * Returns the loaded entities.
     *
     * @return \AppBundle\Domain\Entity\AbstractEntity[]
     */
    protected function getLoadedEntities(): array
    {
        return $this->loadedEntities;
    }

    /**
     * Loads $entity into memory.
     *
     * @param \AppBundle\Domain\Entity\AbstractEntity $entity
     */
    protected function load(AbstractEntity $entity)
    {
        foreach ($this->loadedEntities as $loadedEntity) {
            if ($this->entitiesAreEqual($loadedEntity, $entity)) {
                return;
            }
        }

        $this->loadedEntities[] = $entity;
    }

    /**
     * Loads $entity from memory.
     *
     * @param \AppBundle\Domain\Entity\AbstractEntity $entity
     */
    protected function unload(AbstractEntity $entity)
    {
        foreach ($this->loadedEntities as $key => $loadedEntity) {
            if ($this->entitiesAreEqual($loadedEntity, $entity)) {
                unset($this->loadedEntities[$key]);
                $this->loadedEntities = array_values($this->loadedEntities);
                return;
            }
        }
    }

    /**
     * Checks if $loadedEntity and $entity are equal.
     *
     * @param \AppBundle\Domain\Entity\AbstractEntity $firstEntity
     * @param \AppBundle\Domain\Entity\AbstractEntity $secondEntity
     * @return bool
     */
    private function entitiesAreEqual(AbstractEntity $firstEntity, AbstractEntity $secondEntity)
    {
        return $firstEntity->equals($secondEntity);
    }
}
