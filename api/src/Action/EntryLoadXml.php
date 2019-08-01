<?php

namespace App\Action;

use App\Repository\EtlEntryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EntryLoadXml
{
    /**
     * @var \App\Repository\EtlEntryRepository
     */
    private $repository;

    public function __construct(EtlEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Load from XML request and bulk insert entries
     *
     * @Route(
     *     methods="POST",
     *     name="entry_load_xml",
     *     path="/entries/load-xml",
     *     defaults={"_api_item_operation_name"="load_xml"}
     * )
     */
    public function __invoke(Request $request)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');

        if (!$file) {
            throw new BadRequestHttpException('No file present in the request');
        }

        if (!is_uploaded_file($file->getRealPath())) {
            throw new BadRequestHttpException('This file is not uploaded through HTTP');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file->getRealPath());
        if (false === $xml) {
            $errtext = 'Cannot load xml source: ';

            foreach(libxml_get_errors() as $error) {
                $errtext .= '- ' . $error->message;
            }

            throw new BadRequestHttpException($errtext);
        }
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        return new JsonResponse($this->repository->bulkInsertFromXml($xml), 200);
    }

}

