<?php

namespace App\Services;

use Aws\S3\S3Client;

class StorageService
{
    private S3Client $client;

    private string $bucket;

    public function __construct()
    {
        $this->bucket = (string) config('filesystems.disks.r2.bucket');

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.r2.region', 'auto'),
            'endpoint' => config('filesystems.disks.r2.endpoint'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => config('filesystems.disks.r2.key'),
                'secret' => config('filesystems.disks.r2.secret'),
            ],
        ]);
    }

    public function presignedUploadUrl(string $key, string $contentType): string
    {
        $cmd = $this->client->getCommand('PutObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        return (string) $this->client
            ->createPresignedRequest($cmd, '+30 minutes')
            ->getUri();
    }

    public function presignedDownloadUrl(string $key): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return (string) $this->client
            ->createPresignedRequest($cmd, '+10 minutes')
            ->getUri();
    }

    public function presignedThumbnailUrl(string $key): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        return (string) $this->client
            ->createPresignedRequest($cmd, '+1 hour')
            ->getUri();
    }

    public function delete(string $key): void
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
    }
}
