<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MailSetting;
use Illuminate\Support\Facades\Crypt;

class ConfigServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cofig_mail = new MailSetting();
        $cofig_mail->mail_driver = "smtp";
        $cofig_mail->mail_host = "smtp.gmail.com";
        $cofig_mail->mail_port = 587;
        $cofig_mail->mail_username = "hamilton_diazru@fet.edu.co";
        $cofig_mail->mail_password = Crypt::encryptString("Hadiru.123");
        $cofig_mail->mail_encryption = "tls";
        $cofig_mail->mail_from_address = "hamilton_diazru@fet.edu.co";
        $cofig_mail->mail_from_name = "Repo Proyecto";

        $cofig_mail->save();
    }
}
