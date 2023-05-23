const $menuTrigerButtons = document.querySelectorAll('#app_menu_trigger');
const $menu = document.getElementById('app_menu');

$menuTrigerButtons.forEach((el) => el.addEventListener('click', () => $menu.classList.toggle('active')));