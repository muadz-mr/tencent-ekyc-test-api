<?php

namespace App\Services\Tencent;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

abstract class BaseService
{
  protected Credential $credential;

  protected ClientProfile $clientProfile;

  protected HttpProfile $httpProfile;

  abstract public static function make();

  public function __construct()
  {
    $this->setCredential(config('services.tencent_cloud.secret_id'), config('services.tencent_cloud.secret_key'));
  }

  public function setCredential(string $secretId, string $secretKey)
  {
    $this->credential = new Credential($secretId, $secretKey);
  }

  public function setHttpProfile(?string $endPoint, ?string $protocol = null, ?string $reqMethod = null, ?int $reqTimeout = null)
  {
    $this->httpProfile = new HttpProfile($protocol, $endPoint, $reqMethod, $reqTimeout);
  }

  public function setClientProfile($signMethod = 'TC3-HMAC-SHA256')
  {
    $this->clientProfile = new ClientProfile($signMethod, $this->httpProfile);
  }

  public function setProfile(?string $endPoint, ?string $protocol = null, ?string $reqMethod = null, ?int $reqTimeout = null, $signMethod = 'TC3-HMAC-SHA256')
  {
    $this->setHttpProfile($endPoint, $protocol, $reqMethod, $reqTimeout);
    $this->setClientProfile($signMethod);
  }
}
