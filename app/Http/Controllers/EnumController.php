<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\LivenessType;
use App\Http\Controllers\Controller;

class EnumController extends Controller
{
    public function documentType()
    {
        return $this->apiResponse->okay(DocumentType::getCollection());
    }

    public function livenessType()
    {
        return $this->apiResponse->okay(LivenessType::getCollection());
    }
}
