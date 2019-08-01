<?php

namespace App\Repository;

use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;

/**
 * EntryRepository
 */
class EntryRepository extends NestedTreeRepository implements ServiceEntityRepositoryInterface
{
    private const INSERT_BATCH_SIZE = 50;

    /**
     * EntryRepository constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(Entry::class));
    }


    /**
     * Clear and populate entry table with data from uploaded XML file
     * @param iterable $iterator
     * @return int count of entries inserted
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function bulkInsertFromXml(iterable $iterator): int
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $conn->executeQuery('TRUNCATE TABLE ' . $em->getClassMetadata(EtlEntry::class)->getTableName());

        $i = 0;

        foreach ($iterator as $array) {
            $entry = new EtlEntry();
            $entry->setGlobalId((int) $array->global_id);
            $entry->setName((string) $array->Name);
            $entry->setIdx((string) $array->Idx);
            $entry->setKod((string) $array->Kod);
            $entry->setNomdescr((string) $array->Nomdescr);
            $entry->setRazdel((string) $array->Razdel);

            $em->persist($entry);

            if (($i % self::INSERT_BATCH_SIZE) === 0) {
                $em->flush();
                $em->clear();
            }

            ++$i;
        }

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();


        return $i;
    }

    /**
     * @param string $idx
     * @return Entry|null
     */
    public function findParentByIdx(string $idx): ?Entry
    {
        $arr = array_filter(explode('.', $idx), function($v) { return($v !== ''); });
        array_pop($arr);
        $parent = \count($arr) === 1 ? $arr[0] . '.' : implode('.', $arr);

        return $this->findOneBy(['idx' => $parent]);
    }

    /**
     * @param string $codeTerm
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findByCode(string $codeTerm): Collection
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->startsWith('idx', $codeTerm));
        return $this->matching($criteria);
    }

    /**
     * @param string $nameTerm
     * @return Collection
     */
    public function findByName(string $nameTerm): Collection
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->contains('name', $nameTerm));
        return $this->matching($criteria);
    }
}
