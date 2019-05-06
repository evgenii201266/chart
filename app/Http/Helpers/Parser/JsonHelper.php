<?php

declare(strict_types=1);

namespace App\Http\Helpers\Parser;

trait JsonHelper
{
    private $username;
    private $password;
    private $url;

    public function getCatalogById($catalogId): ?string
    {
        $this->setAutorisationDate();
        $this->setUrlCatalog($catalogId);
        $response = $this->curlRequest();

        return $response;
    }

    private function curlRequest(): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, ($this->username) . ":" . ($this->password));
        $response=curl_exec($ch);
        curl_close ($ch);
        return $response;
    }

    private function setAutorisationDate(): void
    {
        $this->setUserNane();
        $this->setPassword();
    }

    private function setUserNane(): void
    {
        $this->username = 'x41';
    }

    private function setPassword(): void
    {
        $this->password = 'x4passAsDefault666';
    }

    private function setUrlCatalog(string $catalogId): void
    {
        $this->url = "http://satellite.by/~api/json/catalog/getChilds/id/$catalogId";
    }


}
