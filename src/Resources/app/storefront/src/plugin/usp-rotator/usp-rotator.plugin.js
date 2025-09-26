// custom/plugins/StrixNLUxUpgrades/src/Resources/app/storefront/src/plugin/usp-rotator/usp-rotator.plugin.js
import Plugin from "src/plugin-system/plugin.class";

export default class UspRotatorPlugin extends Plugin {
    init() {
        this.items = Array.from(this.el.querySelectorAll("[data-usp-item]"));
        if (!this.items.length) return;

        this.interval = Math.max(
            200,
            parseInt(this.el.dataset.interval || "4000", 10)
        );
        this.duration = Math.max(
            120,
            parseInt(this.el.dataset.duration || "1200", 10)
        );
        this.bp = parseInt(this.el.dataset.lgBreakpoint || "768", 10);
        this.reduce = window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;

        this.index = 1;
        this.animRaf = null;
        this.state = "idle";
        this.t0 = 0;
        this.pause = false;

        this._stepFn = this._step.bind(this); // <— bind once (fix)
        this._onResize = () => this._applyMode();
        this._onHover = (e) => {
            if (!this._isMobile()) return;
            this.pause = e.type === "mouseenter";
            if (!this.pause) this._kick();
        };

        this._build();
        this._applyMode();

        window.addEventListener("resize", this._onResize, { passive: true });
        this.el.addEventListener("mouseenter", this._onHover);
        this.el.addEventListener("mouseleave", this._onHover);
    }

    destroy() {
        this._stop();
        window.removeEventListener("resize", this._onResize);
        this.el.removeEventListener("mouseenter", this._onHover);
        this.el.removeEventListener("mouseleave", this._onHover);
        super.destroy();
    }

    _build() {
        Object.assign(this.el.style, {
            overflow: "hidden",
            position: "relative",
        });
        this.el.setAttribute("role", "region");
        this.el.setAttribute("aria-roledescription", "carousel");
        this.el.setAttribute("aria-live", "polite");

        const first = this.items[0];
        const last = this.items[this.items.length - 1];
        const clone = (n) => {
            const c = n.cloneNode(true);
            c.dataset.clone = "true";
            return c;
        };
        this.slides = [clone(last), ...this.items, clone(first)];

        this.track = document.createElement("div");
        Object.assign(this.track.style, {
            display: "flex",
            width: "100%",
            willChange: "transform",
        });
        this.el.appendChild(this.track);

        this.slides.forEach((li, i) => {
            li.classList.remove("flex-fill");
            li.setAttribute("role", "group");
            li.setAttribute(
                "aria-label",
                `Slide ${Math.min(i, this.items.length)}/${this.items.length}`
            );
            this.track.appendChild(li);
        });
    }

    _isMobile() {
        return window.innerWidth < this.bp;
    }

    _applyMode() {
        if (this._isMobile()) {
            this.slides.forEach((s) => {
                Object.assign(s.style, {
                    flex: "0 0 100%",
                    display: "block",
                    textAlign: "center",
                });
                s.hidden = false;
            });
            this.el.style.minHeight =
                Math.max(
                    ...this.slides.map((s) => s.getBoundingClientRect().height),
                    24
                ) + "px";
            this._translate(-this._vw() * this.index);
            this._kick();
        } else {
            this._stop();
            this.el.style.minHeight = "";
            this._translate(0);
            this.slides.forEach((s) => {
                const clone = s.dataset.clone === "true";
                s.hidden = clone;
                Object.assign(s.style, {
                    display: clone ? "none" : "block",
                    flex: clone ? "" : "1 1 0%",
                    textAlign: "center",
                });
            });
        }
    }

    _kick() {
        if (
            this.reduce ||
            !this._isMobile() ||
            this.pause ||
            this.slides.length <= 2 ||
            this.animRaf
        )
            return;
        this.state = "hold";
        this.t0 = performance.now();
        this.animRaf = requestAnimationFrame(this._stepFn); // <— use bound fn
    }

    _stop() {
        if (this.animRaf) cancelAnimationFrame(this.animRaf);
        this.animRaf = null;
        this.state = "idle";
    }

    _step(now) {
        // <— normal method (no class field)
        if (this.pause || !this._isMobile()) {
            this.animRaf = null;
            return;
        }

        if (this.state === "hold") {
            if (now - this.t0 >= this.interval) {
                this.state = "slide";
                this.t0 = now;
                this.fromPx = -this._vw() * this.index;
                this.toPx = -this._vw() * (this.index + 1);
            }
        } else {
            // slide
            const t = Math.min(1, (now - this.t0) / this.duration);
            const eased = 1 - Math.pow(1 - t, 3);
            this._translate(this.fromPx + (this.toPx - this.fromPx) * eased);

            if (t === 1) {
                this.index += 1;
                const n = this.items.length;
                if (this.index === n + 1) {
                    // wrapped onto cloneFirst
                    this.index = 1;
                    this._translate(-this._vw() * this.index); // snap same frame
                }
                this.state = "hold";
                this.t0 = now;
            }
        }
        this.animRaf = requestAnimationFrame(this._stepFn); // <— reuse bound fn
    }

    _vw() {
        return this.el.clientWidth;
    }
    _translate(px) {
        this.track.style.transform = `translate3d(${px}px,0,0)`;
    }
}
