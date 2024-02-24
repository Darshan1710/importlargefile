<?php

namespace App\Http\Controllers;

use App\Models\UploadModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use League\Csv\Reader;
use App\Jobs\ProcessImportJob;
use Illuminate\Support\Facades\Bus;

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
            $file = $fileReceived->getFile(); // get file
            $extension = $file->getClientOriginalExtension();
            $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName()); //file name without extenstion
            $fileName .= '_' . md5(time()) . '.' . $extension; // a unique file name
            $path = Storage::disk(config('filesystems.default'))->put('public/videos', $file);
            ProcessImportJob::dispatch($path);
            unlink($file->getPathname());
            return [
                'path' => asset('storage/' . $path),
                'filename' => $fileName
            ];
        }

        // otherwise return percentage informatoin
        $handler = $fileReceived->handler();
        return [
            'done' => $handler->getPercentageDone(),
            'status' => true
        ];
    }
}
