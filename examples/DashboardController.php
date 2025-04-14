<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NotifyCord\NotifyCord\Facades\NotifyCord;

class DashboardController extends Controller
{
    /**
     * Ana dashboard gösterimi.
     */
    public function index()
    {
        return view('dashboard');
    }
    
    /**
     * En basit kullanım örneği.
     * Herhangi bir model gerektirmez, doğrudan webhook'a mesaj gönderir.
     */
    public function testSimpleMessage()
    {
        // Basit bir metin mesajı gönderme
        $success = NotifyCord::sendMessage('Bu basit bir test mesajıdır!');
        
        if ($success) {
            return "Mesaj başarıyla gönderildi!";
        } else {
            return "Mesaj gönderilirken bir hata oluştu. Log dosyasını kontrol edin.";
        }
    }
    
    /**
     * Özel webhook URL'sine mesaj gönderme.
     */
    public function testCustomWebhook()
    {
        $webhookUrl = 'https://discord.com/api/webhooks/your-webhook-id/your-webhook-token';
        
        $success = NotifyCord::sendMessage('Bu özel bir webhook mesajıdır!', $webhookUrl);
        
        return $success ? "Özel webhook mesajı gönderildi!" : "Hata oluştu!";
    }
    
    /**
     * Zengin içerikli mesaj gönderme örneği.
     */
    public function testRichMessage()
    {
        // Kullanıcı ve sipariş bilgisi (örnek)
        $user = auth()->user();
        $order = [
            'id' => 12345,
            'total' => '199.99',
            'currency' => 'TL',
            'items' => 3,
            'status' => 'Tamamlandı'
        ];
        
        // Zengin içerikli mesaj
        $success = NotifyCord::sendMessage(
            "Sipariş #{$order['id']} tamamlandı!",
            null, // Varsayılan webhook'u kullan
            function($message) use ($user, $order) {
                $message->embed(function($embed) use ($user, $order) {
                    $embed->title("Sipariş Onayı: #{$order['id']}")
                         ->description("Siparişiniz başarıyla tamamlandı.")
                         ->color('#2ecc71') // Yeşil renk
                         ->timestamp() // Şu anki zaman
                         ->field('Müşteri', $user->name, true)
                         ->field('Tutar', "{$order['total']} {$order['currency']}", true)
                         ->field('Ürün Sayısı', $order['items'], true)
                         ->field('Durum', $order['status'], true)
                         ->footer("Teşekkür ederiz!", "https://example.com/logo.png");
                })
                ->button('Siparişi Görüntüle', 'primary', 'view_order_' . $order['id'])
                ->button('Fatura İndir', 'secondary', 'download_invoice_' . $order['id'])
                ->button('Destek', 'link', null, 'https://example.com/support');
            }
        );
        
        return $success 
            ? "Zengin içerikli sipariş mesajı başarıyla gönderildi!" 
            : "Mesaj gönderilirken bir hata oluştu. Log dosyasını kontrol edin.";
    }
    
    /**
     * Sistem hatası bildirim örneği.
     */
    public function testErrorAlert()
    {
        try {
            // Bir hata simülasyonu
            throw new \Exception("Veritabanı bağlantısı sağlanamadı!");
        } catch (\Exception $e) {
            // Hata bildirimini Discord'a gönder
            NotifyCord::sendMessage(
                "Sistem Hatası!",
                null,
                function($message) use ($e) {
                    $message->embed(function($embed) use ($e) {
                        $embed->title("🚨 Kritik Sistem Hatası")
                             ->description("Uygulama bir hatayla karşılaştı.")
                             ->color('#e74c3c') // Kırmızı renk
                             ->timestamp()
                             ->field('Hata', $e->getMessage(), false)
                             ->field('Dosya', $e->getFile(), true)
                             ->field('Satır', $e->getLine(), true)
                             ->field('Sunucu', gethostname(), true);
                    });
                }
            );
            
            return "Hata bildirimi Discord'a gönderildi!";
        }
    }
}