<?php
/**
 * Created by PhpStorm.
 * User: chrisknight
 * Date: 08/10/2015
 * Time: 10:01
 */

namespace DataSift;


class Ingestion extends Base
{
    protected $ingestionClient = null;

    public function __construct(
        Client $client,
        IngestionClient $ingestionClient
    ) {
        $this->setIngestionClient($ingestionClient);
        parent::__construct($client);
    }

    protected function setIngestionClient(IngestionClient $ingestionClient)
    {
        $this->ingestionClient = $ingestionClient;
    }

    protected function getIngestionClient()
    {
        return $this->ingestionClient;
    }
}