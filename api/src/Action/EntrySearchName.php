<?php

namespace App\Action;

use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Repository\EntryRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EntrySearchName
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
     *     name="entry_search_name",
     *     path="/entries/search-name",
     *     defaults={"_api_item_operation_name"="search-name"}
     * )
     * @return JsonResponse
     * @throws ItemNotFoundException
     */
    public function __invoke(Request $request)
    {
        $term = $request->query->get('nameTerm');

        if (empty($term)) {
            throw new BadRequestHttpException('nameTerm must be specified!');
        }

        $entries = $this->repository->findByName($term);
        $tree = [];

        foreach ($entries as $entry) {
            $tree[] = $this->repository->childrenHierarchy($entry, false, [], true);
        }

        return new JsonResponse($tree, 200);
    }

}

