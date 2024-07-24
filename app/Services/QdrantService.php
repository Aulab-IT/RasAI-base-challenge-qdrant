<?php

namespace App\Services;

use Qdrant\Qdrant;
use GuzzleHttp\Client;
use Qdrant\Http\Transport;
use Illuminate\Support\Str;
use Qdrant\Models\PointStruct;
use Qdrant\Models\PointsStruct;
use Qdrant\Models\VectorStruct;
use Qdrant\Models\Request\SearchRequest;

class QdrantService
{
    private $client;

    public function __construct()
    {
        $config = new \Qdrant\Config(env("QDRANT_HOST"));
        $config->setApiKey(env("QDRANT_API_KEY"));
        $client = new \GuzzleHttp\Client();

        $this->client = new Qdrant(new Transport($client, $config));
    }

    public function search($vector)
    {        
        $searchRequest = (
                new SearchRequest(
                    new VectorStruct($vector, 'document')
                )
            )
            ->setLimit(10)
            ->setParams([
                'hnsw_ef' => 128,
                'exact' => false,
            ])
            ->setWithPayload(true);
    
        $res = $this->client->collections('documents')->points()->search($searchRequest);

        return $res['result'];
    }

    public function insert($vector , $content)
    {
        $points = new PointsStruct();
        $points->addPoint(
            new PointStruct(
                Str::uuid(), // (int) time(),
                new VectorStruct($vector, 'document'),
                [
                    'content' => $content
                ]
            )
        );

        $this->client->collections('documents')->points()->upsert($points);
    }

}