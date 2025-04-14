<?php

// Sabit mesaj türleri için kolay kullanım örnekleri

use NotifyCord\NotifyCord\Facades\DiscordShortcuts;
use NotifyCord\NotifyCord\Facades\NotifyCord;

class ShortcutsExamples 
{
    /**
     * Başarı bildirimi göndermek için.
     */
    public function sendSuccessExample()
    {
        // Başarı bildirimi - yeşil renkli
        DiscordShortcuts::success(
            'İşlem Başarılı', // Başlık
            'Veritabanı yedeklemesi başarıyla tamamlandı.', // Mesaj
            [
                'Sunucu' => 'db-server-01',
                'Boyut' => '2.3 GB',
                'Süre' => '00:05:32'
            ]
        );
        
        return "Başarı bildirimi gönderildi!";
    }
    
    /**
     * Bilgi bildirimi göndermek için.
     */
    public function sendInfoExample()
    {
        // Bilgi bildirimi - mavi renkli
        DiscordShortcuts::info(
            'Sistem Bilgisi',
            'Haftalık bakım çalışmaları başlatılıyor.',
            [
                'Başlangıç' => date('Y-m-d H:i:s'),
                'Tahmini Süre' => '30 dakika',
                'Etkilenen Servisler' => 'API, Web, DB'
            ]
        );
        
        return "Bilgi bildirimi gönderildi!";
    }
    
    /**
     * Uyarı bildirimi göndermek için.
     */
    public function sendWarningExample()
    {
        // Uyarı bildirimi - sarı/turuncu renkli
        DiscordShortcuts::warning(
            'Sistem Uyarısı',
            'Disk alanı kritik seviyeye yaklaşıyor.',
            [
                'Sunucu' => 'web-server-02',
                'Kullanılan Alan' => '%85',
                'Kalan Alan' => '15.2 GB'
            ]
        );
        
        return "Uyarı bildirimi gönderildi!";
    }
    
    /**
     * Hata bildirimi göndermek için.
     */
    public function sendErrorExample()
    {
        // Hata bildirimi - kırmızı renkli
        DiscordShortcuts::error(
            'Sistem Hatası',
            'Veritabanına bağlantı sağlanamadı.',
            [
                'Sunucu' => 'db-server-03',
                'Hata Kodu' => 'CONN_REFUSED',
                'Zaman' => date('Y-m-d H:i:s')
            ]
        );
        
        return "Hata bildirimi gönderildi!";
    }
    
    /**
     * İstisna bildirimi göndermek için.
     */
    public function sendExceptionExample()
    {
        try {
            // Bir hata oluşturalım
            throw new \Exception('Beklenmeyen bir hata oluştu.');
        } catch (\Exception $e) {
            // İstisna bildirimi - stack trace içerir
            DiscordShortcuts::exception($e);
        }
        
        return "İstisna bildirimi gönderildi!";
    }
    
    /**
     * Sunucu durumu bildirimi göndermek için.
     */
    public function sendServerStatusExample()
    {
        // Örnek sistem metrikleri
        $metrics = [
            'CPU' => '%23',
            'Bellek' => '%67',
            'Disk' => '%54',
            'Çalışma Süresi' => '14 gün, 5 saat',
            'Aktif Kullanıcı' => '842',
            'İşlem Sayısı' => '156'
        ];
        
        // Sunucu durumu bildirimi
        DiscordShortcuts::serverStatus($metrics);
        
        return "Sunucu durumu bildirimi gönderildi!";
    }
    
    /**
     * Dağıtım bildirimi göndermek için.
     */
    public function sendDeploymentExample()
    {
        // Dağıtım bildirimi
        DiscordShortcuts::deployment(
            'production', // Ortam
            'v1.2.5',    // Versiyon
            'Mehmet Yılmaz', // Dağıtımı yapan kişi
            [
                'Değişiklikler' => '3 yeni özellik, 5 hata düzeltmesi',
                'Deploy ID' => 'DEP-12345'
            ]
        );
        
        return "Dağıtım bildirimi gönderildi!";
    }
    
    /**
     * Yeni kullanıcı bildirimi göndermek için.
     */
    public function sendNewUserExample()
    {
        // Yeni kullanıcı bildirimi
        DiscordShortcuts::newUser(
            'ahmetyilmaz', // Kullanıcı adı
            'ahmet@example.com', // E-posta
            [
                'Referans' => 'Google',
                'Ülke' => 'Türkiye',
                'Plan' => 'Premium'
            ]
        );
        
        return "Yeni kullanıcı bildirimi gönderildi!";
    }
    
    /**
     * Sipariş bildirimi göndermek için.
     */
    public function sendOrderExample()
    {
        // Sipariş bildirimi
        DiscordShortcuts::order(
            'ORD-12345', // Sipariş numarası
            '259.99 TL', // Tutar
            'Ayşe Kaya',  // Müşteri
            [
                'Ürünler' => '3 ürün',
                'Ödeme Yöntemi' => 'Kredi Kartı',
                'Durum' => 'Onaylandı'
            ]
        );
        
        return "Sipariş bildirimi gönderildi!";
    }
    
    /**
     * Özel bir webhook URL'sine mesaj göndermek için.
     */
    public function sendToCustomWebhook()
    {
        // Özel webhook URL'si
        $webhookUrl = 'https://discord.com/api/webhooks/your-custom-webhook-id/your-custom-webhook-token';
        
        // Özel webhook'a bilgi mesajı gönderme
        DiscordShortcuts::info(
            'Özel Webhook',
            'Bu mesaj özel bir webhook URL\'sine gönderildi.',
            [
                'Test' => 'Başarılı',
                'Zaman' => date('H:i:s')
            ],
            $webhookUrl // Webhook URL'si son parametre
        );
        
        return "Özel webhook'a mesaj gönderildi!";
    }
    
    /**
     * İstenirse geleneksel kullanımla karmaşık senaryoları da destekler.
     */
    public function sendAdvancedExample()
    {
        // Gelişmiş özelleştirmeler gerektiren senaryolar için normal kullanım
        NotifyCord::sendMessage(
            'Bu ileri düzey bir bildirimdir',
            null,
            function($message) {
                $message->embed(function($embed) {
                    $embed->title('Gelişmiş Kullanım')
                         ->description('Burada özel senaryolar için daha esnek bir kullanım gösteriliyor.')
                         ->color('#1abc9c')
                         ->timestamp()
                         ->thumbnail('https://example.com/thumbnail.png')
                         ->image('https://example.com/image.png')
                         ->author('Yönetici', 'https://example.com', 'https://example.com/author.png')
                         ->field('Özellik 1', 'Değer 1', true)
                         ->field('Özellik 2', 'Değer 2', true)
                         ->field('Açıklama', 'Bu alanda daha uzun bir açıklama bulunabilir.', false)
                         ->footer('© 2025 NotifyCord', 'https://example.com/logo.png');
                })
                ->button('Birincil Buton', 'primary', 'action_1')
                ->button('İkincil Buton', 'secondary', 'action_2')
                ->addActionRow()
                ->button('Başarı Butonu', 'success', 'action_3')
                ->button('Tehlike Butonu', 'danger', 'action_4')
                ->button('Web Sitesi', 'link', null, 'https://example.com');
            }
        );
        
        return "Gelişmiş bildirim gönderildi!";
    }
}