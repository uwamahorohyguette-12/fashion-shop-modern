(function () {
  const STORAGE_KEY = 'ecom_cart';

  function loadCart() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch {
      return [];
    }
  }

  function saveCart(items) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    renderCartUI();
  }

  function formatRWF(amount) {
    return new Intl.NumberFormat('en-RW', { maximumFractionDigits: 0 }).format(amount || 0) + ' RWF';
  }

  function getSubtotal(items) {
    return items.reduce((sum, item) => sum + item.price * item.quantity, 0);
  }

  function getCount(items) {
    return items.reduce((sum, item) => sum + item.quantity, 0);
  }

  window.Cart = {
    getItems: loadCart,

    addToCart(item, qty = 1) {
      const items = loadCart();
      const idx = items.findIndex(
        (i) => i.product_id === item.product_id && i.variant_id === item.variant_id
      );
      if (idx >= 0) {
        items[idx].quantity += qty;
      } else {
        items.push({ ...item, quantity: qty });
      }
      saveCart(items);
      const drawer = document.getElementById('cart-drawer');
      if (drawer) drawer.classList.remove('hidden');
    },

    removeFromCart(productId, variantId) {
      const items = loadCart().filter(
        (i) => !(i.product_id === productId && (i.variant_id || '') === (variantId || ''))
      );
      saveCart(items);
    },

    updateQuantity(productId, variantId, qty) {
      const items = loadCart()
        .map((i) =>
          i.product_id === productId && (i.variant_id || '') === (variantId || '')
            ? { ...i, quantity: Math.max(1, qty) }
            : i
        )
        .filter((i) => i.quantity > 0);
      saveCart(items);
    },

    clearCart() {
      saveCart([]);
    },
  };

  function renderCartUI() {
    const items = loadCart();
    const count = getCount(items);
    const subtotal = getSubtotal(items);

    const countEl = document.getElementById('cart-count');
    if (countEl) {
      countEl.textContent = count;
      countEl.classList.toggle('hidden', count === 0);
    }

    const subtotalEl = document.getElementById('cart-drawer-subtotal');
    if (subtotalEl) subtotalEl.textContent = formatRWF(subtotal);

    const drawerItems = document.getElementById('cart-drawer-items');
    if (drawerItems) {
      if (items.length === 0) {
        drawerItems.innerHTML = '<p class="text-gray-500 text-sm text-center py-8">Your bag is empty.</p>';
      } else {
        drawerItems.innerHTML = items
          .map(
            (item) => `
          <div class="flex gap-3 text-sm">
            <img src="${item.image || ''}" alt="" class="h-16 w-14 object-cover rounded bg-gray-100">
            <div class="flex-1">
              <p class="font-medium">${item.name}</p>
              <p class="text-gray-500 text-xs">${item.variant_title ? item.variant_title + ' · ' : ''}Qty ${item.quantity}</p>
              <p class="font-semibold mt-1">${formatRWF(item.price * item.quantity)}</p>
            </div>
          </div>`
          )
          .join('');
      }
    }

    document.querySelectorAll('[data-cart-page]').forEach((root) => {
      const list = root.querySelector('[data-cart-list]');
      const subtotalTarget = root.querySelector('[data-cart-subtotal]');
      const totalTarget = root.querySelector('[data-cart-total]');
      const emptyState = root.querySelector('[data-cart-empty]');
      const content = root.querySelector('[data-cart-content]');

      if (!list) return;

      if (items.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (content) content.classList.add('hidden');
      } else {
        if (emptyState) emptyState.classList.add('hidden');
        if (content) content.classList.remove('hidden');
        list.innerHTML = items
          .map(
            (item) => `
          <div class="flex gap-4 border-b pb-5" data-line="${item.product_id}-${item.variant_id || ''}">
            <img src="${item.image || ''}" alt="" class="h-32 w-28 object-cover rounded bg-gray-100">
            <div class="flex-1">
              <div class="flex justify-between">
                <div>
                  <h3 class="font-medium">${item.name}</h3>
                  ${item.variant_title ? `<p class="text-sm text-gray-500">Size: ${item.variant_title}</p>` : ''}
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 cart-remove" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}">✕</button>
              </div>
              <div class="flex items-center justify-between mt-4">
                <div class="flex items-center border rounded">
                  <button type="button" class="p-2 cart-minus" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}">−</button>
                  <span class="px-3 text-sm">${item.quantity}</span>
                  <button type="button" class="p-2 cart-plus" data-product-id="${item.product_id}" data-variant-id="${item.variant_id || ''}">+</button>
                </div>
                <span class="font-semibold">${formatRWF(item.price * item.quantity)}</span>
              </div>
            </div>
          </div>`
          )
          .join('');
      }

      if (subtotalTarget) subtotalTarget.textContent = formatRWF(subtotal);
      if (totalTarget) totalTarget.textContent = formatRWF(subtotal);
    });
  }

  document.addEventListener('click', (e) => {
    const quickAdd = e.target.closest('.quick-add-btn');
    if (quickAdd) {
      e.preventDefault();
      e.stopPropagation();
      const data = JSON.parse(quickAdd.dataset.product || '{}');
      const card = quickAdd.closest('.product-card');
      if (card && card.dataset.hasVariants === '1') {
        window.location.href = (window.BASE_URL || '') + '/product.php?handle=' + encodeURIComponent(data.handle);
        return;
      }
      Cart.addToCart({
        product_id: data.product_id,
        name: data.name,
        sku: data.sku,
        price: data.price,
        image: data.image,
      });
      return;
    }

    const removeBtn = e.target.closest('.cart-remove');
    if (removeBtn) {
      Cart.removeFromCart(removeBtn.dataset.productId, removeBtn.dataset.variantId || undefined);
      return;
    }

    const minusBtn = e.target.closest('.cart-minus');
    if (minusBtn) {
      const items = loadCart();
      const item = items.find(
        (i) => i.product_id === minusBtn.dataset.productId && (i.variant_id || '') === (minusBtn.dataset.variantId || '')
      );
      if (item) Cart.updateQuantity(item.product_id, item.variant_id, item.quantity - 1);
      return;
    }

    const plusBtn = e.target.closest('.cart-plus');
    if (plusBtn) {
      const items = loadCart();
      const item = items.find(
        (i) => i.product_id === plusBtn.dataset.productId && (i.variant_id || '') === (plusBtn.dataset.variantId || '')
      );
      if (item) Cart.updateQuantity(item.product_id, item.variant_id, item.quantity + 1);
    }
  });

  document.addEventListener('DOMContentLoaded', renderCartUI);
})();
