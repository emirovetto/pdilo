<?php
session_start();
$pass = 'admin123';
$configPath = __DIR__.'/config.json';

if (isset($_POST['logout'])) {
    unset($_SESSION['logged']);
    header('Location: admin.php');
    exit;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === $pass) {
        $_SESSION['logged'] = true;
    } else {
        $error = 'Contraseña incorrecta';
    }
}

if (!empty($_SESSION['logged']) && isset($_POST['update'])) {
    $config = json_decode(file_get_contents($configPath), true);
    $config['name'] = $_POST['name'] ?? $config['name'];
    $config['logo'] = $_POST['logo'] ?? $config['logo'];
    $config['whatsappNumber'] = $_POST['whatsappNumber'] ?? $config['whatsappNumber'];
    $config['transferAlias'] = $_POST['transferAlias'] ?? $config['transferAlias'];
    $config['deliveryFee'] = (int)($_POST['deliveryFee'] ?? $config['deliveryFee']);
    $config['primaryColor'] = $_POST['primaryColor'] ?? $config['primaryColor'];
    $config['secondaryColor'] = $_POST['secondaryColor'] ?? $config['secondaryColor'];
    $cats = json_decode($_POST['categories'], true);
    $prods = json_decode($_POST['products'], true);
    if ($cats !== null) $config['categories'] = $cats;
    if ($prods !== null) $config['products'] = $prods;
    file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    $message = 'Configuración actualizada';
}

$config = json_decode(file_get_contents($configPath), true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración</title>
<link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
<?php if (empty($_SESSION['logged'])): ?>
  <h1 class="text-xl font-bold mb-4">Iniciar sesión</h1>
  <?php if (!empty($error)): ?><p class="text-red-600 mb-2"><?php echo $error; ?></p><?php endif; ?>
  <form method="post" class="space-y-4">
    <input type="password" name="password" class="w-full border p-2" placeholder="Contraseña" />
    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Entrar</button>
  </form>
<?php else: ?>
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Configuración</h1>
    <form method="post"><button name="logout" class="text-sm text-red-600">Salir</button></form>
  </div>
  <?php if (!empty($message)): ?><p class="text-green-600 mb-2"><?php echo $message; ?></p><?php endif; ?>
  <form method="post" class="space-y-4">
    <div><label class="block text-sm">Nombre</label><input name="name" class="w-full border p-2" value="<?php echo htmlspecialchars($config['name']); ?>"></div>
    <div><label class="block text-sm">Logo</label><input name="logo" class="w-full border p-2" value="<?php echo htmlspecialchars($config['logo']); ?>"></div>
    <div><label class="block text-sm">WhatsApp</label><input name="whatsappNumber" class="w-full border p-2" value="<?php echo htmlspecialchars($config['whatsappNumber']); ?>"></div>
    <div><label class="block text-sm">Alias transferencia</label><input name="transferAlias" class="w-full border p-2" value="<?php echo htmlspecialchars($config['transferAlias']); ?>"></div>
    <div><label class="block text-sm">Costo de envío</label><input name="deliveryFee" type="number" class="w-full border p-2" value="<?php echo htmlspecialchars($config['deliveryFee']); ?>"></div>
    <div><label class="block text-sm">Color primario</label><input name="primaryColor" class="w-full border p-2" value="<?php echo htmlspecialchars($config['primaryColor']); ?>"></div>
    <div><label class="block text-sm">Color secundario</label><input name="secondaryColor" class="w-full border p-2" value="<?php echo htmlspecialchars($config['secondaryColor']); ?>"></div>
    <div><label class="block text-sm">Categorías (JSON)</label><textarea name="categories" class="w-full border p-2" rows="4"><?php echo json_encode($config['categories'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); ?></textarea></div>
    <div><label class="block text-sm">Productos (JSON)</label><textarea name="products" class="w-full border p-2" rows="6"><?php echo json_encode($config['products'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE); ?></textarea></div>
    <button type="submit" name="update" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
  </form>
  <p class="mt-4"><a href="index.php" class="text-blue-600">Volver al menú</a></p>
<?php endif; ?>
</div>
</body>
</html>
