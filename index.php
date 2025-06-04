<?php
$businessConfig = json_decode(file_get_contents(__DIR__.'/config.json'), true);
if (!$businessConfig) {
    die('Error al leer configuracion');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo $businessConfig['name']; ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .modal{display:none}
</style>
</head>
<body class="bg-gray-50">
<div class="text-white p-6 text-center relative" style="background-color: <?php echo $businessConfig['primaryColor']; ?>">
  <div class="text-4xl mb-2"><?php echo $businessConfig['logo']; ?></div>
  <h1 class="text-2xl font-bold"><?php echo $businessConfig['name']; ?></h1>
  <p class="text-sm opacity-90 mt-1">Menú Digital</p>
</div>

<div id="productList" class="p-4 pb-20">
<?php foreach($businessConfig['categories'] as $cat):
      $products=array_filter($businessConfig['products'],function($p)use($cat){return $p['categoryId']==$cat['id'];});
      if(empty($products)) continue; ?>
  <div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
      <span class="text-3xl"><?php echo $cat['icon']; ?></span>
      <h2 class="text-xl font-bold"><?php echo $cat['name']; ?></h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php foreach($products as $product): ?>
      <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-4">
          <div class="text-center mb-3">
            <span class="text-4xl"><?php echo $product['image']; ?></span>
          </div>
          <h3 class="font-semibold text-lg mb-2"><?php echo $product['name']; ?></h3>
          <p class="text-gray-600 text-sm mb-3"><?php echo $product['description']; ?></p>
          <div class="flex items-center justify-between">
            <span class="text-xl font-bold" style="color: <?php echo $businessConfig['primaryColor']; ?>">$<?php echo number_format($product['price'],0,',','.'); ?></span>
            <button class="add-to-cart text-white px-4 py-2 rounded-lg hover:opacity-90 transition-opacity" style="background-color: <?php echo $businessConfig['primaryColor']; ?>" data-id="<?php echo $product['id']; ?>">Agregar</button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- Floating Cart Button -->
<div id="cartBtnWrapper" class="fixed bottom-4 right-4 z-40 hidden">
  <button id="toggleCart" class="bg-green-500 text-white p-4 rounded-full shadow-lg hover:bg-green-600 relative">
    🛒
    <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">0</span>
  </button>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-end justify-center p-4 z-50" style="display:none;">
  <div class="bg-white rounded-t-lg w-full max-w-md max-h-96 overflow-hidden">
    <div class="p-4 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Tu Pedido</h3>
      <button id="closeCart" class="text-gray-500 hover:text-gray-700">×</button>
    </div>
    <div id="cartItems" class="p-4 max-h-64 overflow-y-auto"></div>
    <div class="p-4 border-t">
      <div class="flex justify-between items-center mb-4">
        <span class="text-lg font-semibold">Subtotal:</span>
        <span id="cartSubtotal" class="text-lg font-bold">$0</span>
      </div>
      <button id="openCheckout" class="w-full bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 font-semibold">Continuar 📝</button>
    </div>
  </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" style="display:none;">
  <div class="bg-white rounded-lg w-full max-w-lg my-8">
    <div class="p-4 border-b flex justify-between items-center">
      <h3 class="text-lg font-semibold">Finalizar Pedido</h3>
      <button id="closeCheckout" class="text-gray-500 hover:text-gray-700">×</button>
    </div>
    <div class="p-6 max-h-96 overflow-y-auto">
      <div class="mb-6">
        <h4 class="font-semibold text-gray-800 mb-3">👤 Datos Personales</h4>
        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="block text-sm font-medium mb-1">Nombre *</label>
            <input type="text" id="firstName" class="w-full p-2 border rounded-lg text-sm" placeholder="Juan" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Apellido *</label>
            <input type="text" id="lastName" class="w-full p-2 border rounded-lg text-sm" placeholder="Pérez" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Teléfono *</label>
          <input type="tel" id="phone" class="w-full p-2 border rounded-lg text-sm" placeholder="3493123456" />
        </div>
      </div>
      <div class="mb-6">
        <h4 class="font-semibold text-gray-800 mb-3">🚚 Forma de Entrega</h4>
        <div class="space-y-2 mb-3">
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="deliveryType" value="pickup" checked class="text-green-500" /><span class="text-sm">🏪 Retiro en el local (sin costo)</span></label>
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="deliveryType" value="delivery" class="text-green-500" /><span class="text-sm">🏠 Envío a domicilio (+$<?php echo number_format($businessConfig['deliveryFee'],0,',','.'); ?>)</span></label>
        </div>
        <div id="addressWrapper" class="hidden">
          <label class="block text-sm font-medium mb-1">Dirección *</label>
          <input type="text" id="address" class="w-full p-2 border rounded-lg text-sm" placeholder="Av. San Martín 123" />
        </div>
      </div>
      <div class="mb-6">
        <h4 class="font-semibold text-gray-800 mb-3">💳 Forma de Pago</h4>
        <div class="space-y-2 mb-3">
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="paymentMethod" value="cash" checked class="text-green-500" /><span class="text-sm">💵 Efectivo</span></label>
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="paymentMethod" value="transfer" class="text-green-500" /><span class="text-sm">🏦 Transferencia</span></label>
        </div>
        <div id="cashOptions" class="ml-6 space-y-2">
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="cashPayment" value="local" checked class="text-green-500" /><span class="text-sm">Paga en el local</span></label>
          <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="cashPayment" value="delivery" class="text-green-500" /><span class="text-sm">Paga al delivery</span></label>
        </div>
        <div id="transferInfo" class="ml-6 bg-blue-50 p-3 rounded-lg hidden">
          <p class="text-sm font-medium text-blue-800">ALIAS para transferencia:</p>
          <p class="text-lg font-bold text-blue-900"><?php echo $businessConfig['transferAlias']; ?></p>
        </div>
      </div>
      <div class="mb-6">
        <label class="block text-sm font-medium mb-1">📝 Observaciones (opcional)</label>
        <textarea id="observations" class="w-full p-2 border rounded-lg text-sm" rows="3" placeholder="Aclaraciones adicionales..."></textarea>
      </div>
    </div>
    <div class="p-4 border-t bg-gray-50">
      <div class="space-y-2 mb-4">
        <div class="flex justify-between text-sm"><span>Subtotal:</span><span id="summarySubtotal">$0</span></div>
        <div class="flex justify-between text-sm hidden" id="summaryDelivery"><span>Envío:</span><span>$<?php echo number_format($businessConfig['deliveryFee'],0,',','.'); ?></span></div>
        <div class="flex justify-between font-bold text-lg border-t pt-2"><span>Total:</span><span id="summaryTotal">$0</span></div>
      </div>
      <button id="sendOrder" class="w-full bg-green-500 text-white p-3 rounded-lg hover:bg-green-600 font-semibold">Enviar Pedido por WhatsApp 📱</button>
    </div>
  </div>
</div>

<script>
const products = <?php echo json_encode($businessConfig['products']); ?>;
const config = <?php echo json_encode($businessConfig); ?>;
const cart = [];

function updateCartBadge() {
  const count = cart.reduce((t,i)=>t+i.quantity,0);
  document.getElementById('cartCount').textContent = count;
  document.getElementById('cartBtnWrapper').style.display = count>0?'block':'none';
}

function renderCart() {
  const container = document.getElementById('cartItems');
  container.innerHTML='';
  cart.forEach(item=>{
    const div=document.createElement('div');
    div.className='flex items-center justify-between py-2 border-b';
    div.innerHTML=`<div class='flex-1'><div class='font-medium'>${item.name}</div><div class='text-sm text-gray-500'>$${item.price.toLocaleString()} c/u</div></div><div class='flex items-center gap-2'><button class='dec w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center hover:bg-red-200'>-</button><span class='w-8 text-center font-medium'>${item.quantity}</span><button class='inc w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200'>+</button></div>`;
    div.querySelector('.dec').onclick=()=>updateQuantity(item.id,-1);
    div.querySelector('.inc').onclick=()=>updateQuantity(item.id,1);
    container.appendChild(div);
  });
  document.getElementById('cartSubtotal').textContent='$'+getSubtotal().toLocaleString();
}

function updateQuantity(id,delta){
  const idx=cart.findIndex(i=>i.id==id);
  if(idx>=0){
    cart[idx].quantity+=delta;
    if(cart[idx].quantity<=0) cart.splice(idx,1);
  }
  updateCartBadge();
  renderCart();
}

function getSubtotal(){
  return cart.reduce((t,i)=>t+i.price*i.quantity,0);
}

function addToCart(id){
  const prod=products.find(p=>p.id==id);
  if(!prod) return;
  const existing=cart.find(i=>i.id==id);
  if(existing) existing.quantity++;
  else cart.push({...prod, quantity:1});
  updateCartBadge();
}

document.querySelectorAll('.add-to-cart').forEach(btn=>{
  btn.addEventListener('click',()=>{addToCart(btn.dataset.id);});
});

document.getElementById('toggleCart').onclick=()=>{
  const modal=document.getElementById('cartModal');
  modal.style.display='flex';
  renderCart();
};

document.getElementById('closeCart').onclick=()=>{
  document.getElementById('cartModal').style.display='none';
};

document.getElementById('openCheckout').onclick=()=>{
  document.getElementById('cartModal').style.display='none';
  document.getElementById('checkoutModal').style.display='flex';
  refreshSummary();
};

document.getElementById('closeCheckout').onclick=()=>{
  document.getElementById('checkoutModal').style.display='none';
};

document.querySelectorAll('input[name="deliveryType"]').forEach(r=>{
  r.addEventListener('change',()=>{
    document.getElementById('addressWrapper').style.display=r.value==='delivery'?'block':'none';
    document.getElementById('summaryDelivery').classList.toggle('hidden',r.value!=='delivery');
    refreshSummary();
  });
});

document.querySelectorAll('input[name="paymentMethod"]').forEach(r=>{
  r.addEventListener('change',()=>{
    const cash=r.value==='cash';
    document.getElementById('cashOptions').style.display=cash?'block':'none';
    document.getElementById('transferInfo').style.display=!cash?'block':'none';
  });
});

function refreshSummary(){
  const subtotal=getSubtotal();
  const delivery=document.querySelector('input[name="deliveryType"]:checked').value==='delivery'?config.deliveryFee:0;
  document.getElementById('summarySubtotal').textContent='$'+subtotal.toLocaleString();
  document.getElementById('summaryTotal').textContent='$'+(subtotal+delivery).toLocaleString();
}

document.getElementById('sendOrder').onclick=()=>{
  const firstName=document.getElementById('firstName').value.trim();
  const lastName=document.getElementById('lastName').value.trim();
  const phone=document.getElementById('phone').value.trim();
  const deliveryType=document.querySelector('input[name="deliveryType"]:checked').value;
  const address=document.getElementById('address').value.trim();
  const paymentMethod=document.querySelector('input[name="paymentMethod"]:checked').value;
  const cashPayment=document.querySelector('input[name="cashPayment"]:checked')?document.querySelector('input[name="cashPayment"]:checked').value:'local';
  const observations=document.getElementById('observations').value.trim();
  if(!firstName||!lastName){alert('Ingresa tu nombre y apellido');return;}
  if(!phone){alert('Ingresa tu teléfono');return;}
  if(deliveryType==='delivery'&&!address){alert('Ingresa tu dirección');return;}
  let msg=`🍽️ *NUEVO PEDIDO - ${config.name}*\n\n`;
  msg+=`👤 *DATOS DEL CLIENTE*\n`;
  msg+=`• Nombre: ${firstName} ${lastName}\n`;
  msg+=`• Teléfono: ${phone}\n\n`;
  msg+=`🚚 *ENTREGA*\n`;
  if(deliveryType==='pickup'){msg+=`• Retira en el local\n\n`;}else{msg+=`• Envío a domicilio\n`;
    msg+=`• Dirección: ${address}\n`;
    msg+=`• Costo de envío: ${config.deliveryFee.toLocaleString()}\n\n`;}
  msg+=`🛍️ *PEDIDO*\n`;
  cart.forEach(i=>{msg+=`• ${i.name} x${i.quantity} - ${(i.price*i.quantity).toLocaleString()}\n`;});
  const subtotal=getSubtotal();
  const delivery=deliveryType==='delivery'?config.deliveryFee:0;
  const total=subtotal+delivery;
  msg+=`\n💰 *RESUMEN*\n`;
  msg+=`• Subtotal: ${subtotal.toLocaleString()}\n`;
  if(delivery>0) msg+=`• Envío: ${delivery.toLocaleString()}\n`;
  msg+=`• *Total: ${total.toLocaleString()}*\n\n`;
  msg+=`💳 *FORMA DE PAGO*\n`;
  if(paymentMethod==='cash'){
    if(cashPayment==='local') msg+=`• Efectivo - Paga en el local\n\n`;
    else msg+=`• Efectivo - Paga al delivery\n\n`;
  }else{
    msg+=`• Transferencia bancaria\n`;
    msg+=`• ALIAS: *${config.transferAlias}*\n`;
    msg+=`• Monto a transferir: *${total.toLocaleString()}*\n\n`;
  }
  if(observations){msg+=`📝 *OBSERVACIONES*\n${observations}\n\n`;}
  msg+='¡Gracias por tu pedido! 🙏';
  const url=`https://wa.me/${config.whatsappNumber}?text=${encodeURIComponent(msg)}`;
  window.open(url,'_blank');
  document.getElementById('checkoutModal').style.display='none';
  cart.length=0;updateCartBadge();
};
</script>
</body>
</html>
