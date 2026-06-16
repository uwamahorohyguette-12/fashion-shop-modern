</main>
<footer class="bg-black text-white mt-20">
  <div class="max-w-7xl mx-auto px-4 py-14 grid grid-cols-1 md:grid-cols-4 gap-10">
    <div>
      <h3 class="font-serif text-2xl">Kigali<span class="text-[#D4AF37]">Threads</span></h3>
      <p class="text-gray-400 text-sm mt-3 leading-relaxed">
        Premium fashion crafted for the modern Rwandan. Style that moves with you, from Kigali to the world.
      </p>
    </div>
    <div>
      <h4 class="text-sm font-semibold tracking-widest mb-4 text-[#D4AF37]">SHOP</h4>
      <ul class="space-y-2 text-sm text-gray-400">
        <li><a href="<?= url('collection.php?handle=men') ?>" class="hover:text-white">Men</a></li>
        <li><a href="<?= url('collection.php?handle=women') ?>" class="hover:text-white">Women</a></li>
        <li><a href="<?= url('collection.php?handle=kids') ?>" class="hover:text-white">Kids</a></li>
        <li><a href="<?= url('collection.php?handle=shoes') ?>" class="hover:text-white">Shoes</a></li>
        <li><a href="<?= url('collection.php?handle=accessories') ?>" class="hover:text-white">Accessories</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-sm font-semibold tracking-widest mb-4 text-[#D4AF37]">HELP</h4>
      <ul class="space-y-2 text-sm text-gray-400">
        <li><a href="<?= url('products.php') ?>" class="hover:text-white">Shop All</a></li>
        <li><a href="<?= url('order-tracking.php') ?>" class="hover:text-white">Track Order</a></li>
        <li><a href="#" class="hover:text-white">Shipping &amp; Delivery</a></li>
        <li><a href="#" class="hover:text-white">Returns</a></li>
        <li><a href="#" class="hover:text-white">Contact Us</a></li>
      </ul>
    </div>
    <div>
      <h4 class="text-sm font-semibold tracking-widest mb-4 text-[#D4AF37]">JOIN THE LIST</h4>
      <p class="text-sm text-gray-400">Subscribe for updates on new arrivals and exclusive offers.</p>
    </div>
  </div>
  <div class="border-t border-white/10 py-5 text-center text-xs text-gray-500">
    &copy; <?= date('Y') ?> KigaliThreads. All rights reserved. Made in Rwanda.
  </div>
</footer>
<script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
<script src="<?= asset('js/cart.js') ?>"></script>
<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
