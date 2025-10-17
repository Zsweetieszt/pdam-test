<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected $baseUrl;
    protected $timeout;
    protected $retryAttempts;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.service_url');
        $this->timeout = config('whatsapp.timeout');
        $this->retryAttempts = config('whatsapp.retry_attempts');
    }

    /**
     * Send WhatsApp message via wa-service
     *
     * @param string $phone
     * @param string $message
     * @param string|null $billId
     * @return array
     */
    public function sendMessage($phone, $message, $billId = null)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            Log::info('Sending WhatsApp message', [
                'phone' => $formattedPhone,
                'bill_id' => $billId,
                'service_url' => $this->baseUrl
            ]);

            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . config('whatsapp.endpoints.send_message'), [
                    'number' => $formattedPhone,
                    'message' => $message
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $formattedPhone,
                    'bill_id' => $billId,
                    'message_id' => $data['msg']['id']['_serialized'] ?? null
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['msg']['id']['_serialized'] ?? null,
                    'response' => $data,
                    'phone' => $formattedPhone
                ];
            }

            Log::error('WhatsApp service HTTP error', [
                'phone' => $formattedPhone,
                'bill_id' => $billId,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
                'response' => $response->json(),
                'phone' => $formattedPhone
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp send failed', [
                'phone' => $phone,
                'bill_id' => $billId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'phone' => $phone
            ];
        }
    }

    /**
     * Get QR Code for WhatsApp Web authentication
     *
     * @return array
     */
    public function getQRCode()
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . config('whatsapp.endpoints.get_qr'));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get QR code',
                'status' => $response->status()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if WhatsApp service is healthy
     *
     * @return bool
     */
    public function checkHealth()
    {
        try {
            $response = Http::timeout(5)
                ->get($this->baseUrl . config('whatsapp.endpoints.health_check'));

            return $response->successful();

        } catch (Exception $e) {
            Log::warning('WhatsApp service health check failed', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp (Indonesian format)
     *
     * @param string $phone
     * @return string
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Apply Indonesian phone number formatting
        if (substr($phone, 0, 1) === '0') {
            // Replace leading 0 with 62
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            // Add 62 prefix if not present
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Process template variables in message
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public function processTemplate($template, $variables = [])
    {
        $processed = $template;
        
        foreach ($variables as $key => $value) {
            $processed = str_replace('{{' . $key . '}}', $value, $processed);
        }
        
        return $processed;
    }
}
