<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Storage;
use App\Models\UploadModel;
use Illuminate\Bus\Batchable;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public $data;
    public $header;
    public $result;

    // Giving a timeout of 20 minutes to the Job to process the file
    public $timeout = 1200;

    public function __construct($data, $header)
    {
        $this->result = [];
        $this->data = $data;
        $this->header = $header;
    }

    public function handle(): void
    {
        foreach ($this->data as $item) {
            $this->result[] = array_combine($this->header, $item);
        }
        UploadModel::insert($this->result);
    }
}
