<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333333; text-align: center;">Recuperación de Contraseña de Administrador</h2>
        
        <p style="color: #555555; font-size: 16px;">Has solicitado restablecer la contraseña de tu cuenta de administrador. Utiliza el siguiente código de verificación de 6 dígitos para completar el proceso:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; padding: 15px 25px; background-color: #007bff; color: #ffffff; font-size: 24px; font-weight: bold; border-radius: 5px; letter-spacing: 5px;">
                {{ $code }}
            </span>
        </div>
        
        <p style="color: #555555; font-size: 16px;">Este código expirará en 15 minutos.</p>
        
        <p style="color: #888888; font-size: 14px; margin-top: 40px; border-top: 1px solid #eeeeee; padding-top: 20px;">
            Si no solicitaste un restablecimiento de contraseña, por favor ignora este correo. No es necesario realizar ninguna acción adicional.
        </p>
    </div>
</body>
</html>
