<?php
require_once __DIR__ . '/includes/init.php';
requireLogin();

$user = currentUser($pdo);
$error = '';
$success = false;

$pageTitle = 'Checkout — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-10" id="checkout-page">
  <div id="checkout-empty" class="text-center py-20 text-gray-500 hidden">
    Your bag is empty. <a href="<?= url('products.php') ?>" class="underline">Shop now</a>
  </div>

  <form id="checkout-form" method="post" action="<?= url('checkout-process.php') ?>" class="hidden">
    <div class="grid lg:grid-cols-[1fr_360px] gap-10">
      <div>
        <h1 class="font-serif text-3xl mb-6">Checkout</h1>

        <section class="mb-8">
          <h2 class="font-medium text-lg mb-4">Delivery Details</h2>
          <div class="grid grid-cols-2 gap-3">
            <input required name="name" placeholder="Full name" class="col-span-2 border rounded px-4 py-3">
            <input required type="email" name="email" value="<?= e($user['email']) ?>" placeholder="Email" class="col-span-2 border rounded px-4 py-3">
            <input required type="tel" name="phone" placeholder="Phone number" class="col-span-2 border rounded px-4 py-3">
            <input required name="address" placeholder="Address / Neighbourhood" class="col-span-2 border rounded px-4 py-3">
            <input name="city" value="Kigali" placeholder="City" class="border rounded px-4 py-3">
            <input name="country" value="Rwanda" placeholder="Country" class="border rounded px-4 py-3">
          </div>
        </section>

        <section>
          <h2 class="font-medium text-lg mb-4">Payment Method</h2>
          <div class="bg-[#fff9e6] border border-[#FFCC00] rounded-lg p-5">
            <div class="flex items-center gap-2 mb-3">
              <span class="bg-[#FFCC00] text-black text-xs font-bold px-2 py-1 rounded">MTN MoMo</span>
              <span class="text-sm text-gray-600">Simulated payment</span>
            </div>
            <label class="text-sm font-medium">MTN Mobile Money Number</label>
            <input required name="momo_number" placeholder="078 XXX XXXX" class="w-full border rounded px-4 py-3 mt-1 bg-white">
            <p class="text-xs text-gray-500 mt-2">You'll receive a prompt on your phone to approve the payment.</p>
          </div>
        </section>
      </div>

      <aside class="bg-[#f5f5f5] rounded-lg p-6 h-fit">
        <h2 class="font-serif text-xl mb-4">Order Summary</h2>
        <div id="checkout-items" class="space-y-3 mb-4"></div>
        <div class="border-t pt-4 space-y-2 text-sm">
          <div class="flex justify-between"><span>Subtotal</span><span id="checkout-subtotal">0 RWF</span></div>
          <div class="flex justify-between"><span>Shipping</span><span class="text-[#D4AF37] font-medium">Free</span></div>
          <div class="flex justify-between font-semibold text-lg border-t pt-2"><span>Total</span><span id="checkout-total">0 RWF</span></div>
        </div>
        <input type="hidden" name="cart_json" id="cart-json">
        <button type="submit" class="w-full mt-4 bg-black text-white py-3.5 rounded font-medium hover:bg-[#FFCC00] hover:text-black transition-colors">
          Place Order
        </button>
      </aside>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const items = Cart.getItems();
  const empty = document.getElementById('checkout-empty');
  const form = document.getElementById('checkout-form');
  if (!items.length) {
    empty.classList.remove('hidden');
    return;
  }
  form.classList.remove('hidden');
  document.getElementById('cart-json').value = JSON.stringify(items);
  const subtotal = items.reduce((s, i) => s + i.price * i.quantity, 0);
  const fmt = (n) => new Intl.NumberFormat('en-RW', { maximumFractionDigits: 0 }).format(n) + ' RWF';
  document.getElementById('checkout-subtotal').textContent = fmt(subtotal);
  document.getElementById('checkout-total').textContent = fmt(subtotal);
  document.getElementById('checkout-items').innerHTML = items.map((i) => `
    <div class="flex gap-3 text-sm">
      <img src="${i.image || ''}" class="h-14 w-12 object-cover rounded bg-gray-200" alt="">
      <div class="flex-1">
        <p class="font-medium leading-tight">${i.name}</p>
        <p class="text-gray-500">${i.variant_title ? i.variant_title + ' · ' : ''}Qty ${i.quantity}</p>
      </div>
      <span>${fmt(i.price * i.quantity)}</span>
    </div>`).join('');
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
