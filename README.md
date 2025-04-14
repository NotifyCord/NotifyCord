<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/notifycord-logo.png" alt="NotifyCord Logo" width="180">
</p>

<h1 align="center">NotifyCord</h1>

<p align="center">
  Laravel için güçlü ve esnek Discord bildirim paketi
</p>

<p align="center">
  <a href="https://packagist.org/packages/notifycord/notifycord"><img src="https://img.shields.io/packagist/v/notifycord/notifycord.svg?style=for-the-badge" alt="Latest Version on Packagist"></a>
  <a href="https://packagist.org/packages/notifycord/notifycord"><img src="https://img.shields.io/packagist/dt/notifycord/notifycord.svg?style=for-the-badge" alt="Total Downloads"></a>
  <a href="https://github.com/NotifyCord/NotifyCord"><img src="https://img.shields.io/badge/github-NotifyCord%2FNotifyCord-blue?style=for-the-badge" alt="GitHub Repository"></a>
  <a href="https://github.com/NotifyCord/NotifyCord/blob/main/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-green?style=for-the-badge" alt="License"></a>
</p>

<p align="center">
NotifyCord, Laravel uygulamanızdan Discord bildirimlerini göndermek için güçlü ve esnek bir pakettir. Zengin Discord mesajlarını kanallar ve webhook'lar aracılığıyla göndermenin temiz ve basit bir yolunu sunar ve Laravel'in bildirim sistemiyle mükemmel bir şekilde entegre olur.
</p>

---

## ✨ Özellikler

- 🔌 Laravel'in Bildirim sistemiyle sorunsuz entegrasyon
- 🤖 Bot tabanlı mesajlaşma desteği (kanallara ve kullanıcılara)
- 🔗 Webhook tabanlı bildirimler için destek
- 📋 Zengin gömülü mesaj desteği (başlık, açıklama, alanlar, renkler vb.)
- 🔘 Discord bileşenleri desteği (butonlar, aksiyon satırları)
- 🔁 Otomatik hız sınırı yönetimi ve yeniden denemeler
- 🧩 Asenkron bildirimler için kuyruk uyumlu
- 🛡️ Detaylı istisnalarla hata yönetimi
- ⚙️ Kapsamlı yapılandırma seçenekleri

## 📋 Gereksinimler

- PHP 8.0 veya üzeri
- Laravel 9.0 veya üzeri
- GuzzleHTTP 7.0 veya üzeri

## 💻 Kurulum

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/installation.png" alt="Kurulum" width="500">
</p>

Paketi Composer ile yükleyebilirsiniz:

```bash
composer require notifycord/notifycord
```

Laravel'in paket otomatik keşfini kullanıyorsanız, paket servis sağlayıcısını otomatik olarak kaydedecektir.

Otomatik keşif olmadan Laravel kullanıyorsanız, servis sağlayıcısını `config/app.php` dosyanıza ekleyin:

```php
'providers' => [
    // ...
    NotifyCord\NotifyCord\NotifyCordServiceProvider::class,
],

'aliases' => [
    // ...
    'NotifyCord' => NotifyCord\NotifyCord\Facades\NotifyCord::class,
],
```

### Yapılandırma Dosyasını Yayınlama

Yapılandırma dosyasını şu komut ile yayınlayabilirsiniz:

```bash
php artisan vendor:publish --tag="notifycord-config"
```

Bu, uygulamanızda aşağıdaki seçeneklerle bir `config/notifycord.php` yapılandırma dosyası oluşturacaktır:

```php
return [
    'default_webhook' => env('DISCORD_WEBHOOK_URL'),
    'bot_token' => env('DISCORD_BOT_TOKEN'),
    'retry_on_rate_limit' => true,
    'retry_on_failure' => true,
    'max_retries' => 3,
    'retry_delay' => 2, // saniye
    'timeout' => 5, // saniye
    'connect_timeout' => 5, // saniye
    'log_errors' => true,
    // ...
];
```

### Ortam Yapılandırması

`.env` dosyanıza aşağıdakileri ekleyin:

```
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/webhook-id/webhook-token
DISCORD_BOT_TOKEN=bot-token  # İsteğe bağlı, sadece belirli kanallara göndermek istiyorsanız
```

## 📖 Kullanım Kılavuzu

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/notifycord-banner.png" alt="NotifyCord Banner" width="800">
</p>

### 🚀 Laravel Bildirim Sistemi Entegrasyonu

NotifyCord'u kullanmanın en kolay yolu, Laravel'in bildirim sistemi üzerinden kullanmaktır. İlk olarak, bir bildirim sınıfı oluşturun:

```bash
php artisan make:notification DiscordNotification
```

Sonra, oluşturulan bildirim sınıfına `toDiscord` metodunu ekleyin:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotifyCord\NotifyCord\Facades\NotifyCord;

class DiscordNotification extends Notification
{
    protected $message;
    
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    public function via($notifiable)
    {
        return ['discord'];
    }
    
    public function toDiscord($notifiable)
    {
        return NotifyCord::message($this->message)
            ->embed(function ($embed) {
                $embed->title('Önemli Bildirim')
                     ->description('Bu uygulamamızdan gelen önemli bir bildirimdir')
                     ->color('#ff0000')
                     ->timestamp()
                     ->footer('Uygulamanız', 'https://example.com/logo.png')
                     ->field('Durum', 'Aktif', true)
                     ->field('Ortam', app()->environment(), true);
            })
            ->button('Detayları Görüntüle', 'primary', 'view_details')
            ->button('Paneli Ziyaret Et', 'link', null, 'https://dashboard.example.com');
    }
}
```

Modelinizi Discord üzerinden "bildirim yapılabilir" hale getirmek için, `routeNotificationForDiscord` metodunu ekleyin:

```php
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    public function routeNotificationForDiscord()
    {
        // Webhook URL veya kanal ID'si döndürün
        return 'https://discord.com/api/webhooks/your-webhook-id/your-webhook-token';
        
        // Veya bot token kullanıyorsanız kanal ID'si döndürün
        // return '123456789012345678';
    }
}
```

Ardından bildirimi gönderin:

```php
$user->notify(new DiscordNotification('NotifyCord\'dan merhaba!'));
```

### 🔄 Doğrudan Kullanım

NotifyCord'u bildirim sınıfı olmadan doğrudan da kullanabilirsiniz:

```php
use NotifyCord\NotifyCord\Facades\NotifyCord;

// .env dosyasında yapılandırılmış varsayılan webhook'a gönder
NotifyCord::channel()->send(null, new class {
    public function toDiscord() {
        return NotifyCord::message('NotifyCord ile doğrudan mesaj!')
            ->embed(function ($embed) {
                $embed->title('Doğrudan Kullanım Örneği')
                     ->description('Bu mesaj, bildirim sınıfı olmadan doğrudan gönderilmiştir')
                     ->color('#00ff00');
            });
    }
});

// Veya özel bir webhook URL'si belirtin
$webhookUrl = 'https://discord.com/api/webhooks/custom/webhook';
$notifiable = new class {
    public function routeNotificationForDiscord() {
        return $webhookUrl;
    }
};

NotifyCord::channel()->send($notifiable, new class {
    public function toDiscord() {
        return NotifyCord::message('Özel webhook mesajı');
    }
});
```

## 📊 Mesaj Bileşenleri

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/discord-components.png" alt="Discord Components" width="700">
</p>

### 🎨 Zengin Mesaj Blokları (Embeds)

Discord embed'leri zengin formatlama seçenekleri sunar:

```php
NotifyCord::message('Embed içeren mesaj')
    ->embed(function ($embed) {
        $embed->title('Embed Başlığı')
             ->description('Bu bir embed açıklamasıdır')
             ->url('https://example.com')
             ->color('#3498db')
             ->timestamp() // Güncel zaman
             ->footer('Altbilgi metni', 'https://example.com/footer-icon.png')
             ->thumbnail('https://example.com/thumbnail.png')
             ->image('https://example.com/image.png')
             ->author('Yazar Adı', 'https://example.com', 'https://example.com/author-icon.png')
             ->field('Alan 1', 'Değer 1', true)
             ->field('Alan 2', 'Değer 2', true)
             ->field('Alan 3', 'Değer 3', false);
    });
```

### 🔘 İnteraktif Butonlar

Mesajlarınıza etkileşimli butonlar ekleyin:

```php
NotifyCord::message('Butonlu mesaj')
    ->button('Birincil Buton', 'primary', 'primary_button_id')
    ->button('İkincil Buton', 'secondary', 'secondary_button_id')
    ->button('Başarı Butonu', 'success', 'success_button_id')
    ->button('Tehlike Butonu', 'danger', 'danger_button_id')
    ->button('Web Sitesini Ziyaret Et', 'link', null, 'https://example.com');
```

Butonları birden fazla aksiyon satırına da düzenleyebilirsiniz:

```php
NotifyCord::message('Çoklu aksiyon satırlı mesaj')
    ->button('Buton 1', 'primary', 'button_1')
    ->button('Buton 2', 'secondary', 'button_2')
    ->addActionRow()
    ->button('Buton 3', 'success', 'button_3')
    ->button('Buton 4', 'danger', 'button_4');
```

## 📋 Örnekler

<p align="center">
  <img src="https://raw.githubusercontent.com/NotifyCord/NotifyCord/main/examples/example-preview.png" alt="Example Preview" width="650">
</p>

### 📱 Mobil Uygulama Bildirimleri

```php
// Mobil uygulama işlem bildirimi
public function sendPurchaseNotification($user, $transaction)
{
    $user->notify(new DiscordNotification("Yeni Satın Alma İşlemi: #{$transaction->id}"))
        ->embed(function ($embed) use ($transaction) {
            $embed->title("Satın Alma: {$transaction->product_name}")
                 ->description("Satın alma işleminiz başarıyla tamamlandı.")
                 ->timestamp()
                 ->color('#2ecc71')
                 ->field('Müşteri', $transaction->user->name, true)
                 ->field('Fiyat', "{$transaction->amount} {$transaction->currency}", true)
                 ->field('Durum', 'Onaylandı', true);
        })
        ->button('Sipariş Detayları', 'primary', 'view_order')
        ->button('Fatura', 'secondary', 'view_invoice')
        ->button('Destek', 'link', null, 'https://example.com/support');
}
```

### 🚨 Sistem Uyarıları

```php
// Sistem uyarı bildirimi
public function sendServerAlert($system, $metrics)
{
    return NotifyCord::message("Sistem Uyarısı: {$system->name}")
        ->embed(function ($embed) use ($system, $metrics) {
            $embed->title("🚨 Yüksek CPU Kullanımı")
                 ->description("Sunucu CPU kullanımı belirlenen eşiği aştı.")
                 ->color('#e74c3c')
                 ->timestamp()
                 ->field('Sunucu', $system->name, true)
                 ->field('CPU', "{$metrics->cpu_usage}%", true)
                 ->field('Bellek', "{$metrics->memory_usage}%", true)
                 ->field('Disk', "{$metrics->disk_usage}%", true)
                 ->footer("Sunucu Monitörü", "https://example.com/logo.png");
        });
}
```

## 🛡️ Güvenlik

Güvenlik açıklarını keşfederseniz, lütfen issue tracker'ı kullanmak yerine contact@example.com adresine e-posta gönderin.

## 📄 Lisans

MIT Lisansı (MIT). Daha fazla bilgi için lütfen [Lisans Dosyasına](LICENSE.md) bakın.

## 🤝 Katkıda Bulunma

Katkılarınızı memnuniyetle karşılıyoruz! Lütfen [Katkıda Bulunma Rehberimize](CONTRIBUTING.md) göz atın.
