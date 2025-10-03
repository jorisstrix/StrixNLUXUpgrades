import Plugin from "src/plugin-system/plugin.class";

export default class StrixStyleguidePlugin extends Plugin {
    init() {
        const nodes = this.el.querySelectorAll("[data-token]");
        if (!nodes.length) return;
        const cs = getComputedStyle(document.documentElement);
        nodes.forEach((node) => {
            const token = node.getAttribute("data-token");
            const val = cs.getPropertyValue(token)?.trim();
            node.textContent = val || "(not set)";
        });
    }
}
