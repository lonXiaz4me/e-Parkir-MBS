<?php
// app/Services/BillplzService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $collectionId;

    public function __construct()
    {
        $this->baseUrl      = config('services.billplz.base_url');
        $this->apiKey       = config('services.billplz.api_key');
        $this->collectionId = config('services.billplz.collection_id');
    }

    public function createBill(array $data): array
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->asForm()
            ->post("{$this->baseUrl}/bills", [
                'collection_id' => $this->collectionId,
                'email'         => $data['email'],
                'name'          => $data['name'],
                'amount'        => $data['amount'], // in cents
                'description'   => $data['description'],
                'callback_url'  => $data['callback_url'],
                'redirect_url'  => $data['redirect_url'],
                'reference_1_label' => 'Invoice',
                'reference_1'   => $data['reference_1'],
            ]);

        if ($response->failed()) {
            Log::error('Billplz createBill failed', ['response' => $response->body()]);
            throw new \RuntimeException('Failed to create payment bill.');
        }

        return $response->json(); // includes 'id' and 'url'
    }

    public function verifySignature(array $payload, string $signature): bool
    {
        // Billplz signs a specific concatenation of sorted key=value pairs.
        // See: https://www.billplz.com/api#callback-x-signature
        $sourceString = collect($payload)
            ->except(['x_signature'])
            ->sortKeys()
            ->map(fn ($value, $key) => "{$key}{$value}")
            ->implode('|');

        $expected = hash_hmac('sha256', $sourceString, config('services.billplz.x_signature_key'));

        return hash_equals($expected, $signature);
    }
}