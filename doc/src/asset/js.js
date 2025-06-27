function initAnchors() {
    document.querySelector('body').addEventListener('click', event => {
        if (event.target.closest('a[href*="#"]')) {
            event.preventDefault();

            const href = event.target.getAttribute('href');
            const blockID = href.substring(1)
            document.getElementById(blockID).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            })

            history.pushState({}, null, href)
        }
    })
}

function initTabs() {
    document.querySelectorAll('.js-tabs-title').forEach(title => {
        title.addEventListener('click', event => {
            if (title.classList.contains('_active')) {
                return;
            }

            const index = title.getAttribute('data-number');
            const tab = title.closest('.js-tabs');
            tab.querySelectorAll('._active').forEach(elem => {
                elem.classList.remove('_active');
            })

            tab.querySelectorAll(`[data-number="${index}"]`).forEach(elem => {
                elem.classList.add('_active');
            })
        })
    })
}

function initAutocomplete() {
    const inp = document.querySelector('.js-search-input');
    const inpClear = document.querySelector('.js-search-input-clear');
    const sidebarSearched = document.querySelector('.js-sidebar-searched');

    inp.addEventListener('input', event => {
        const query = inp.value.trim();

        if (query.length) {
            sidebarSearched.classList.add('_active');
            inp.classList.add('_clearable');
            sidebarSearched.innerHTML = buildCntAutocomplete(event.target.value);
        } else {
            sidebarSearched.classList.remove('_active');
            inp.classList.remove('_clearable');
            sidebarSearched.innerHTML = '';
        }
    })

    inpClear.addEventListener('click', event => {
        inp.value = '';

        sidebarSearched.classList.remove('_active');
        inp.classList.remove('_clearable');
        sidebarSearched.innerHTML = '';
    })
}

function buildCntAutocomplete(query) {
    const items = methods
        .map(section => {
            if (section.titleSearch.includes(query)) {
                return section;
            }

            const child = section.items.filter(item => item.titleSearch.includes(query));
            if (child.length) {
                return {
                    ...section,
                    items: child
                }
            }

            return null
        })
        .filter(item => item !== null);

    if (!items.length) {
        return '<div class="sidebar__searched-none">Ничего не найдено</div>';
    }

    let res = '';
    res += '<ul class="sidebar__nav">';

    for (const section of items) {
        res += '<li>'
        res += `<a href="#${section.id}">${section.title}</a>`

        if (section.items.length) {
            res += '<ul>';
            for (const item of section.items) {
                res += '<li>'
                res += `<a href="#${item.id}">${item.title}</a>`
                res += '</li>'
            }
            res += '</ul>';
        }

        res += '</li>'
    }

    res += '</ul>';

    return res;
}

initAnchors();
initTabs();
initAutocomplete();
