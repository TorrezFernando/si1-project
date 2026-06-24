<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Servicio para integrar la pasarela de pagos Libelula (https://libelula.bo).
// Provee metodos para registrar deudas, consultar pagos y verificar estado.
class LibelulaService
{
    protected string $appkey;
    protected string $apiUrl;
    protected string $callbackUrl;
    protected string $moneda;
    protected string $emiteFactura;

    // Carga la configuracion desde config/libelula.php (valores del .env).
    public function __construct()
    {
        $this->appkey = config('libelula.appkey');
        $this->apiUrl = config('libelula.api_url');
        $this->callbackUrl = config('libelula.callback_url');
        $this->moneda = config('libelula.moneda');
        $this->emiteFactura = config('libelula.emite_factura');
    }

    // Registra una deuda en Libelula y devuelve la respuesta con url_pasarela_pagos.
    // Parametros: datos del cliente, identificador unico de deuda, monto, etc.
    public function registrarDeuda(
        string $emailCliente,
        string $identificadorDeuda,
        string $fechaVencimiento,
        string $descripcion,
        string $nombreCliente,
        string $apellidoCliente,
        string $ci,
        float $monto,
    ): ?array {
        $url = rtrim($this->apiUrl, '/') . '/rest/deuda/registrar';

        $payload = [
            'appkey' => $this->appkey,
            'email_cliente' => $emailCliente,
            'identificador_deuda' => $identificadorDeuda,
            'fecha_vencimiento' => $fechaVencimiento,
            'descripcion' => $descripcion,
            'callback_url' => rtrim($this->callbackUrl, '/') . '/pasarela/callback',
            'nombre_cliente' => $nombreCliente,
            'apellido_cliente' => $apellidoCliente,
            'ci' => $ci,
            'moneda' => $this->moneda,
            'emite_factura' => strtolower($this->emiteFactura),
            'lineas_detalle_deuda' => [
                [
                    'cantidad' => 1,
                    'descripcion' => $descripcion,
                    'precio' => $monto,
                    'total' => $monto,
                ],
            ],
        ];

        try {
            $response = Http::timeout(30)->withoutVerifying()->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Libelula: deuda registrada exitosamente', [
                    'identificador_deuda' => $identificadorDeuda,
                    'response' => $data,
                ]);

                return $data;
            }

            Log::error('Libelula: error al registrar deuda', [
                'identificador_deuda' => $identificadorDeuda,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Libelula: excepcion al registrar deuda', [
                'identificador_deuda' => $identificadorDeuda,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    // Consulta pagos realizados en un rango de fechas (para conciliacion).
    public function consultarPagos(string $fechaInicial, string $fechaFinal): ?array
    {
        $url = rtrim($this->apiUrl, '/') . '/rest/deuda/consultar_pagos';

        try {
            $response = Http::timeout(30)->withoutVerifying()->get($url, [
                'appkey' => $this->appkey,
                'fecha_inicial' => $fechaInicial,
                'fecha_final' => $fechaFinal,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Libelula: error al consultar pagos', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Libelula: excepcion al consultar pagos', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    // Consulta una deuda especifica por su identificador unico.
    public function consultarDeudaPorIdentificador(string $identificadorDeuda): ?array
    {
        $url = rtrim($this->apiUrl, '/') . '/rest/deuda/consultar';

        try {
            $response = Http::timeout(30)->withoutVerifying()->get($url, [
                'appkey' => $this->appkey,
                'identificador_deuda' => $identificadorDeuda,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Libelula: error al consultar deuda', [
                'identificador_deuda' => $identificadorDeuda,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Libelula: excepcion al consultar deuda', [
                'identificador_deuda' => $identificadorDeuda,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    // Verifica si la pasarela tiene una APPKEY configurada en el .env.
    public function configurado(): bool
    {
        return ! empty($this->appkey);
    }
}
