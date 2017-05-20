<?php
namespace AppBundle\Infrastructure\Traits;

/**
 * Trait HasPublicIdTrait
 *
 * Use on repositories that have entities that have a publicId attribute.
 * Makes it easy to work with publicIds.
 *
 * @author Max Humme <max@humme.nl>
 */
trait HasPublicIdTrait
{
    /**
     * Make sure to set $doctrineRepository when constructing the class using this trait.
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $doctrineRepository;

    /**
     * Returns a new and unique publicId for the repository.
     *
     * @return string
     */
    public function newPublicId(): string
    {
        do {
            $newPublicId = $this->generatePublicId();
        } while (!$this->publicIdIsUnique($newPublicId));

        return $newPublicId;
    }

    /**
     * Checks if $publicId does not exist in the repository.
     *
     * @param string $publicId
     * @return bool
     */
    private function publicIdIsUnique($publicId): bool
    {
        $query = $this->doctrineRepository->createQueryBuilder('table')
            ->where('table.publicId = :publicId')
            ->setParameters(['publicId' => $publicId])
            ->getQuery();

        return is_null($query->setMaxResults(1)->getOneOrNullResult());
    }

    /**
     * Generates a new public id. Not necessarily unique in the repository.
     *
     * @return string
     */
    private function generatePublicId(): string
    {
        return (string) mt_rand(10000000, 99999999);
    }
}
