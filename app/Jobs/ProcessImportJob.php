<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UploadModel;
use Illuminate\Bus\Batchable;
use Illuminate\Support\LazyCollection;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public $path;

    public $headers;

    public $handle;

    // Giving a timeout of 20 minutes to the Job to process the file
    public $timeout = 1200;

    public function __construct($path,$handle)
    {
        $this->path = $path;
        $this->handle = $handle;
        $this->headers = [];
    }

    public function handle(): void
    {
        LazyCollection::make(function () {
            $handle = fopen(storage_path('app/'.$this->path), 'r');
            while (($data = fgetcsv($handle, 4096)) !== false) {
                if(empty($this->headers)){
                    $this->headers = array_map(function ($name) {
                        return trim(str_replace(' ', '_', $name));
                    }, $data);
                }
                yield $data;
            }
            fclose($handle);
          })
          ->skip(1)
          ->chunk(1000)
          ->each(function (LazyCollection $chunk) {
            $records = $chunk->map(function ($row) {
                return array_combine($this->headers, $row);
            })->toArray();
            UploadModel::insert($records);
          });    
    }
}
