<?php
namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Crypt;

class MailConfigService
{
    public function configure()
    {
        $settings = MailSetting::find(1); // O el ID que uses

        config([
            'mail.mailers.smtp.host' => $settings->mail_host,
            'mail.mailers.smtp.port' => $settings->mail_port,
            'mail.mailers.smtp.username' => $settings->mail_username,
            'mail.mailers.smtp.password' => Crypt::decryptString($settings->mail_password), // Desencriptar la contraseña
            'mail.mailers.smtp.encryption' => $settings->mail_encryption,
            'mail.from.address' => $settings->mail_from_address,
            'mail.from.name' => $settings->mail_from_name,
        ]);
    }
}
