import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
}

window.toggleTheme = () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
};

function updateCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach((element) => {
        const end = new Date(element.dataset.countdown).getTime();
        const distance = end - Date.now();

        if (distance <= 0) {
            element.textContent = element.textContent === 'Ended' ? 'Ended' : 'Closed';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        element.textContent = days > 0 ? `${days}d ${hours}h ${minutes}m` : `${hours}h ${minutes}m ${seconds}s`;
    });
}

updateCountdowns();
setInterval(updateCountdowns, 1000);

const auctionContainer = document.querySelector('[data-auction-id]');

if (auctionContainer && window.Echo) {
    const auctionId = auctionContainer.dataset.auctionId;

    window.Echo.channel(`auctions.${auctionId}`).listen('.bid.placed', (event) => {
        document.getElementById('current-bid').textContent = Number(event.amount).toFixed(2);
        const minimumNextBid = document.getElementById('minimum-next-bid');
        if (minimumNextBid) {
            minimumNextBid.textContent = Number(event.minimum_next_bid).toFixed(2);
        }

        const history = document.getElementById('bid-history');
        const row = document.createElement('div');
        row.className = 'rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 shadow-sm dark:border-emerald-800 dark:bg-emerald-950/40';
        row.innerHTML = `<div class="flex items-center justify-between gap-3"><span class="font-semibold text-slate-900 dark:text-white">${event.bidder}</span><strong class="text-emerald-600 dark:text-emerald-300">$${Number(event.amount).toFixed(2)}</strong></div><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">just now</p>`;
        history.prepend(row);
    });
}
