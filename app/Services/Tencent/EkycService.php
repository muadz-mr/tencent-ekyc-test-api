<?php

namespace App\Services\Tencent;

use App\Enums\LivenessType;
use App\Services\Tencent\BaseService;
use Exception;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Faceid\V20180301\FaceidClient;
use TencentCloud\Faceid\V20180301\Models\CompareFaceLivenessRequest;

class EkycService extends BaseService
{
  private FaceidClient $client;

  public static function make(): self
  {
    return new self();
  }

  public function __construct()
  {
    parent::__construct();
    $this->setProfile('faceid.intl.tencentcloudapi.com');
    $this->client = new FaceidClient($this->credential, config('services.tencent_cloud.region', 'ap-singapore'), $this->clientProfile);
  }

  public function verifySelfie(string $imageBase64, string $videoBase64, string $livenessType = LivenessType::Silent)
  {
    try {
      $requestObj = new CompareFaceLivenessRequest();
      $requestObj->setImageBase64($imageBase64);
      $requestObj->setVideoBase64($videoBase64);
      $requestObj->setLivenessType($livenessType);

      if ($livenessType === LivenessType::Action) {
        $requestObj->setValidateData('1,2');
      }

      $response = $this->client->CompareFaceLiveness($requestObj);

      return $response;
    } catch (TencentCloudSDKException $e) {
      throw new Exception($e->getMessage());
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
  }
}
