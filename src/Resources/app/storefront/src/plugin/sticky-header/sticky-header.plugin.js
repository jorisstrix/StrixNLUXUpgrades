import Plugin from 'src/plugin-system/plugin.class';

export default class StickyHeaderNavPlugin extends Plugin {
    init() {
        this.target = this.el;

        const logoCol = this.target.querySelector('.header-logo-col');
        if (logoCol && !logoCol.hasAttribute('data-sticky-hide-below')) {
            logoCol.setAttribute('data-sticky-hide-below', '992');
        }

        this.exclusions = Array.from(
            this.target.querySelectorAll('[data-sticky-hide-below]')
        ).map((el) => ({
            el,
            threshold: parseInt(
                el.getAttribute('data-sticky-hide-below') || '0',
                10
            ),
        }));

        this.spacer = document.createElement('div');
        this.spacer.setAttribute('aria-hidden', 'true');
        this.spacer.className = 'strix-sticky-spacer';
        this.target.parentNode.insertBefore(
            this.spacer,
            this.target.nextSibling
        );

        this._bg = document.createElement('div');
        this._bg.className = 'strix-sticky-bg';
        document.body.appendChild(this._bg);
        this._onScroll = this._onScroll.bind(this);
        this._onResize = this._onResize.bind(this);

        this._measure();
        this._apply(true);

        this._ticking = false;
        window.addEventListener('scroll', this._onScroll, { passive: true });
        window.addEventListener('resize', this._onResize, { passive: true });

        setTimeout(() => {
            this._measure();
            this._apply(true);
        }, 100);
    }

    destroy() {
        window.removeEventListener('scroll', this._onScroll);
        window.removeEventListener('resize', this._onResize);
        this._unsetFixed();
        if (this._bg?.parentNode) this._bg.parentNode.removeChild(this._bg);
        if (this.spacer?.parentNode)
            this.spacer.parentNode.removeChild(this.spacer);
        super.destroy();
    }

    _measure() {
        const wasFixed = this._isFixed();
        if (wasFixed) this._unsetFixed();
        this._setExclusionsVisible(true);

        const rect = this.target.getBoundingClientRect();
        this.triggerTop = rect.top + window.scrollY;
        this.leftWhenStatic = rect.left;
        this.width = this.target.offsetWidth;
        this.height = this.target.offsetHeight;

        if (wasFixed) this._setFixed();
    }

    _onScroll() {
        if (this._ticking) return;
        this._ticking = true;
        requestAnimationFrame(() => {
            this._apply(false);
            this._ticking = false;
        });
    }

    _onResize() {
        this._measure();
        this._apply(true);
    }

    _apply(force) {
        const shouldFix = window.scrollY >= this.triggerTop;

        if (shouldFix && !this._isFixed()) {
            this._setFixed();
        } else if (!shouldFix && this._isFixed()) {
            this._unsetFixed();
        } else if (shouldFix && force) {
            this._applyExclusions();
            this._syncSpacer();
            this._applyVars();
        }
    }

    _setFixed() {
        this._fixing = true;

        this.target.classList.add('strix-sticky-fixed');
        this._bg.classList.add('is-visible');

        this._applyExclusions();
        this._applyVars();
        this._syncSpacer();

        this._fixing = false;
    }

    _unsetFixed() {
        this.target.classList.remove('strix-sticky-fixed');
        this.spacer.classList.remove('is-visible');
        this.target.style.removeProperty('--strix-width');
        this.target.style.removeProperty('--strix-left');
        this.spacer.style.removeProperty('--strix-height');
        this._bg.classList.remove('is-visible');
        this._bg.style.removeProperty('--strix-height');
        this._setExclusionsVisible(true);
    }

    _applyExclusions() {
        const w = window.innerWidth;
        const isFixedOrFixing = this._isFixed() || this._fixing;
        this.exclusions.forEach((x) => {
            const shouldHide = isFixedOrFixing && w < x.threshold;
            if (shouldHide) x.el.classList.add('d-none');
            else x.el.classList.remove('d-none');
        });
    }

    _setExclusionsVisible(visible) {
        this.exclusions.forEach((x) => {
            if (visible) x.el.classList.remove('d-none');
            else x.el.classList.add('d-none');
        });
    }

    _syncSpacer() {
        this.height = this.target.offsetHeight;
        const h = `${this.height}px`;
        this.spacer.style.setProperty('--strix-height', h);
        this._bg.style.setProperty('--strix-height', h);
        this.spacer.classList.add('is-visible');
    }

    _applyVars() {
        this.target.style.setProperty('--strix-width', `${this.width}px`);
        this.target.style.setProperty(
            '--strix-left',
            `${this.leftWhenStatic}px`
        );
    }

    _isFixed() {
        return this.target.classList.contains('strix-sticky-fixed');
    }
}
