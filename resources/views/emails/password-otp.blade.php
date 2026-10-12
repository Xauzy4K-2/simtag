<!DOCTYPE html>
<html lang="id">
<body style="margin:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 12px;">
        <tr><td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;overflow:hidden;">
                <tr><td style="background:#047857;padding:24px;color:#ffffff;">
                    <div style="font-size:11px;letter-spacing:3px;opacity:.8;">PDAM KOTA</div>
                    <div style="font-size:18px;font-weight:bold;letter-spacing:1px;">TIRTA PINANG</div>
                </td></tr>
                <tr><td style="padding:28px 24px;">
                    <p style="margin:0 0 12px;">Halo <b>{{ $user->name }}</b>,</p>
                    <p style="margin:0 0 20px;">Kami menerima permintaan untuk mengatur ulang kata sandi akun SIMTAG Anda. Gunakan kode berikut:</p>
                    <div style="text-align:center;font-size:32px;font-weight:bold;letter-spacing:10px;background:#ecfdf5;color:#065f46;border-radius:12px;padding:16px;">{{ $otp }}</div>
                    <p style="margin:20px 0 0;font-size:13px;color:#64748b;">Kode berlaku selama {{ $ttl }} menit. Jangan berikan kode ini kepada siapa pun, termasuk petugas PDAM. Abaikan email ini jika Anda tidak meminta reset kata sandi.</p>
                </td></tr>
                <tr><td style="padding:16px 24px;background:#f8fafc;font-size:11px;color:#94a3b8;text-align:center;">
                    SIMTAG &middot; Perumda Air Minum Tirta Pinang Kota Pangkalpinang
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
