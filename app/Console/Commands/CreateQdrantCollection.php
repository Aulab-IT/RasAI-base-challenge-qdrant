<?php

namespace App\Console\Commands;

use Qdrant\Config;
use Qdrant\Qdrant;
use Qdrant\Http\Builder;
use Illuminate\Console\Command;
use Qdrant\Models\Request\VectorParams;
use Qdrant\Models\Request\CreateCollection;

class CreateQdrantCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qdrant:create-collection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command creates a collection in the Qdrant vector database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $config = new Config(env('QDRANT_HOST'));
        $config->setApiKey(env('QDRANT_API_KEY'));

        $transport = (new Builder())->build($config);
        $client = new Qdrant($transport);

        $createCollection = new CreateCollection();
        $createCollection->addVector(new VectorParams(1536, VectorParams::DISTANCE_COSINE), 'document');
        $response = $client->collections('documents')->create($createCollection);

        if ($response->__toArray()['status'] === 'ok') {
            $this->info('Collection created successfully');
        } else {
            $this->error('Failed to create collection');
        }
    }
}
