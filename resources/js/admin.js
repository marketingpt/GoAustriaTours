import Alpine from 'alpinejs';
import 'trix';

/** Media picker: single or multiple selection from the library (grid of thumbnails). */
Alpine.data('mediaPicker', (cfg) => ({
    multiple: !!cfg.multiple,
    items: cfg.items || [], // library: [{id, url, alt}]
    selected: cfg.selected || [], // ids, ordered
    open: false,
    get chosen() {
        return this.selected.map((id) => this.items.find((i) => i.id === id)).filter(Boolean);
    },
    toggle(id) {
        if (!this.multiple) {
            this.selected = [id];
            this.open = false;
            return;
        }
        this.selected = this.selected.includes(id) ? this.selected.filter((x) => x !== id) : [...this.selected, id];
    },
    clear() {
        this.selected = [];
    },
    move(index, dir) {
        const j = index + dir;
        if (j < 0 || j >= this.selected.length) return;
        const next = [...this.selected];
        [next[index], next[j]] = [next[j], next[index]];
        this.selected = next;
    },
    remove(id) {
        this.selected = this.selected.filter((x) => x !== id);
    },
}));

/** Repeatable rows (itinerary days, FAQs, quiz options). */
Alpine.data('rows', (initial, blank) => ({
    rows: initial.length ? initial : [],
    add() {
        this.rows.push(JSON.parse(JSON.stringify(blank)));
    },
    remove(i) {
        this.rows.splice(i, 1);
    },
    move(i, dir) {
        const j = i + dir;
        if (j < 0 || j >= this.rows.length) return;
        [this.rows[i], this.rows[j]] = [this.rows[j], this.rows[i]];
    },
}));

window.Alpine = Alpine;
Alpine.start();
