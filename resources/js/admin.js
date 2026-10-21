document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    // Mobile sidebar
    document.querySelectorAll('[data-side-toggle]').forEach((el) =>
        el.addEventListener('click', () => body.classList.toggle('side-open'))
    );

    // Slug "বদলান"
    document.querySelectorAll('[data-slug]').forEach((wrap) => {
        const text = wrap.querySelector('[data-slug-text]');
        const input = wrap.querySelector('[data-slug-input]');
        const btn = wrap.querySelector('[data-slug-btn]');
        btn.addEventListener('click', () => {
            const editing = input.classList.toggle('d-none') === false;
            text.classList.toggle('d-none', editing);
            btn.textContent = editing ? 'ঠিক আছে' : 'বদলান';
            if (!editing && input.value) text.textContent = input.value;
        });
    });

    // প্রকাশের সময় (এখনই / schedule)
    document.querySelectorAll('[data-sched]').forEach((wrap) => {
        const now = wrap.querySelector('[data-sched-now]');
        const box = wrap.querySelector('[data-sched-box]');
        const input = wrap.querySelector('input[type=datetime-local]');
        const btn = wrap.querySelector('[data-sched-btn]');
        const apply = (on) => {
            now.classList.toggle('d-none', on);
            box.classList.toggle('d-none', !on);
            input.disabled = !on;
            btn.textContent = on ? '← এখনই প্রকাশ করুন' : '🕒 সময় ঠিক করুন';
            wrap.dataset.on = on ? '1' : '0';
        };
        apply(wrap.dataset.on === '1');
        btn.addEventListener('click', () => apply(wrap.dataset.on !== '1'));
    });

    // কভার ছবি preview
    document.querySelectorAll('[data-cover]').forEach((wrap) => {
        const img = wrap.querySelector('[data-cover-img]');
        const label = wrap.querySelector('[data-cover-label]');
        wrap.querySelector('input[type=file]').addEventListener('change', (e) => {
            const f = e.target.files[0];
            if (!f) return;
            img.src = URL.createObjectURL(f);
            img.classList.remove('d-none');
            label.textContent = 'ছবি বদলান';
        });
    });
});