<?php

namespace App\Http\Controllers\Ekyc;

use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Services\Tencent\OcrService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesDrivingLicenseOCRResponse;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesSssIDOCRResponse;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesTinIDOCRResponse;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesUMIDOCRResponse;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesVoteIDOCRResponse;

class AnalyseDocumentController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'document_type' => ['required', Rule::in(DocumentType::getValues())],
            'image' => 'required|image|mimes:jpeg,jpg|max:2048',
        ]);

        $response = OcrService::make()->analyseDocument($request['document_type'], base64_encode(file_get_contents($request['image']->path())));

        return $this->apiResponse->okay($this->transformResponse($request['document_type'], $response));
    }

    /**
     * @param RecognizePhilippinesDrivingLicenseOCRResponse|RecognizePhilippinesSssIDOCRResponse|RecognizePhilippinesTinIDOCRResponse|RecognizePhilippinesUMIDOCRResponse|RecognizePhilippinesVoteIDOCRResponse $response
     */
    private function transformResponse(
        string $documentType,
        $response
    ) {
        $result = [];

        // Storage::put('head-portrait/' . $response->getRequestId() . '.jpg', base64_decode($response->getHeadPortrait()->getValue()));

        $result['head_portrait'] = $response->getHeadPortrait()->getValue();
        $result['date_of_birth'] = $response->getBirthday()->getValue();

        switch ($documentType) {
            case DocumentType::DrivingLicense:
                return array_merge($result, [
                    'name' => $response->getName()->getValue(),
                    'last_name' => $response->getLastName()->getValue(),
                    'first_name' => $response->getFirstName()->getValue(),
                    'middle_name' => $response->getMiddleName()->getValue(),
                    'nationality' => $response->getNationality()->getValue(),
                    'sex' => $response->getSex()->getValue(),
                    'address' => $response->getAddress()->getValue(),
                    'license_no' => $response->getLicenseNo()->getValue(),
                    'expires_date' => $response->getExpiresDate()->getValue(),
                    'agency_code' => $response->getAgencyCode()->getValue(),
                ]);
                break;
            case DocumentType::SssID:
                return array_merge($result, [
                    'full_name' => $response->getFullName()->getValue(),
                    'license_number' => $response->getLicenseNumber()->getValue(),
                ]);
                break;
            case DocumentType::TinID:
                return array_merge($result, [
                    'full_name' => $response->getFullName()->getValue(),
                    'license_number' => $response->getLicenseNumber()->getValue(),
                    'address' => $response->getAddress()->getValue(),
                    'issue_date' => $response->getIssueDate()->getValue(),
                ]);
                break;
            case DocumentType::VoteID:
                return array_merge($result, [
                    'vin' => $response->getVIN()->getValue(),
                    'last_name' => $response->getLastName()->getValue(),
                    'first_name' => $response->getFirstName()->getValue(),
                    'civil_status' => $response->getCivilStatus()->getValue(),
                    'citizenship' => $response->getCitizenship()->getValue(),
                    'address' => $response->getAddress()->getValue(),
                    'precinct_no' => $response->getPrecinctNo()->getValue(),
                ]);
                break;
            default:
                return array_merge($result, [
                    'surname' => $response->getSurname()->getValue(),
                    'middle_name' => $response->getMiddleName()->getValue(),
                    'given_name' => $response->getGivenName()->getValue(),
                    'address' => $response->getAddress()->getValue(),
                    'crn' => $response->getCRN()->getValue(),
                    'sex' => $response->getSex()->getValue(),
                ]);
                break;
        }

        return $result;
    }
}
