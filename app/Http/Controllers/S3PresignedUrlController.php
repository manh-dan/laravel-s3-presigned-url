<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class S3PresignedUrlController extends Controller
{
    public function index()
    {
        $user = User::inRandomOrder()->first();
        return view('welcome', compact('user'));
    }

    public function getUrlUpload(Request $request)
    {
        if ($request->name) {
            $filename = \Str::random(10) . '_' . $request->name;
            return response()->json([
                'error' => false,
                'url'   => $this->get_amazon_url($filename),
                'additionalData' => [
                    // Uploading many files and need a unique name? UUID it!
                    //'fileName' => Uuid::uuid4()->toString()
                ],
                'code' => 200,
            ], 200);
        }
        return response()->json([
            'error'   => true,
            'message' => ['name' => 'name'],
            'code'    => 400,
        ], 400);
    }

    private function get_amazon_url($name)
    {
        $client = Storage::disk('s3')->getClient();
        $expiry = "+90 minutes";
        $command = $client->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key'    => $name,
        ]);
        $request = $client->createPresignedRequest($command, '+20 minutes');
        return (string)$request->getUri();
    }
}
