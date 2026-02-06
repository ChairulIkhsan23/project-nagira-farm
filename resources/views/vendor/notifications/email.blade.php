<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Nagira Farm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin: 0; padding: 0; background-color: #f7fafc; font-family: 'Poppins', Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header Email -->
        <div style="background: linear-gradient(135deg, #38a169 0%, #48bb78 100%); padding: 50px 30px 40px; text-align: center; border-radius: 0 0 20px 20px;">

            <!-- Title -->
            <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: 0.5px;">
                Reset Password
            </h1>

            <!-- Description -->
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 16px; font-weight: 300;">
                Nagira Farm Account Security
            </p>
        </div>

        <!-- Konten Utama -->
        <div style="padding: 40px 30px;">
            <!-- Salam -->
            <div style="margin-bottom: 30px;">
                <h2 style="color: #2d3748; margin: 0 0 10px; font-size: 22px; font-weight: 600;">
                    {{ !empty($greeting) ? $greeting : 'Halo ' . $user->name . '!' }}
                </h2>
                <p style="color: #718096; margin: 0; font-size: 15px; line-height: 1.6;">
                    Kami menerima permintaan untuk mengubah password akun Anda di <strong style="color: #38a169;">Nagira Farm</strong>.
                </p>
            </div>

            <!-- Kartu Informasi -->
            <div style="background-color: #f0fff4; border-left: 4px solid #38a169; padding: 20px; border-radius: 8px; margin-bottom: 35px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <svg style="width: 20px; height: 20px; color: #38a169; margin-right: 10px;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 style="color: #276749; margin: 0; font-size: 16px; font-weight: 600;">Permintaan Reset Password</h3>
                </div>
                <p style="color: #2d3748; margin: 0; font-size: 14px; line-height: 1.6;">
                    Jika Anda yang melakukan permintaan ini, silakan reset password Anda dalam waktu <strong>60 menit</strong> untuk keamanan akun.
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div style="text-align: center; margin-bottom: 40px;">
                <a href="{{ $actionUrl }}" style="display: inline-block; background: linear-gradient(135deg, #38a169 0%, #48bb78 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(56, 161, 105, 0.3); transition: all 0.3s ease;">
                    Reset Password Sekarang
                </a>
                <p style="color: #a0aec0; font-size: 13px; margin-top: 15px;">
                    Tombol di atas akan berlaku selama 60 menit
                </p>
            </div>

            <!-- Peringatan Keamanan -->
            <div style="background-color: #fff5f5; border: 1px solid #fed7d7; padding: 20px; border-radius: 10px; margin-bottom: 35px;">
                <div style="display: flex; align-items: flex-start;">
                    <svg style="width: 20px; height: 20px; color: #f56565; margin-right: 12px; flex-shrink: 0; margin-top: 2px;" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 style="color: #c53030; margin: 0 0 8px; font-size: 15px; font-weight: 600;">Jika BUKAN Anda yang meminta:</h4>
                        <p style="color: #744210; margin: 0; font-size: 14px; line-height: 1.6;">
                            Abaikan email ini. Password Anda akan tetap aman. Namun, jika Anda merasa ada aktivitas mencurigakan, 
                            segera hubungi tim support kami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Link Alternatif -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px dashed #cbd5e0;">
                <p style="color: #718096; margin: 0 0 12px; font-size: 14px; font-weight: 500;">
                    Atau salin dan tempel link berikut di browser:
                </p>
                <div style="background-color: white; padding: 12px 15px; border-radius: 6px; border: 1px solid #e2e8f0; word-break: break-all;">
                    <a href="{{ $actionUrl }}" style="color: #4299e1; text-decoration: none; font-size: 13px; font-family: monospace;">
                        {{ $displayableActionUrl }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #2d3748; padding: 30px; text-align: center; border-radius: 20px 20px 0 0;">
            <div style="margin-bottom: 20px;">
                <span style="color: #cbd5e0; font-size: 22px; font-weight: 700;">NAGIRA FARM</span>
            </div>
            <p style="color: #a0aec0; margin: 0 0 20px; font-size: 14px; line-height: 1.6;">
                Dedicated to providing the freshest organic products<br>
                directly from our farm to your table
            </p>
            <div style="border-top: 1px solid #4a5568; padding-top: 20px;">
                <p style="color: #cbd5e0; margin: 0; font-size: 13px;">
                    © {{ date('Y') }} Nagira Farm. All rights reserved.<br>
                    <span style="color: #a0aec0; font-size: 12px;">
                        Email ini dikirim secara otomatis, mohon tidak membalas
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Styling untuk hover effects (hanya bekerja di beberapa client email) -->
    <style>
        @media only screen and (max-width: 600px) {
            div { padding-left: 20px !important; padding-right: 20px !important; }
            h1 { font-size: 24px !important; }
            h2 { font-size: 20px !important; }
        }
        
        a:hover {
            opacity: 0.9;
            box-shadow: 0 6px 20px rgba(56, 161, 105, 0.4) !important;
        }
    </style>
</body>
</html>