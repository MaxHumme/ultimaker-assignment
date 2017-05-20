<?php
namespace AppBundle\Infrastructure\Repository;

use AppBundle\Domain\Entity\AbstractEntity;
use AppBundle\Domain\Entity\ThreeDPrint;
use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Interfaces\Repository\PrintRepositoryInterface;
use AppBundle\Infrastructure\Interfaces\HasPublicIdInterface;
use AppBundle\Infrastructure\Traits\HasPublicIdTrait;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class PrintRepository
 *
 * Responsible for retrieving, adding and deleting ThreeDPrints.
 *
 * @author Max Humme <max@humme.nl>
 */
final class PrintRepository extends AbstractEntityRepository implements PrintRepositoryInterface, HasPublicIdInterface
{
    use HasPublicIdTrait;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    private $doctrineRepository;

    /**
     * @var \AppBundle\Infrastructure\Repository\ImageRepository
     */
    private $imageRepository;

    /**
     * PrintRepository constructor.
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param \AppBundle\Infrastructure\Repository\ImageRepository $imageRepository
     */
    public function __construct(Registry $doctrine, ImageRepository $imageRepository)
    {
        parent::__construct($doctrine->getManager());

        $this->doctrineRepository = $doctrine->getRepository('AppBundle:ThreeDPrint');
        $this->imageRepository = $imageRepository;
    }

    /** @inheritdoc */
    public function add(ThreeDPrint $print): ThreeDPrint
    {
        $printPublicId = $this->newPublicId();
        $print->setPublicId($printPublicId);

        $this->imageRepository->add($print->getImage());

        $this->doctrineManager->persist($print);
        $this->doctrineManager->flush();

        // Load $print in memory, so we don't have to query the database if we need it again this request.
        $this->load($print);

        return $print;
    }

    /** @inheritdoc */
    public function delete(ThreeDPrint $print)
    {
        $this->unload($print);
        $this->doctrineManager->remove($print);
        $this->doctrineManager->flush();
    }

    /** @inheritdoc */
    public function getPrint($publicId): ?ThreeDPrint
    {
        // Try to find the print in memory, so we might not need to query the database
        $loadedPrints = $this->getLoadedEntities();
        foreach ($loadedPrints as $loadedPrint) {
            if ($this->loadedPrintMatchesPrint($loadedPrint, $publicId)) {
                return $loadedPrint;
            }
        }

        // Print not in memory, query persistence.
        $query = $this->doctrineRepository->createQueryBuilder('p')
            ->where('p.publicId = :publicId')
            ->setParameters(['publicId' => $publicId])
            ->getQuery();

        $print = $query->setMaxResults(1)->getOneOrNullResult();

        // Load $print in memory in case we need it again this request.
        if (!is_null($print)) {
            $this->load($print);
        }

        return $print;
    }

    /** @inheritdoc */
    public function getPrintsOfUser(User $user, $limit = 20, $afterPrintPublicId = null): array
    {
        // We don't try to find the prints in memory, because it would make finding the prints in memory and in the
        // database drastically more complex. So we just query the database.
        $queryBuilder = $this->doctrineRepository->createQueryBuilder('p');

        if (!is_null($afterPrintPublicId)) {
            $afterPrint = $this->getPrint($afterPrintPublicId);
            $queryBuilder = $queryBuilder
                ->andWhere('p.user = :user')
                ->andWhere('p.id < :afterPrintId')
                ->setParameters([
                    'user' => $user,
                    'afterPrintId' => $afterPrint->getId()
                ]);
        }

        $query = $queryBuilder->orderBy('p.id', 'DESC')->getQuery();
        $prints = $query->setMaxResults($limit)->getResult();

        // Load $prints in memory in case we need one of them again this request.
        foreach ($prints as $print) {
            $this->load($print);
        }

        return $prints;
    }

    /** @inheritdoc */
    protected function unload(AbstractEntity $print)
    {
        $image = $print->getImage();
        $this->imageRepository->unload($image);

        parent::unload($print);
    }

    /**
     * Checks if $loadedPrint matches a print with $publicId.
     *
     * @param string $loadedPrint
     * @param string $publicId
     * @return bool
     */
    private function loadedPrintMatchesPrint($loadedPrint, $publicId)
    {
        return $loadedPrint->getPublicId() == $publicId;
    }
}
