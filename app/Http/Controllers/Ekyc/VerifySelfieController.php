<?php

namespace App\Http\Controllers\Ekyc;

use App\Http\Controllers\Controller;
use App\Services\Tencent\EkycService;
use Illuminate\Http\Request;
use TencentCloud\Faceid\V20180301\Models\CompareFaceLivenessResponse;

class VerifySelfieController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg|max:2048',
            'video' => 'required|file|mimetypes:video/avi,video/mp4,video/x-m4v,video/x-matroska,video/x-flv|max:6144',
        ]);

        $response = EkycService::make()->verifySelfie(
            base64_encode(file_get_contents($validated['image']->path())),
            base64_encode(file_get_contents($validated['video']->path())),
        );

        return $this->apiResponse->okay($this->transformResponse($response));
    }

    private function transformResponse(CompareFaceLivenessResponse $response)
    {
        return [
            'request_id' => $response->getRequestId(),
            'result' => $response->getResult(),
            'description' => $response->getDescription(),
            'similarity' => $response->getSim(),
            'best_frame_base64' => $response->getBestFrameBase64(), // data:image/jpeg;base64,xxx
        ];
    }
}
