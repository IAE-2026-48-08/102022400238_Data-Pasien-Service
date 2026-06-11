<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Service Data Pasien API",
 * description="API Documentation untuk Service Data Pasien E-Healthcare"
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="ApiKeyAuth",
 * type="apiKey",
 * in="header",
 * name="X-IAE-KEY"
 * )
 */
abstract class Controller
{
    //
}