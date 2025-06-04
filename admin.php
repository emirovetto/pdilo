<?php
session_start();
require_once __DIR__.'/db.php';
init_db();
$db=get_db();
$pass='admin123';

if(isset($_POST['logout'])){
    unset($_SESSION['logged']);
    header('Location: admin.php');
    exit;
}

if(isset($_POST['password'])){
    if($_POST['password']===$pass){
        $_SESSION['logged']=true;
    }else{
        $error='Contraseña incorrecta';
    }
}

// handle actions when logged
if(!empty($_SESSION['logged'])){
    if(isset($_POST['updateSettings'])){
        $keys=['name','logo','whatsappNumber','transferAlias','deliveryFee','primaryColor','secondaryColor'];
        foreach($keys as $k){
            if(isset($_POST[$k])){
                $stmt=$db->prepare("REPLACE INTO settings(`key`,`value`) VALUES(?,?)");
                $stmt->execute([$k,trim($_POST[$k])]);
            }
        }
        $message='Configuración guardada';
    }

    if(isset($_POST['addCategory'])){
        $stmt=$db->prepare("INSERT INTO categories(name,icon) VALUES(?,?)");
        $stmt->execute([trim($_POST['newName']),trim($_POST['newIcon'])]);
    }
    if(isset($_POST['updateCategory'])){
        $stmt=$db->prepare("UPDATE categories SET name=?,icon=? WHERE id=?");
        $stmt->execute([trim($_POST['name']),trim($_POST['icon']),$_POST['id']]);
    }
    if(isset($_POST['deleteCategory'])){
        $stmt=$db->prepare("DELETE FROM categories WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }

    if(isset($_POST['addProduct'])){
        $stmt=$db->prepare("INSERT INTO products(category_id,name,description,price,image) VALUES(?,?,?,?,?)");
        $stmt->execute([$_POST['new_category_id'],trim($_POST['new_name']),trim($_POST['new_description']),$_POST['new_price'],trim($_POST['new_image'])]);
    }
    if(isset($_POST['updateProduct'])){
        $stmt=$db->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,image=? WHERE id=?");
        $stmt->execute([$_POST['category_id'],trim($_POST['name']),trim($_POST['description']),$_POST['price'],trim($_POST['image']),$_POST['id']]);
    }
    if(isset($_POST['deleteProduct'])){
        $stmt=$db->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$_POST['id']]);
    }
}

// load current data
$settings=[];
$stmt=$db->query("SELECT `key`,`value` FROM settings");
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
    $settings[$row['key']]=$row['value'];
}
$settings+=['name'=>'Menu','logo'=>'','whatsappNumber'=>'','transferAlias'=>'','deliveryFee'=>'0','primaryColor'=>'#16a34a','secondaryColor'=>'#15803d'];
$settings['deliveryFee']=(int)$settings['deliveryFee'];

$categories=$db->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$products=$db->query("SELECT * FROM products ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Administración</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="text-white p-6 text-center" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">
  <h1 class="text-2xl font-bold">Administración - <?php echo htmlspecialchars($settings['name']); ?></h1>
</div>
<div class="p-6 max-w-5xl mx-auto">
<?php if(empty($_SESSION['logged'])): ?>
  <div class="max-w-sm mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Iniciar sesión</h2>
    <?php if(!empty($error)): ?><p class="text-red-600 mb-2"><?php echo $error; ?></p><?php endif; ?>
    <form method="post" class="space-y-4">
      <input type="password" name="password" class="w-full border p-2" placeholder="Contraseña" />
      <button class="w-full text-white py-2 rounded" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Entrar</button>
    </form>
  </div>
<?php else: ?>
  <?php if(!empty($message)): ?><p class="text-green-600 mb-4 text-center"><?php echo $message; ?></p><?php endif; ?>
  <form method="post" class="bg-white p-4 rounded shadow mb-8">
    <h2 class="text-lg font-bold mb-4">Datos del menú</h2>
    <input type="hidden" name="updateSettings" value="1">
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div><label class="block text-sm">Nombre</label><input name="name" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['name']); ?>"></div>
      <div><label class="block text-sm">Logo</label><input name="logo" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['logo']); ?>"></div>
      <div><label class="block text-sm">WhatsApp</label><input name="whatsappNumber" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['whatsappNumber']); ?>"></div>
      <div><label class="block text-sm">Alias transferencia</label><input name="transferAlias" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['transferAlias']); ?>"></div>
      <div><label class="block text-sm">Costo envío</label><input type="number" name="deliveryFee" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['deliveryFee']); ?>"></div>
      <div><label class="block text-sm">Color primario</label><input name="primaryColor" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['primaryColor']); ?>"></div>
      <div><label class="block text-sm">Color secundario</label><input name="secondaryColor" class="w-full border p-2" value="<?php echo htmlspecialchars($settings['secondaryColor']); ?>"></div>
    </div>
    <button class="px-4 py-2 text-white rounded" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Guardar</button>
    <button name="logout" class="ml-4 text-red-600">Salir</button>
  </form>

  <div class="bg-white p-4 rounded shadow mb-8">
    <h2 class="text-lg font-bold mb-4">Categorías</h2>
    <?php foreach($categories as $cat): ?>
      <form method="post" class="flex gap-2 mb-2">
        <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
        <input name="name" value="<?php echo htmlspecialchars($cat['name']); ?>" class="border p-2 flex-1">
        <input name="icon" value="<?php echo htmlspecialchars($cat['icon']); ?>" class="border p-2 w-24">
        <button name="updateCategory" class="px-3 text-white rounded" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Guardar</button>
        <button name="deleteCategory" class="px-3 bg-red-500 text-white rounded" onclick="return confirm('¿Eliminar?');">Borrar</button>
      </form>
    <?php endforeach; ?>
    <form method="post" class="flex gap-2">
      <input name="newName" placeholder="Nueva categoría" class="border p-2 flex-1">
      <input name="newIcon" placeholder="Icono" class="border p-2 w-24">
      <button name="addCategory" class="px-3 text-white rounded" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Agregar</button>
    </form>
  </div>

  <div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-bold mb-4">Productos</h2>
    <?php foreach($products as $p): ?>
      <form method="post" class="grid grid-cols-7 gap-2 mb-2 items-center">
        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
        <select name="category_id" class="border p-2">
          <?php foreach($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$p['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endforeach; ?>
        </select>
        <input name="name" value="<?php echo htmlspecialchars($p['name']); ?>" class="border p-2">
        <input name="description" value="<?php echo htmlspecialchars($p['description']); ?>" class="border p-2">
        <input type="number" name="price" value="<?php echo htmlspecialchars($p['price']); ?>" class="border p-2 w-24">
        <input name="image" value="<?php echo htmlspecialchars($p['image']); ?>" class="border p-2 w-24">
        <button name="updateProduct" class="px-3 text-white rounded" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Guardar</button>
        <button name="deleteProduct" class="px-3 bg-red-500 text-white rounded" onclick="return confirm('¿Eliminar?');">Borrar</button>
      </form>
    <?php endforeach; ?>
    <form method="post" class="grid grid-cols-7 gap-2 items-center">
      <select name="new_category_id" class="border p-2">
        <?php foreach($categories as $c): ?>
          <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
      <input name="new_name" placeholder="Nombre" class="border p-2">
      <input name="new_description" placeholder="Descripción" class="border p-2">
      <input type="number" name="new_price" placeholder="Precio" class="border p-2 w-24">
      <input name="new_image" placeholder="Imagen" class="border p-2 w-24">
      <button name="addProduct" class="px-3 text-white rounded col-span-2" style="background-color: <?php echo htmlspecialchars($settings['primaryColor']); ?>">Agregar</button>
    </form>
  </div>
<?php endif; ?>
</div>
</body>
</html>
