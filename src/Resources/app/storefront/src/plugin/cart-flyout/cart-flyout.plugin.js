export default class CartFlyoutPlugin extends window.PluginBaseClass {
    static options = {
        removeProductTriggerSelector: '.js-offcanvas-cart-remove-product',
        removeButtonSelector: '.line-item-remove-button',
        trashButtonSelector: 'button[formaction*="/checkout/line-item/delete/"]',
        quantitySelectSelector: '.js-offcanvas-cart-change-quantity',
        quantityInputSelector: '.js-offcanvas-cart-change-quantity-number',
        cartItemSelector: '.js-cart-item',
        cartItemsContainerSelector: '.offcanvas-cart-items'
    };

    init() {
        this._registerEvents();
    }

    _registerEvents() {
        const forms = this.el.querySelectorAll(this.options.removeProductTriggerSelector);
        
        forms.forEach(form => {
            form.addEventListener('submit', this._onRemoveProductFromCart.bind(this));
        });

        const removeButtons = this.el.querySelectorAll(this.options.removeButtonSelector);
        const trashButtons = this.el.querySelectorAll(this.options.trashButtonSelector);
        const quantitySelects = this.el.querySelectorAll(this.options.quantitySelectSelector);
        const quantityInputs = this.el.querySelectorAll(this.options.quantityInputSelector);
        
        removeButtons.forEach(button => {
            button.addEventListener('click', this._onRemoveButtonClick.bind(this));
        });

        trashButtons.forEach(button => {
            button.addEventListener('click', this._onTrashButtonClick.bind(this));
        });

        quantitySelects.forEach(select => {
            select.addEventListener('change', this._onQuantityChange.bind(this));
        });

        quantityInputs.forEach(input => {
            input.addEventListener('change', this._onQuantityChange.bind(this));
        });
    }

    _onRemoveProductFromCart(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const form = event.target;
        this._submitFormAjax(form);
    }

    _onRemoveButtonClick(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const button = event.currentTarget;
        const form = button.closest('form');
        this._submitFormAjax(form);
    }

    _onTrashButtonClick(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const button = event.currentTarget;
        const form = button.closest('form');
        const formAction = button.getAttribute('formaction');
        
        this._submitFormAjax(form, formAction);
    }

    _onQuantityChange(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const element = event.currentTarget;
        const form = element.closest('form');
        
        this._submitFormAjax(form);
    }

    async _submitFormAjax(form, customAction = null) {
        const formData = new FormData(form);
        formData.append('redirectTo', 'frontend.cart.offcanvas');
        const url = customAction || form.action;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const responseText = await response.text();
                
                // Find the offcanvas element and update its content
                const offcanvasElement = document.querySelector('.offcanvas');
                if (offcanvasElement) {
                    offcanvasElement.innerHTML = responseText;
                    
                    // Re-initialize plugins for the new content
                    window.PluginManager.initializePlugins();
                    
                    // Update cart widgets
                    const cartWidgets = window.PluginManager.getPluginInstances('CartWidget');
                    cartWidgets.forEach(widget => widget.fetch());
                }
            } else {
                form.submit();
            }
        } catch (error) {
            form.submit();
        }
    }
}
