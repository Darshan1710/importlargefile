<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\ProcessImportJob;
use Exception;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));
        if (!$receiver->isUploaded()) {
            // file not uploaded
        }

        $fileReceived = $receiver->receive(); // receive file
        if ($fileReceived->isFinished()) { // file uploading is complete / all chunks are uploaded
            try {
                $file = $fileReceived->getFile(); // get file
                $extension = $file->getClientOriginalExtension();
                $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
                $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name
                $path = Storage::disk(config('filesystems.default'))->put('public/videos', $file);
                $csv    = file(storage_path('app/'.$path));
                $headers = explode(',', array_shift($csv));
                $headers = array_map(function ($name) {
                    return trim(str_replace(' ', '_', $name));
                }, $headers);

                $chunks = array_chunk($csv, 1000);
                $batch  = Bus::batch([])->dispatch();
                foreach ($chunks as $chunk) {
                    $data = array_map('str_getcsv', $chunk);
                    $batch->add(new ProcessImportJob($data, $headers));
                }

                unlink($file->getPathname());
                return [
                    'path' => asset('storage/' . $path),
                    'filename' => $fileName
                ];
            } catch(Exception $e) {
                Log::error('An error occurred: ' . $e->getMessage());
            }
        }

        // otherwise return percentage informatoin
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
}
