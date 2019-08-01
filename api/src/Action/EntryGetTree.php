<?php

namespace App\Action;

use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Repository\EntryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EntryGetTree
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
     *     name="entry_get_tree",
     *     path="/entries/get-tree",
     *     defaults={"_api_item_operation_name"="get_tree"}
     * )
     * @return JsonResponse
     * @throws ItemNotFoundException
     */
    public function __invoke(Request $request)
    {
        return new JsonResponse($this->repository->childrenHierarchy(), 200);
    }

}

