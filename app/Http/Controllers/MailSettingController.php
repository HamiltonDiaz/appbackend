<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MailSettingController extends Controller
{
    /**
     * Almacena una nueva configuración de correo electrónico o actualiza la existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|string',
            'mail_from_name' => 'required|string',
        ]);

        $data = $request->all();
        $data['mail_password'] = Crypt::encryptString($request->mail_password); // Encriptar la contrase a

        $settings = MailSetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return response()->json([
            'status' => 200,
            'success' => true, 
            'data'=> $settings,
            ]);
    }

    /**
     * Muestra la configuraci n de correo electr nico actual.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $settings = MailSetting::find(1); // O el ID que uses
        return response()->json($settings);
    }
}
