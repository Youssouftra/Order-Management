<?php

namespace App\Service\Impl;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct(
        string $cloudName,
        string $apiKey,
        string $apiSecret,
        private HttpClientInterface $httpClient
    ) {
        $this->cloudName = $cloudName;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function upload(UploadedFile $file, string $folder = 'brasil-burger'): ?string
    {
        $timestamp = time();
        $params = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];

        // Generate signature
        $signature = $this->generateSignature($params);

        // Prepare form data
        $formData = [
            'file' => fopen($file->getPathname(), 'r'),
            'folder' => $folder,
            'timestamp' => $timestamp,
            'api_key' => $this->apiKey,
            'signature' => $signature,
        ];

        try {
            $response = $this->httpClient->request('POST', 
                "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload",
                [
                    'body' => $formData,
                ]
            );

            $data = $response->toArray();
            return $data['secure_url'] ?? null;
        } catch (\Exception $e) {
            // Log error or handle it
            return null;
        }
    }

    public function delete(string $publicId): bool
    {
        $timestamp = time();
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
        ];

        $signature = $this->generateSignature($params);

        try {
            $response = $this->httpClient->request('POST',
                "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy",
                [
                    'body' => [
                        'public_id' => $publicId,
                        'timestamp' => $timestamp,
                        'api_key' => $this->apiKey,
                        'signature' => $signature,
                    ],
                ]
            );

            $data = $response->toArray();
            return ($data['result'] ?? '') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getPublicIdFromUrl(string $url): ?string
    {
        // Extract public_id from Cloudinary URL
        if (preg_match('/\/v\d+\/(.+)\.[a-z]+$/i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function generateSignature(array $params): string
    {
        ksort($params);
        $paramString = http_build_query($params);
        return sha1($paramString . $this->apiSecret);
    }
}
