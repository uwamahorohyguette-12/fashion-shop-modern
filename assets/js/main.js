document.addEventListener('DOMContentLoaded', () => {
  const searchToggle = document.getElementById('search-toggle');
  const searchBar = document.getElementById('search-bar');
  const mobileOpen = document.getElementById('mobile-menu-open');
  const mobileClose = document.getElementById('mobile-menu-close');
  const mobileMenu = document.getElementById('mobile-menu');
  const cartOpen = document.getElementById('cart-open');
  const cartClose = document.getElementById('cart-close');
  const cartBackdrop = document.getElementById('cart-backdrop');
  const cartDrawer = document.getElementById('cart-drawer');

  searchToggle?.addEventListener('click', () => {
    searchBar?.classList.toggle('hidden');
    searchBar?.querySelector('input')?.focus();
  });

  mobileOpen?.addEventListener('click', () => mobileMenu?.classList.remove('hidden'));
  mobileClose?.addEventListener('click', () => mobileMenu?.classList.add('hidden'));

  const closeCart = () => cartDrawer?.classList.add('hidden');
  cartOpen?.addEventListener('click', () => cartDrawer?.classList.remove('hidden'));
  cartClose?.addEventListener('click', closeCart);
  cartBackdrop?.addEventListener('click', closeCart);
});
