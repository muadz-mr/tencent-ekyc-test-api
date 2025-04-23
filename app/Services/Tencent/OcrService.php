<?php

namespace App\Services\Tencent;

use App\Enums\DocumentType;
use Exception;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesDrivingLicenseOCRRequest;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesSssIDOCRRequest;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesTinIDOCRRequest;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesUMIDOCRRequest;
use TencentCloud\Ocr\V20181119\Models\RecognizePhilippinesVoteIDOCRRequest;
use TencentCloud\Ocr\V20181119\OcrClient;

class OcrService extends BaseService
{
  private OcrClient $client;

  public static function make(): self
  {
    return new self();
  }

  public function __construct()
  {
    parent::__construct();
    $this->setProfile('ocr.intl.tencentcloudapi.com');
    $this->client = new OcrClient($this->credential, config('services.tencent_cloud.region', 'ap-singapore'), $this->clientProfile);
  }

  public function analyseDocument(string $documentType, string $imageBase64)
  {
    try {
      if (!in_array($documentType, DocumentType::getValues())) {
        throw new Exception(__('Invalid document type.'));
      }

      switch ($documentType) {
        case DocumentType::DrivingLicense:
          $requestObj = new RecognizePhilippinesDrivingLicenseOCRRequest();
          break;
        case DocumentType::SssID:
          $requestObj = new RecognizePhilippinesSssIDOCRRequest();
          break;
        case DocumentType::TinID:
          $requestObj = new RecognizePhilippinesTinIDOCRRequest();
          break;
        case DocumentType::VoteID:
          $requestObj = new RecognizePhilippinesVoteIDOCRRequest();
          break;
        default:
          $requestObj = new RecognizePhilippinesUMIDOCRRequest();
          break;
      }

      $requestObj->setImageBase64($imageBase64);
      $requestObj->setReturnHeadImage(true);

      switch ($documentType) {
        case DocumentType::DrivingLicense:
          $response = $this->client->RecognizePhilippinesDrivingLicenseOCR($requestObj);
          break;
        case DocumentType::SssID:
          $response = $this->client->RecognizePhilippinesSssIDOCR($requestObj);
          break;
        case DocumentType::TinID:
          $response = $this->client->RecognizePhilippinesTinIDOCR($requestObj);
          break;
        case DocumentType::VoteID:
          $response = $this->client->RecognizePhilippinesVoteIDOCR($requestObj);
          break;
        default:
          $response = $this->client->RecognizePhilippinesUMIDOCR($requestObj);
          break;
      }

      return $response;
    } catch (TencentCloudSDKException $e) {
      throw new Exception($e->getMessage());
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }
}
