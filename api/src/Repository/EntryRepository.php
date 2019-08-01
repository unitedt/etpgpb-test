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
    private $logger;

    /**
     * EntryRepository constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->logger =$logger;
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
        return $this->bulkInsert($this->transformXmlByLevels($iterator));
    }

    /**
     * @param iterable $iterator
     * @return array
     */
    private function transformXmlByLevels(iterable $iterator): array
    {
        $entriesData = [];

        foreach ($iterator as $array) {
            $level = empty((string)$array->Kod) ? 0 : substr_count((string)$array->Idx, '.');
            $entriesData[$level][] = $array;
        }

        return $entriesData;
    }

    /**
     * @param array $entriesData
     * @return int count of entries inserted
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function bulkInsert(array $entriesData = []): int
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $conn->executeQuery('TRUNCATE TABLE ' . $em->getClassMetadata(Entry::class)->getTableName());

        $i = 0;
        $levels = \count($entriesData);

        for ($n = 0; $n < $levels; $n++) {
            foreach ($entriesData[$n] as $data) {
                $entry = new Entry();
                $entry->setGlobalId((int)$data->global_id);
                $entry->setName((string)$data->Name);
                $entry->setIdx((string)$data->Idx);
                $entry->setKod((string)$data->Kod);
                $entry->setNomdescr((string)$data->Nomdescr);
                $entry->setRazdel((string)$data->Razdel);

                if (null !== ($parent = $this->findParentByIdx((string)$data->Idx))) {
                    $entry->setParent($parent);
                }

                $em->persist($entry);

//            if (($i % self::INSERT_BATCH_SIZE) === 0) {
                $em->flush();
                $em->clear();
//            }

                ++$i;
            }
        }

//        $em->flush(); //Persist objects that did not make up an entire batch
//        $em->clear();

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
        $criteria->where(Criteria::expr()->startsWith('idx', $codeTerm))->orderBy(['lvl' => 'ASC']);
        return $this->matching($criteria);
    }

    /**
     * @param string $nameTerm
     * @return Collection
     */
    public function findByName(string $nameTerm): Collection
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->contains('name', $nameTerm))->orderBy(['lvl' => 'ASC']);
        return $this->matching($criteria);
    }
}
