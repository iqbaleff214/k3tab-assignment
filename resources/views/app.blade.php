<!--
Starter Kit Software License Agreement (Single-Device License)
----------------------------------------------------------

Copyright (c) 2025 404 Not Found Indonesia

This software is licensed, not sold.

By installing, copying, or otherwise using this software ("Starter Kit"), you agree to be bound by the terms of this license agreement.

1. GRANT OF LICENSE
You are granted a non-exclusive, non-transferable license to install and use Starter Kit on **one (1) device only**. This license is intended solely for the registered licensee and may not be shared, transferred, or used concurrently on multiple devices.

2. RESTRICTIONS
You may NOT:
- Sell, resell, rent, lease, or otherwise commercialize the software or any of its parts, including modified versions, without explicit written permission from the licensor.
- Copy, distribute, or sublicense the software.
- Reverse-engineer, decompile, or disassemble any part of the software.
- Modify, alter, or create derivative works of the software.
- Use the software on more than one device unless explicitly authorized in writing.

3. OWNERSHIP
This software remains the exclusive property of 404NotFoundIndonesia. All rights not expressly granted are reserved.

4. ACTIVATION AND VALIDATION
This software may include a license key or activation system. Tampering with the activation mechanism is strictly prohibited.

5. TERMINATION
This license is effective until terminated. It will terminate automatically without notice from 404NotFoundIndonesia if you fail to comply with any provision of this agreement. Upon termination, you must destroy all copies of the software.

6. DISCLAIMER OF WARRANTY
The software is provided "as is" without warranty of any kind. Use of the software is at your own risk.

7. LIMITATION OF LIABILITY
404NotFoundIndonesia shall not be liable for any damages arising out of the use or inability to use the software, including but not limited to lost profits, lost data, or hardware failure.

8. GOVERNING LAW
This agreement shall be governed by the laws of Indonesia, without regard to conflict of law principles.

For licensing inquiries, please contact:
**iqbaleff214@gmail.com**

----------------------------------------------------------
Unauthorized copying or distribution of this software is strictly prohibited.

-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <!-- Favicon standar -->
        <link rel="icon" type="image/jpg" sizes="32x32" href="/CORSA.jpeg">
        <link rel="icon" type="image/jpg" sizes="16x16" href="/CORSA.jpeg">

        <!-- Apple Touch Icon (iOS) -->
        <link rel="apple-touch-icon" sizes="180x180" href="/CORSA.jpeg">

        <!-- Android/Chrome -->
        <link rel="icon" sizes="192x192" href="/CORSA.jpeg">

        <!-- Title -->
        <meta property="og:title" content="{{ config('app.name') }}">
        <!-- Deskripsi -->
        <meta property="og:description" content="K3TAB 2025 - POLIBAN">
        <!-- URL -->
        <meta property="og:url" content="{{ config('app.url') }}">
        <!-- Gambar -->
        <meta property="og:image" content="https://raw.githubusercontent.com/iqbaleff214/k3tab-assignment/refs/heads/main/public/CORSA.jpeg">
        <!-- Tipe konten -->
        <meta property="og:type" content="website">

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
