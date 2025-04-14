<?php

namespace Examples;

use NotifyCord\NotifyCord\Facades\NotifyCord;
use NotifyCord\NotifyCord\Helpers\DiscordEmbedHelper;

class SimpleUsageExamples
{
    /**
     * En basit kullanım şekli.
     */
    public function basicExample()
    {
        // Basit metin mesajı
        NotifyCord::sendMessage('Merhaba, bu bir test mesajıdır!');
    }
    
    /**
     * Webhook belirtme.
     */
    public function webhookExample()
    {
        // Özel webhook URL belirterek
        $webhookUrl = 'https://discord.com/api/webhooks/your-webhook-id/your-webhook-token';
        NotifyCord::sendMessage('Özel webhook ile gönderilmiş mesaj', $webhookUrl);
    }
    
    /**
     * Embed içerikli mesaj.
     */
    public function embedExample()
    {
        // Embed ile mesaj gönderme
        NotifyCord::sendMessage('', null, function($message) {
            $message->embed(function($embed) {
                $embed->title('Embed Başlık')
                     ->description('Bu bir açıklamadır.')
                     ->color('#ff0000')
                     ->timestamp()
                     ->field('Alan 1', 'Değer 1', true)
                     ->field('Alan 2', 'Değer 2', true);
            });
        });
    }
    
    /**
     * Butonlu mesaj.
     */
    public function buttonExample()
    {
        // Butonlu mesaj gönderme
        NotifyCord::sendMessage('Butonlu mesaj örneği', null, function($message) {
            $message->button('Tıkla', 'primary', 'button_id_1')
                    ->button('İptal', 'secondary', 'button_id_2')
                    ->button('Web Sitesi', 'link', null, 'https://example.com');
        });
    }
    
    /**
     * Hazır stil kullanımı - Başarı mesajı.
     */
    public function successExample()
    {
        // Başarı mesajı gönderme (yeşil)
        NotifyCord::success(
            'İşlem Tamamlandı', 
            'Veriler başarıyla işlendi.',
            ['Süre' => '5 saniye', 'Kayıtlar' => '150']
        );
    }
    
    /**
     * Hazır stil kullanımı - Hata mesajı.
     */
    public function errorExample()
    {
        // Hata mesajı gönderme (kırmızı)
        NotifyCord::error(
            'Hata Oluştu', 
            'İşlem sırasında bir sorun oluştu.',
            ['Hata Kodu' => 'ERR-404', 'Zaman' => now()->format('H:i:s')]
        );
    }
    
    /**
     * Hazır stil kullanımı - Uyarı mesajı.
     */
    public function warningExample()
    {
        // Uyarı mesajı gönderme (turuncu)
        NotifyCord::warning(
            'Dikkat', 
            'Bu işlemi yapmak istediğinizden emin misiniz?',
            ['İşlem' => 'Veri Silme', 'Etki' => 'Geri alınamaz']
        );
    }
    
    /**
     * Hazır stil kullanımı - Bilgi mesajı.
     */
    public function infoExample()
    {
        // Bilgi mesajı gönderme (mavi)
        NotifyCord::info(
            'Bilgilendirme', 
            'Sistemde bakım yapılacaktır.',
            ['Tarih' => '15.04.2025', 'Süre' => '2 saat']
        );
    }
    
    /**
     * Hazır stil kullanımı - Sunucu bildirimi.
     */
    public function serverAlertExample()
    {
        // Sunucu bildirimi gönderme (mor)
        NotifyCord::serverAlert(
            'Sunucu Durumu', 
            'Yüksek CPU kullanımı tespit edildi.',
            [
                'CPU' => '%92',
                'RAM' => '%76',
                'Disk' => '%45',
                'Yük' => '3.45'
            ]
        );
    }
    
    /**
     * Hazır stil kullanımı - Kullanıcı aktivitesi.
     */
    public function userActivityExample()
    {
        // Kullanıcı aktivitesi bildirimi (turkuaz)
        NotifyCord::userActivity(
            'Yeni Kullanıcı',
            'Platformunuza yeni bir kullanıcı kaydoldu.',
            'Ahmet Yılmaz',
            'https://example.com/avatars/ahmet.png',
            [
                'Email' => 'ahmet@example.com',
                'Plan' => 'Premium',
                'Referans' => 'Google'
            ]
        );
    }
    
    /**
     * Gelişmiş embed yardımcıları - Resim.
     */
    public function imageExample()
    {
        // Sadece resim içeren embed
        NotifyCord::sendMessage('', null, function($message) {
            $message->embed(
                DiscordEmbedHelper::imageEmbed(
                    'Güzel Manzara', 
                    'https://example.com/images/manzara.jpg',
                    '#ff9900'
                )
            );
        });
    }
    
    /**
     * Gelişmiş embed yardımcıları - Kod parçası.
     */
    public function codeExample()
    {
        // Kod parçası içeren embed
        NotifyCord::sendMessage('', null, function($message) {
            $message->embed(
                DiscordEmbedHelper::codeEmbed(
                    'PHP Kod Örneği',
                    'php',
                    '$user = User::find(1);\necho $user->name;',
                    '#8e44ad'
                )
            );
        });
    }
    
    /**
     * Gelişmiş embed yardımcıları - İlerleme çubuğu.
     */
    public function progressExample()
    {
        // İlerleme çubuğu içeren embed
        NotifyCord::sendMessage('', null, function($message) {
            $message->embed(
                DiscordEmbedHelper::progressEmbed(
                    'İndirme Durumu',
                    75,
                    100,
                    'İndiriliyor...',
                    '#2980b9'
                )
            );
        });
    }
    
    /**
     * Gelişmiş embed yardımcıları - Karşılaştırma.
     */
    public function comparisonExample()
    {
        // Karşılaştırma içeren embed
        NotifyCord::sendMessage('', null, function($message) {
            $message->embed(
                DiscordEmbedHelper::comparisonEmbed(
                    'Ayarlar Değiştirildi',
                    [
                        'Plan' => ['before' => 'Ücretsiz', 'after' => 'Premium'],
                        'Depolama' => ['before' => '5 GB', 'after' => '50 GB'],
                        'Kullanıcı' => ['before' => '3', 'after' => 'Sınırsız']
                    ],
                    '#16a085'
                )
            );
        });
    }
}