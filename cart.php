<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Shopping Bag — KigaliThreads';
require __DIR__ . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto px-4 py-10" data-cart-page>
  <h1 class="font-serif text-4xl mb-8">Shopping Bag</h1>

  <div data-cart-empty class="text-center py-20 hidden">
    <p class="text-gray-500 mb-6">Your bag is empty.</p>
    <a href="<?= url('products.php') ?>" class="bg-black text-white px-8 py-3 rounded inline-block hover:bg-[#D4AF37] hover:text-black transition-colors">Start Shopping</a>
  </div>

  <div data-cart-content class="hidden">
    <div class="grid lg:grid-cols-[1fr_340px] gap-10">
      <div class="space-y-5" data-cart-list></div>
      <div class="bg-[#f5f5f5] rounded-lg p-6 h-fit">
        <h2 class="font-serif text-xl mb-4">Order Summary</h2>
        <div class="flex justify-between text-sm mb-2"><span>Subtotal</span><span data-cart-subtotal>0 RWF</span></div>
        <div class="flex justify-between text-sm mb-4 text-gray-500"><span>Shipping</span><span class="text-[#D4AF37] font-medium">Free</span></div>
        <div class="flex justify-between font-semibold text-lg border-t pt-4"><span>Total</span><span data-cart-total>0 RWF</span></div>
        <a href="<?= url('checkout.php') ?>" class="block text-center bg-black text-white py-3 rounded mt-6 hover:bg-[#D4AF37] hover:text-black transition-colors">Proceed to Checkout</a>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
