<?php

namespace App\Repository;

use App\Entity\EtlEntry;
use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;

/**
 * EtlEntryRepository
 */
class EtlEntryRepository extends ServiceEntityRepository
{
    private const INSERT_BATCH_SIZE = 100;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EntryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($registry, EtlEntry::class);
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
            $entry->setLevel(empty($entry->getKod()) ? 0 : substr_count($entry->getIdx(), '.'));
//            $entry->setPath(array_merge([$entry->getRazdel()], array_filter(explode('.', $entry->getKod()))));

            $em->persist($entry);

            if (($i % self::INSERT_BATCH_SIZE) === 0) {
                $em->flush();
                $em->clear();
            }

            ++$i;
        }

        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();

        $this->bulkTransform();

        return $i;
    }

    private function bulkTransform(): int
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $conn->executeQuery('TRUNCATE TABLE ' . $em->getClassMetadata(Entry::class)->getTableName());

        $i = 0;

        $q = $em->createQuery('select e from App\Entity\EtlEntry e order by e.level, e.globalId');
        $iterableResult = $q->iterate();

        foreach ($iterableResult as $row) {
            /**
             * @var EtlEntry $etlEntry
             */
            $etlEntry = $row[0];

            $entry = new Entry();
            $entry->setGlobalId($etlEntry->getGlobalId());
            $entry->setName($etlEntry->getName());
            $entry->setIdx($etlEntry->getIdx());
            $entry->setKod($etlEntry->getKod());
            $entry->setNomdescr($etlEntry->getNomdescr());
            $entry->setRazdel($etlEntry->getRazdel());

            if (null !== ($parent = $em->getRepository(Entry::class)->findParentByIdx($etlEntry->getIdx()))) {
                $entry->setParent($parent);
            }

            $em->persist($entry);

//            if (($i % self::INSERT_BATCH_SIZE) === 0) {
                $em->flush();
                $em->clear();
//            }

            ++$i;
        }

//        $em->flush(); //Persist objects that did not make up an entire batch
//        $em->clear();

        return $i;
    }



}
