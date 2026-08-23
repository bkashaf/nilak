import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../../public/themes/default/js/script.js';

const minPrice = document.querySelector('#price_min_range');
const maxPrice = document.querySelector('#price_max_range');
const minPriceOutput = document.querySelector('#price_min_output');
const maxPriceOutput = document.querySelector('#price_max_output');

function syncPriceRange() {
	if (!minPrice || !maxPrice) return;

	if (Number(minPrice.value) > Number(maxPrice.value)) {
		[minPrice.value, maxPrice.value] = [maxPrice.value, minPrice.value];
	}

	if (minPriceOutput) minPriceOutput.textContent = Number(minPrice.value).toLocaleString('fa-IR');
	if (maxPriceOutput) maxPriceOutput.textContent = Number(maxPrice.value).toLocaleString('fa-IR');
}

minPrice?.addEventListener('input', syncPriceRange);
maxPrice?.addEventListener('input', syncPriceRange);
syncPriceRange();
