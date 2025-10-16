<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $templates = [
            [
                'template_name' => 'Tagihan Bulanan PDAM',
                'template_type' => 'bill_reminder',
                'subject' => 'Tagihan Air PDAM',
                'message_content' => 'Yth. {{customer_name}}, tagihan air PDAM bulan {{period}} sebesar {{amount}} jatuh tempo {{due_date}}. Pemakaian: {{usage_m3}} m³. No. Pel: {{customer_number}}. Info: PDAM Kota',
                'variables' => json_encode([
                    'customer_name',
                    'customer_number', 
                    'bill_number',
                    'amount',
                    'due_date',
                    'period',
                    'usage_m3'
                ]),
                'is_active' => true
            ],
            [
                'template_name' => 'Peringatan Tunggakan',
                'template_type' => 'overdue_notice', 
                'subject' => 'Peringatan Tunggakan PDAM',
                'message_content' => 'Yth. {{customer_name}}, tagihan air PDAM No. {{bill_number}} sebesar {{amount}} telah jatuh tempo pada {{due_date}}. Mohon segera melakukan pembayaran untuk menghindari pemutusan layanan. Info: PDAM Kota',
                'variables' => json_encode([
                    'customer_name',
                    'customer_number',
                    'bill_number', 
                    'amount',
                    'due_date',
                    'period'
                ]),
                'is_active' => true
            ],
            [
                'template_name' => 'Konfirmasi Pembayaran',
                'template_type' => 'payment_confirmation',
                'subject' => 'Konfirmasi Pembayaran PDAM',
                'message_content' => 'Terima kasih {{customer_name}}, pembayaran tagihan {{bill_number}} sebesar {{amount}} telah kami terima pada {{payment_date}}. Ref: {{reference_number}}. Info: PDAM Kota',
                'variables' => json_encode([
                    'customer_name',
                    'bill_number',
                    'amount', 
                    'payment_date',
                    'reference_number'
                ]),
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            NotificationTemplate::updateOrCreate(
                ['template_name' => $template['template_name']],
                $template
            );
        }
    }
}