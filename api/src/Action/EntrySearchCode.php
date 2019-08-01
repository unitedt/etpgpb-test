<?php

namespace App\Action;

use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Entity\Entry;
use App\Repository\EntryRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EntrySearchCode
{
    /**
     * @var \App\Repository\EntryRepository
     */
    private $repository;

    public function __construct(EntryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get tree
     *
     * @Route(
     *     methods="GET",
     *     name="entry_search_code",
     *     path="/entries/search-code",
     *     defaults={"_api_item_operation_name"="search-code"}
     * )
     * @return JsonResponse
     * @throws ItemNotFoundException
     */
    public function __invoke(Request $request)
    {
        $term = $request->query->get('codeTerm');

        if (empty($term)) {
            throw new BadRequestHttpException('codeTerm must be specified!');
        }

        $entries = $this->repository->findByCode($term);
        $tree = [];

        foreach ($entries as $entry) {
            $tree[] = $this->repository->childrenHierarchy($entry, false, [], true);
        }

        return new JsonResponse($tree, 200);
    }

}

