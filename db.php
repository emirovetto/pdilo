<?php
function get_db(){
    static $pdo;
    if(!$pdo){
        $host='localhost';
        $db='u102838416_pdilo';
        $user='u102838416_pdilo';
        $pass='Rovetto5!';
        $pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

function init_db(){
    $db=get_db();
    $db->exec("CREATE TABLE IF NOT EXISTS settings (`key` VARCHAR(50) PRIMARY KEY, `value` TEXT NOT NULL)");
    $db->exec("CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, icon VARCHAR(20) DEFAULT '')");
    $db->exec("CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, category_id INT NOT NULL, name VARCHAR(100) NOT NULL, description TEXT, price INT NOT NULL, image VARCHAR(255) DEFAULT '', FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE)");
    seed_defaults($db);
}

function seed_defaults($db){
    $count=$db->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if($count==0){
        $defaults=[
            'name'=>'La Esquina Gourmet',
            'logo'=>'🍕',
            'whatsappNumber'=>'5493493123456',
            'transferAlias'=>'LAESQUINA.GOURMET.MP',
            'deliveryFee'=>'500',
            'primaryColor'=>'#e74c3c',
            'secondaryColor'=>'#c0392b'
        ];
        $stmt=$db->prepare("INSERT INTO settings(`key`,`value`) VALUES(?,?)");
        foreach($defaults as $k=>$v){
            $stmt->execute([$k,$v]);
        }
    }
    $count=$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if($count==0){
        $cats=[['name'=>'Pizzas','icon'=>'🍕'],['name'=>'Hamburguesas','icon'=>'🍔'],['name'=>'Bebidas','icon'=>'🥤']];
        $stmt=$db->prepare("INSERT INTO categories(name,icon) VALUES(?,?)");
        foreach($cats as $c){
            $stmt->execute([$c['name'],$c['icon']]);
        }
    }
    $count=$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if($count==0){
        $prods=[
            ['categoryId'=>1,'name'=>'Pizza Margherita','description'=>'Salsa de tomate, mozzarella, albahaca fresca','price'=>3500,'image'=>'🍕'],
            ['categoryId'=>1,'name'=>'Pizza Pepperoni','description'=>'Salsa de tomate, mozzarella, pepperoni','price'=>4200,'image'=>'🍕'],
            ['categoryId'=>2,'name'=>'Hamburguesa Clásica','description'=>'Carne, lechuga, tomate, cebolla, queso','price'=>3800,'image'=>'🍔'],
            ['categoryId'=>2,'name'=>'Hamburguesa BBQ','description'=>'Carne, cebolla caramelizada, queso, salsa BBQ','price'=>4500,'image'=>'🍔'],
            ['categoryId'=>3,'name'=>'Coca Cola','description'=>'500ml','price'=>800,'image'=>'🥤'],
            ['categoryId'=>3,'name'=>'Agua Mineral','description'=>'500ml','price'=>600,'image'=>'💧']
        ];
        $stmt=$db->prepare("INSERT INTO products(category_id,name,description,price,image) VALUES(?,?,?,?,?)");
        foreach($prods as $p){
            $stmt->execute([$p['categoryId'],$p['name'],$p['description'],$p['price'],$p['image']]);
        }
    }
}
?>
